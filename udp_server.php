<?php
/**
 * Created by aaron <2590419211@qq.com>.
 * User: john
 * Date: 2017/3/30
 * Time: 15:32
 */

//创建一个UDP服务器对象
//127.0.0.1本机回环 没有通过网卡
//
$ser = new swoole_server('127.0.0.1',9502,SWOOLE_PROCESS,SWOOLE_SOCK_UDP);
$ser->set(array(
    'work_num'=>4,
    'dispatch_mode'=>2
));
//udp服务器只有一个receive 事件
$ser->on('receive',function(swoole_server $ser,$fd,$from_id,$data){
    $ser->send($fd,'swoole:'.$data,$from_id);
    echo 'receive',$fd,"\n";
});
