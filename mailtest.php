<html>
<head>
    <title></title>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
  </head>
<?php
/**
 * Created by aaron <2590419211@qq.com>.
 * User: user
 * Date: 2016/12/15
 * Time: 17:33
 */
include  'config.php' ;
include  'mail.php' ;
$mail_text = <<<EOF
<table border="1">
<caption>股票监测提醒</caption>
  <tr>
    <th>股票代码</th>
    <th>股票名称</th>
    <th>当前价格</th>
    <th>监测价格</th>
    <th>操作建议</th>
  </tr>
  <tr>
    <td>000651</td>
    <td>格力电器</td>
    <td>25.62</td>
    <td>26.00</td>
    <td><font color="red">建议加仓</font></td>
  </tr>
</table>
EOF;
$mail = new MySendMail();
$mail->setServer(SMTP_HOST, MAIL_NAME, MAIL_PASSWD);
$mail->setFrom(MAIL_NAME);
$mail->setReceiver(RECEIVER_MAIL);
$mail->setMailInfo("每日邮件提醒", $mail_text);
$mail->sendMail();
echo "success";
?>
</html>