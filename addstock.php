<html>
<head>
    <title>添加检测</title>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
  </head>
<body>
  <form action="addstock.php" method="post">
      <table>
          <tr><td>代码</td><td><input type="text" name="stock_code"></td></tr>
          <tr><td>名称</td><td><input type="text" name="stock_name"></td></tr>
          <tr><td>价格</td><td><input type="text" name="stock_price"></td></tr>
          <tr><td>类型</td><td>
              <select name="check_type">
                <option value="0">下限</option>
                <option value="1">上限</option>
              </select>
            </td></tr>
      </table>
    <input type="submit" name="submit" value="提交">
  </form>
</body>
<?php
/**
 * Created by aaron <2590419211@qq.com>.
 * User: user
 * Date: 2016/12/15
 * Time: 17:33
 */
include  'config.php' ;
include  'mail.php' ;
?>
</html>