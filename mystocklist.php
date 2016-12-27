<html>
<head>
    <title>检测列表</title>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
  </head>
<body>
  <table>
	<caption>检测列表<caption>
	<tr><td>股票代码</td><td>股票名称</td><td>检测价格</td><td>提醒类型</td></tr>
	<?php
	include  'config.php' ;
	$pdo = new PDO(DSN, DB_USER, DB_PASSWD);//创建数据库连接
	$sth  =  $pdo -> prepare ( 'select * from select_stock where uid = ?' );
	$sth -> execute (array(1));
	$my_stock  =  $sth -> fetchAll (PDO::FETCH_ASSOC);
	$my_stock = empty($my_stock) || !is_array($my_stock) ? array() : $my_stock;
	foreach($my_stock as $ms){
		?>
		<tr><td><?php echo $ms['stock_code'];?></td><td><?php echo $ms['stock_name'];?></td><td><?php echo $ms['stock_price'];?></td><td><?php if($_POST['check_type']==0){echo "下限";}else{echo "上限";}?></td></tr>
		<?php
	}
	?>
  </table>
</body>
</html>