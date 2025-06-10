<?PHP
/*  =================================  ##
##              Jensen CMS 2           ##
##  =================================  ##
##          Copyright (c) 2015         ##
##         www.JensenStudio.net        ##
##  =================================  ##
##   WWW: www.JensenStudio.net         ##
##   EMAIL: support@JensenStudio.net   ##
##  =================================  */

header("HTTP/1.0 200");
error_reporting(E_ALL & ~E_NOTICE|E_DEPRECATED|E_STRICT);
ini_set('display_errors', 0); 
header('Content-type: text/html; charset=utf-8');
$errors = array(
	400 => array("Неправильный запрос!", '<b>Ваш запрос не может быть обработан!</b><br>Возможно, была допущена ошибка в адресе или в параметрах запроса.', "400 Bad Request"),
	401 => array("Требуется авторизация!", '<b>Извините, доступ к запрошенной странице возможен только после авторизации на сервере!</b>', "401 Unauthorized"),
	403 => array("Доступ запрещён!", '<b>Извините, доступ к запрошенной странице запрещён!</b><br>Возможно, вы пытаетесь получить доступа к системным ресурсам веб-сервера или к файлам, доступ к которым был ограничен администратором сервера.</i>', "403 Forbidden"),
	404 => array("Документ не найден!", '<b>По запрошенному Вами адресу ничего не нет!</b><br>Вероятно, запрошенный документ был перемещен, удален, либо никогда не существовал на этом сайте.', "404 Not Found"),
	410 => array("Страница удалена!", "<b>Запрошенная страница раньше была по этому адресу, но была удалена и теперь недоступна.</b>", "410 Gone"),
	500 => array("Внутренняя ошибка сервера!", "<b>Во время выполнения запроса произошла ошибка сервера!</b><br/>Попробуйте выполнить запрос немного позже или обратитесь к администратору сайта...", "500 Internal Server Error")
);
$error = intval($_SERVER['REDIRECT_STATUS']); if( !$errors[$error] ) $error = 404; $_error = $errors[$error];$_error[3] = $error; $error = $_error; unset($_error);
header("HTTP/1.1 ".$error[3]);
echo "<html><head><title>Ошибка ".$error[3].' - '.$error[0]." | Jensen CMS - система управления сайтом</title><style type=\"text/css\">div{ margin:0px; padding:0px; width:800px; margin:0 auto;}body>div>div{ background-color:#FFF;width:800px; border:solid 1px black; margin:0 auto; border-radius:6px;}h1,p{margin:0px; padding:0px; border-bottom:dashed 1px black; padding:20px 10px;}p:last-child{border:none;}a{color:gray}h1{text-align:center;}body{background-color:#CCC; padding-top:10%;}</style>
</head><body><div><div><h1>Ошибка ".$error[3].' - '.$error[0]."</h1><p>".$error[1]."</p><p><b>Запрошенный адрес:</b> <kbd><i>".$_SERVER['SERVER_NAME'].$_SERVER['REQUEST_URI']."</i></kbd><br><b>Ответ сервера:</b> <kbd><i>".$_SERVER['SERVER_PROTOCOL'].' '.$error[2]."</i></kbd><br/></p></div><p><kbd><i style=\"color:gray;\">* Страница автоматически сгенерирована системой управления сайтом &ndash; <a href=\"http://jensenstudio.net/jensencms\" target=\"_blank\" title=\"Jensen CMS - система управления сайтом\">Jensen CMS</a>.</i></kbd></p></div></body></html>";
// END. 
?>