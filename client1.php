<?php
/**
 * Created by aaron <2590419211@qq.com>.
 * User: john
 * Date: 2017/3/30
 * Time: 15:32
 */

//创建一个客户端 使用异步的方式。
$client = new swoole_client(SWOOLE_SOCK_TCP,SWOOLE_SOCK_ASYNC);

$client->set(array(
    'open_length_check'     => 1,
    'package_length_type'   => 'N',
    'package_length_offset' => 1,       //第N个字节是包长度的值
    'package_body_offset'   => 3,       //第几个字节开始计算长度
    'package_max_length'    => 2000000,  //协议最大长度
));

$client->on('connect',function($cli){
    echo "client connect";
    $cli->send("hello world\n");
});

$client->on('receive',function($cli,$data){
    echo "client receive",$data,"\n";
});

$client->on('error',function(){
    echo "client error";
});

$client->on('close',function(){
    echo "client close";
});

$client->connect('127.0.0.1',9501);

echo 111;