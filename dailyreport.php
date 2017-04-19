<?php
/**
 * Created by aaron <2590419211@qq.com>.
 * User: user
 * Date: 2016/12/2
 * Time: 16:07
 */

/**
 * 载入响应配置
 */
include  'config.php' ;
include  'mail.php' ;
###
/**
 * 使用CURL库读取指定地址信息
 * @param string $url 要读取的URL地址
 * @return string 地址內容 失败时为FALSE
 */
function getStockInfo($url, $timeout = 3)
{
    $ch	= curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_DNS_USE_GLOBAL_CACHE, TRUE);
    curl_setopt($ch, CURLOPT_DNS_CACHE_TIMEOUT, 300);
    // curl_setopt($ch, CURLOPT_HEADER, 1);
    // curl_setopt($ch, CURLOPT_VERBOSE, 1);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
    curl_setopt($ch, CURLOPT_FAILONERROR, TRUE);
    @curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
    curl_setopt($ch, CURLOPT_MAXREDIRS, 5);


    if (defined('CURLOPT_CONNECTTIMEOUT_MS')) {
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT_MS, 300);
        curl_setopt($ch, CURLOPT_NOSIGNAL, 1);
    } else {
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 1);
    }

    if (defined('CURLOPT_TIMEOUT_MS')) {
        curl_setopt($ch, CURLOPT_TIMEOUT_MS, $timeout * 1000);
    } else {
        curl_setopt($ch, CURLOPT_TIMEOUT, ceil($timeout));
    }

    curl_setopt ($ch, CURLOPT_SSL_VERIFYPEER, 0);
    curl_setopt ($ch, CURLOPT_SSL_VERIFYHOST, 0);

    curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 6.1; WOW64) AppleWebKit/535.11 (KHTML, like Gecko) Chrome/17.0.963.83 Safari/535.11');

    $header = array();
    $header[] = "Accept-Language: zh-CN,zh;q=0.8,en;q=0.6";
    $header[] = "Accept-Charset: GBK,utf-8;q=0.7,*;q=0.3";
    curl_setopt($ch, CURLOPT_HTTPHEADER, $header);

    $response	= curl_exec($ch);
    if ($response === false)
    {
        $error = curl_error($ch);
        var_dump($error);
    }
    @curl_close($ch);
    return $response;
}

function formatData($_info,$code_key){
    $pattern  =  '/\(.*\)/' ;
    preg_match ( $pattern ,  $_info ,  $matches);
    $matches = $matches[0];
    $matches = ltrim($matches,"(");
    $matches = rtrim($matches,")");
    $matches = json_decode($matches,true);
    $mdata = $matches;
    $mdata = $mdata[$code_key];
    if(empty($mdata) || empty($mdata["data"])){
        echo "STOCK INFO EMPTY";
        exit;
    }
    /**
     *
     * 拉取这天每一分钟的数据
     */
    $data_info = explode(";",$mdata["data"]);
    if(empty($data_info) || !is_array($data_info)){
        echo "STOCK DATA INFO EMPTY";
        exit;
    }
    $d = array();
    foreach($data_info as $di){
        $tmp = array();
        $di_arr = explode(",",$di);
        $tmp['current_time'] = $di_arr[0];
        $tmp['current_price'] = $di_arr[1];
        $tmp['current_avg_price'] = $di_arr[3];
        $d[] = $tmp;
    }
    return $d;
}


//周六日和非交易时间不提示
if(date('w')==6 || date('w') == 0){
    echo "周六日不提示";
    exit;
}
//我的持仓
$pdo = new PDO(DSN, DB_USER, DB_PASSWD);
$sth  =  $pdo -> prepare ( 'select * from my_stock' );
$sth -> execute ();
$my_stock  =  $sth -> fetchAll (PDO::FETCH_ASSOC);

//判断是否存在比预期的低 加仓提醒
$my_stock = empty($my_stock) || !is_array($my_stock) ? array() : $my_stock;
$mail_t = "";
$ismail = true;
foreach($my_stock as $ms){
    $code_key = "hs_".$ms['stock_code'];
    $url = STOCK_URL.$code_key."/0930.js";
    $info = getStockInfo($url);
    $data = formatData($info,$code_key);
    $length = count($data);
    $current_pric = empty($data[$length-1]['current_price']) ? 0 : $data[$length-1]['current_price'];
    $r = ($current_pric/$ms['price']-1)*100;
    $r1 = $r>0 ? true : false;
    $ar = sprintf("%.2f",abs($r))."%";
    if($r1){
        //表示盈利
        $mail_t.="<tr><td>".$ms['stock_code']."</td><td>".$ms['stock_name']."</td><td>".$current_pric."</td><td><font color='red'>".$ar."</font></td></tr>";
    }else{
        //表示亏损
        $mail_t.="<tr><td>".$ms['stock_code']."</td><td>".$ms['stock_name']."</td><td>".$current_pric."</td><td><font color='green'>".$ar."</font></td></tr>";
    }
}
/**************************** 开始发送邮件 ***********************************/
$mail_text = <<<EOF
<table border="1">
<caption>股票监测提醒</caption>
  <tr>
    <th>股票代码</th>
    <th>股票名称</th>
    <th>当前价格</th>
    <th>当前盈亏</th>
  </tr>
  $mail_t
</table>
EOF;
if($ismail === true){
    $mail_text = "<p>".$mail_text."</p>";
    $mail = new MySendMail();
    $mail->setServer(SMTP_HOST, MAIL_NAME, MAIL_PASSWD);
    $mail->setFrom(MAIL_NAME);
    $mail->setReceiver(RECEIVER_MAIL);
    $mail->setMailInfo("每日结算报告", $mail_text);
    $mail->sendMail();
}
echo "success";