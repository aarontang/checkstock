<?php
#载入数据库配置
include  'config.php' ;
$pdo = new PDO(DSN, DB_USER, DB_PASSWD);
$sth  =  $pdo -> prepare ( 'select * from video_data limit 1' );
$sth -> execute ();
$my_data  =  $sth -> fetchAll (PDO::FETCH_ASSOC);
$my_data = empty($my_data) ? array() : $my_data;
foreach($my_data as $md){
    $sql="INSERT INTO `wp_posts` VALUES (null, '1', '2016-12-29 22:23:37', '2016-12-29 14:23:37', '<h2 style=\"text-align: center;\"><a href=\"".$md['video_url']."\" target=\"_blank\">".$md['video_name']."</a></h2>\r\n<img class=\"alignnone size-medium\" src=\"".$md['pic_url']."\" width=\"640\" height=\"480\" />\r\n<p style=\"text-align: center;\"><a href=\"".$md['video_url']."\" target=\"_blank\">&gt;&gt;&gt;&gt;&gt;&gt;&gt;福利直达地址&lt;&lt;&lt;&lt;&lt;&lt;&lt;&lt;&lt;&lt;</a></p>', '".$md['video_name']."', '', 'publish', 'open', 'open', '', '".urlencode($md['video_name'])."', '', '', '2016-12-29 22:28:18', '2016-12-29 14:28:18', '', '0', 'http://www.fuliselect.cn/?p=15', '0', 'post', '', '0');";
    $re = $pdo -> exec ($sql);
    $id = $pdo->lastInsertId();



    var_dump($id);
}


