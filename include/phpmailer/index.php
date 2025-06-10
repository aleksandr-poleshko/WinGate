<?php 

session_start();
error_reporting(E_ALL^E_NOTICE);
ini_set('display_errors', 1); 

require_once('class.phpmailer.php');

$mail = new MultipleInterfaceMailer(true);
$mail->IsSMTPX('185.20.224.126');
try {
  $mail->Username   = "support@sectortelecom.ru";
  $mail->Password   = "ej2QlzWoLC";
  $mail->AddAddress('panshin32@yandex.ru', 'Паньшин Евгений Викторович');
  $mail->AddAddress('panshin32@mail.ru', 'Паньшин Евгений Викторович');
  $mail->AddAddress('panshin32@gmail.com', 'Паньшин Евгений Викторович');
  $mail->SetFrom('support@sectortelecom.ru', 'Сектор Телеком');
  $mail->Subject = 'тестовое письмо';
  $mail->MsgHTML("текст тестового письма");
  $mail->SMTPDebug = 10;
  $mail->Debugoutput = 10;
  $mail->Send();
  echo "Message Sent OK\n";
} catch (phpmailerException $e) {
  echo $e->errorMessage(); //Pretty error messages from PHPMailer
} catch (Exception $e) {
  echo $e->getMessage(); //Boring error messages from anything else!
}
    
	
	
	

?>	