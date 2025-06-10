<?php 



phpinfo();


exit();
ini_set('error_reporting', E_ALL);
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);



echo '<form method="POST">
<input type="mail" name="mail"><br />
<input type="submit" name="submit" value="Send">
';

if (isset($_POST["submit"])) {

$mails_to = $_POST["mail"];

$to = $mails_to; 
$text = "Hello world!";
$subject = "TEST MESSAGE";



include_once('/var/www/proxymen/data/www/cabinet.wingate.me/include/phpmailer/class.phpmailer.php');

$mail = new PHPMailer();
$mail->Host = "185.43.221.165";
$mail->Port   = "465";
$mail->SMTPSecure = 'ssl';
$mail->Username   = "noreplay@wingate.me";
$mail->Password   = "uH7oZ0gJ8q";
$mail->SetFrom("noreplay@wingate.me", "WinGate.Me");
$mail->AddAddress($to);
$mail->Subject = iconv("utf-8",'cp1251',$subject);
$mail->MsgHTML(iconv("utf-8",'cp1251',$text));
$mail->SMTPDebug = 1;
//$mail->Debugoutput = "echo";
//$mail->Send();

if(!$mail->send()) {
    echo 'Message could not be sent.';
    echo 'Mailer Error: ' . $mail->ErrorInfo;
} else {
    echo 'Message has been sent';
}
}

?>

