<?php
#载入数据库配置
include  'config.php' ;
$pdo = new PDO(DSN, DB_USER, DB_PASSWD);
$content = file_get_contents('thefile1.txt');
$content_arr = explode("\n",$content);
$content_arr = empty($content_arr) || !is_array($content_arr) ? array() : $content_arr;
foreach($content_arr as $ca){
    $ca1 = trim($ca,"\n");
    $data = explode(",,,",$ca1);
    if(!$data['0']){
        //没有播放地址
        continue;
    }
    $sql = "INSERT INTO `video_data` VALUES (null, '".$data['0']."', '".$data['3']."', '".$data['2']."', '".$data['1']."', '".$data['4']."');";
    try {
        $re = $pdo -> exec ($sql);
    } catch ( Exception $e ) {
        echo  'insert fail Caught exception: ' ,   $e -> getMessage (),  "\n" ;
    }
}