<?php
/**
 * Created by aaron <2590419211@qq.com>.
 * User: john
 * Date: 2017/3/30
 * Time: 15:32
 */

//创建一个服务器对象
//127.0.0.1本机回环 没有通过网卡
//
$ser = new swoole_server('127.0.0.1',9501);
//事件  有三个基本都要设置回调的，一个是链接，一个是接受数据，一个是关闭
//设置事件 事件回调的匿名函数
//链接时候的事件
$ser->on('connect',function($ser,$fd,$fromid){
    echo "connect success\n";
    $ser->send($fd,'hello');
});

//这是服务器收到客户端推送过来的时候的数据时 这边不指定参数类型是没办法ID 提示send方法的
$ser->on('receive',function(swoole_server $ser,$fd,$fromid,$data){
    echo "receive".$data,$fromid;
    $ser->send($fd,"server:".$data);
});

//关闭事件
$ser->on('close',function($ser,$fd,$fromid){
    echo "closed \n";
});

$ser->start();