<?php
/**
 * Created by aaron <2590419211@qq.com>.
 * User: user
 * Date: 2016/12/2
 * Time: 16:07
 */

/**
 * 使用CURL库读取指定地址信息
 * @param string $url 要读取的URL地址
 * @return string 地址內容 失败时为FALSE
 */
function getArea($url, $timeout = 3)
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
    $header[] = "X_TRACK_ID: ". $_SERVER['X_TRACK_ID'];
    $header[] = "X-BACKEND-BILI-REAL-IP: " . get_ip();
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