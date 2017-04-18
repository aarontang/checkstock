<?php
/**
 * Created by aaron <2590419211@qq.com>.
 * User: john
 * Date: 2017/3/30
 * Time: 15:32
 */

$serv = new swoole_server('0.0.0.0',9501);
$serv->set(array(
    'work_num'=>4,
    'open_eof_check'=>true,
    'package_eof'=>"}}}}}",
    'package_max_length'=>80000,
));
//设置回调
$serv->on('receive',function(swoole_server $serv,$fd,$from_id,$data){
    var_dump($data);
    $serv->send($fd,"response\n");
});

$serv->start();