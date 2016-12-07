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

/**
 * 使用同花顺数据
 */
$code = "000651";
$code_key = "hs_".$code;
$url = STOCK_URL.$code_key."/0930.js";
$info = getStockInfo($url);
$data = formatData($info,$code_key);
var_dump($data);

/**************************** Test ***********************************/
$mail = new MySendMail();
$mail->setServer(SMTP_HOST, MAIL_NAME, MAIL_PASSWD);
$mail->setFrom(MAIL_NAME);
$mail->setReceiver(RECEIVER_MAIL);
//$mail->setReceiver("XXXXX@XXXXX");
//$mail->setCc("XXXXX@XXXXX");
//$mail->setBcc("XXXXX@XXXXX");
//$mail->setBcc("XXXXX@XXXXX");
//$mail->setBcc("XXXXX@XXXXX");
$mail->setMailInfo("每日邮件提醒", "<b>test</b>这封是来自自己的测试邮件");
$mail->sendMail();