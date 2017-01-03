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



//我的自选股
$pdo = new PDO(DSN, DB_USER, DB_PASSWD);
$sth  =  $pdo -> prepare ( 'select * from select_stock where uid = ?' );
$sth -> execute (array(1));
$my_stock  =  $sth -> fetchAll (PDO::FETCH_ASSOC);

//判断是否存在比预期的低 加仓提醒
$my_stock = empty($my_stock) || !is_array($my_stock) ? array() : $my_stock;
$mail_t = "";
$ismail = false;
foreach($my_stock as $ms){
    $cold = empty($ms['alter_time']) ? true : false;
    $cold = $cold ? true : time()-strtotime($ms['alter_time']) > 86400; //一天就提醒一次
    $code = $ms['stock_code'];
    $code_key = "hs_".$code;
    $url = STOCK_URL.$code_key."/0930.js";
    $info = getStockInfo($url);
    $data = formatData($info,$code_key);
    $length = count($data);
    $current_pric = empty($data[$length-1]['current_price']) ? 0 : $data[$length-1]['current_price'];
    $c_date = date('Y-m-d H:i:s',time());
    if(!empty($ms['stock_code']) && $ms['stock_price']>0 && $cold){
        //如果不为空且当前价格小于检测价格
        if(!empty($current_pric) && $current_pric<$ms['stock_price'] && $ms['check_type']==0){
			$mail_t.="<tr><td>".$ms['stock_code']."</td><td>".$ms['stock_name']."</td><td>".$current_pric."</td><td>".$ms['stock_price']."</td><td><font color='red'>建议加仓</font></td></tr>";
            //加入冷却时间避免反复提醒
            $sql = "update select_stock set alter_time = '".$c_date."' WHERE id = ".$ms['id'];
            $re = $pdo -> exec ($sql);
            $ismail = true;
        }
        if(!empty($current_pric) && $current_pric>$ms['stock_price'] && $ms['check_type']==1){
            $mail_t.="<tr><td>".$ms['stock_code']."</td><td>".$ms['stock_name']."</td><td>".$current_pric."</td><td>".$ms['stock_price']."</td><td><font color='red'>建议减仓或者清仓</font></td></tr>";
            //加入冷却时间避免反复提醒
            $sql = "update select_stock set alter_time = '".$c_date."' WHERE id = ".$ms['id'];
            $re = $pdo -> exec ($sql);
            $ismail = true;
        }
        //加入监控日志 每次检测的结果写入数据库
        $sql="INSERT INTO check_stock VALUES(null,'".$ms['stock_code']."',".$current_pric.",'".$c_date."');";
        $re = $pdo -> exec ($sql);
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
    <th>监测价格</th>
    <th>操作建议</th>
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
    $mail->setMailInfo("每日邮件提醒", $mail_text);
    $mail->sendMail();
}
echo "success";