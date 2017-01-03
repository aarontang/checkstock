<?php
#载入数据库配置
include  'config.php' ;
$pdo = new PDO(DSN, DB_USER, DB_PASSWD);
$sth  =  $pdo -> prepare ( 'select * from video_data ' );
$sth -> execute ();
$my_data  =  $sth -> fetchAll (PDO::FETCH_ASSOC);
$my_data = empty($my_data) ? array() : $my_data;
$fenlei_arr = array(
    '福利视频'=>2,
    '美女主播'=>5,
    '丝袜美腿'=>6,
    '微拍广场'=>3,
    '街拍美女'=>7,
    '微福利'=>4,
    '视频广场'=>10,
);
foreach($my_data as $md){
    $pid = (int)($id+1);
    $sql="INSERT INTO `wp_posts` VALUES (null, '1', '2016-12-29 22:23:37', '2016-12-29 14:23:37', '<h2 style=\"text-align: center;\"><a href=\"".$md['video_url']."\" target=\"_blank\">".$md['video_name']."</a></h2>\r\n<img class=\"alignnone size-medium\" src=\"".$md['pic_url']."\" width=\"640\" height=\"480\" />\r\n<p style=\"text-align: center;\"><a href=\"".$md['video_url']."\" target=\"_blank\">&gt;&gt;&gt;&gt;&gt;&gt;&gt;福利直达地址&lt;&lt;&lt;&lt;&lt;&lt;&lt;&lt;&lt;&lt;</a></p>', '".$md['video_name']."', '', 'publish', 'open', 'open', '', '".urlencode($md['video_name'])."', '', '', '2016-12-29 22:28:18', '2016-12-29 14:28:18', '', '0', 'http://www.fuliselect.cn/?p=".$pid."', '0', 'post', '', '0');";
    $re = $pdo -> exec ($sql);
    $id = $pdo->lastInsertId();

    $sql = "INSERT INTO `wp_posts` VALUES (null, '1', '2016-12-29 22:23:32', '2016-12-29 14:23:32', '<h2 style=\"text-align: center;\"><a href=\"".$md['video_url']."\" target=\"_blank\">".$md['video_name']."</a></h2>\r\n<img class=\"alignnone size-medium\" src=\"".$md['pic_url']."\" width=\"640\" height=\"480\" />\r\n<p style=\"text-align: center;\"><a href=\"http://www.vlook.cn/show/qs/YklkPTI3OTAxNzg\" target=\"_blank\">&gt;&gt;&gt;&gt;&gt;&gt;&gt;福利直达地址&lt;&lt;&lt;&lt;&lt;&lt;&lt;&lt;&lt;&lt;</a></p>', '".$md['video_name']."', '', 'inherit', 'closed', 'closed', '', '".$id."-revision-v1', '', '', '2016-12-29 22:23:32', '2016-12-29 14:23:32', '', '".$id."', 'http://www.fuliselect.cn/2016/12/29/".$id."-revision-v1/', '0', 'revision', '', '0');";
    $re = $pdo -> exec ($sql);

    //进入分类组
    if(array_key_exists($md['video_fenlei'],$fenlei_arr)){
        $sql = "INSERT INTO `wp_term_relationships` VALUES ('".$id."', '".$fenlei_arr[$md['video_fenlei']]."', '0');";
        $re = $pdo -> exec ($sql);
    }
}


