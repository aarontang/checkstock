<?php
/**
 * Created by aaron <2590419211@qq.com>.
 * User: user
 * Date: 2016/12/15
 * Time: 17:33
 */
include  'config.php' ;
include  'mail.php' ;
$pdo = new PDO(DSN, DB_USER, DB_PASSWD);
if(!empty($_POST['submit'])){
    $sql="INSERT INTO select_stock VALUES(null,1,'".$_POST['stock_code']."',".$_POST['stock_price'].",".$_POST['check_type'].",'".$_POST['stock_name']."','',0);";
    $re = $pdo -> exec ($sql);
    if($re){
        echo "success";
    }else{
        echo "fail";
    }
}
if(empty($_GET['id'])){
    //有ID 传过来看作是修改
    $id = (int)$_GET['id'];
    $sql="select * from my_stock WHERE ";
    $sth  =  $pdo -> prepare ( 'select * from my_stock WHERE id = ? limit 1' );
    $sth -> execute (array(1));
    $my_stock  =  $sth -> fetch (PDO::FETCH_ASSOC);
    var_dump($my_stock);
}
?>
<html>
<head>
    <title></title>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
  </head>
<body>
  <form action="addmystock.php" method="post">
      <input type="hidden" name="id" value="">
      <table>
          <tr><td>代码</td><td><input type="text" name="stock_code"></td></tr>
          <tr><td>名称</td><td><input type="text" name="stock_name"></td></tr>
          <tr><td>价格</td><td><input type="text" name="stock_price"></td></tr>
      </table>
    <input type="submit" name="submit" value="提交">
  </form>
</body>
</html>