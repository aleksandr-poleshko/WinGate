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
session_start(); 
error_reporting(E_ALL & ~E_NOTICE|E_DEPRECATED|E_STRICT);
header('Content-type: text/html; charset=utf-8');
ini_set('display_errors', 1);
set_time_limit(5*60); // макс лимит времени выполнения (5 мин)
ob_start();

define("JENSENCMS2", TRUE);
define("ROOT_PATH", dirname(__FILE__));
define("PATH", dirname($_SERVER['PHP_SELF'])=='/'?dirname($_SERVER['PHP_SELF']):dirname($_SERVER['PHP_SELF']).'/');
define("AJAX", $_SERVER['HTTP_X_REQUESTED_WITH'] == 'XMLHttpRequest'?TRUE:FALSE);

// запрет прямого доступа к init.php
if(preg_match('#^/init.php#i', $_SERVER["REQUEST_URI"])){ ob_end_clean(); header('HTTP/1.1 301'); header("Location: ".PATH); exit(); }

include_once("../include/functions.php");

if( !include_once("./modules/core/core.jcms.php") ){ exit("<h3>Критическая ошибка Jensen CMS! Не удалось загрузить ядро Jensen CMS! [php/init#".__LINE__."]</h3>"); }
$_JCMS = new JENSENCMS;
set_error_handler(array($_JCMS,'phpErrorHandler'));
$_JCMS->init();


if( AJAX ) {
	$_JSON = array();
	$_JCMS->load_module('core/syslog',1);
	$_JCMS->load_module('core/auth');
	if( $_JCMS->auth->checkAuth() ){
		// модули доступны только после авторизации
		if( !empty($_JCMS->query[0]) && !empty($_JCMS->query[1]) ){
			// проверяем права пользователя на загрузку модуля.
			$perms = $_JCMS->auth->getProfile(); $perms = $perms['permission'];
			if( ($perms[$_JCMS->query[0].'/'.$_JCMS->query[1]] && in_array("init", $perms[$_JCMS->query[0].'/'.$_JCMS->query[1]])) || ($_JCMS->query[0]=='core'&&in_array(strtoupper($_JCMS->query[1]), $_JCMS->sysmodules)) ){
				$_JCMS->load_module($_JCMS->query[0].'/'.$_JCMS->query[1]);
			} else {
				$_JSON['result'] = 2;
				$_JCMS->message('Запрошенный модуль не установлен, либо доступ ограничен групповой политикой безопасности! [php/init#'.__LINE__.']', '', 'error');
			}
		}
		$_JCMS->load_interface();
		if( $_JSON['result'] < 1 ){
			// статус запроса неизвестен, открываем страницу модуля по умолчанию
			$def_module = $_JCMS->getConfig('def_module');
			// если запрос пустой, выводим страницу модуля по умолчанию... иначе ошибку
			if( $_JCMS->query[0].'/'.$_JCMS->query[1] == '/' && $_JCMS->query[0].'/'.$_JCMS->query[1] != $def_module ){
				if( !$def_module ){
					$_JCMS->message('Модуль &laquo;по умолчанию&raquo; не определён! [php/init#'.__LINE__.']', 'Установить модуль &laquo;по умолчанию&raquo; можно в разделе &laquo;Конфигурация Jensen CMS&raquo;.', 'warning');
					$_JSON['module_title'] = "";			 		
				} else
				if( !$_JCMS->load_module($def_module) ){ 
					$_JCMS->message('Ошибка: не удалось загрузить страницу модуля &laquo;по умолчанию&raquo;. [php/init#'.__LINE__.']', '', 'error');
					$_JSON['module_title'] = "";
				}
			} else {
				$_JCMS->message("Ошибка: модуль для обработки этого запроса не установлен, отключён или работает неправильно! [php/init#".__LINE__.']', "", 'error');
				$_JSON['module_title'] = "";
			}
		} else
		if( $_JSON['result'] == 2 ){
			$_JSON['result'] = 0;
		}
		if( !is_int($_JSON['result']) ){ $_JSON['result'] = 0; } // не понятный ответ
	}
	// удаляем возможные дубли из важных массивов.
	if( is_array($_JSON['callback']) ) $_JSON['callback'] = array_values(array_unique($_JSON['callback']));
	if( is_array($_JSON['load_module']) ) $_JSON['load_module'] = array_values(array_unique($_JSON['load_module']));
	$_JSON['template'] = ob_get_contents().$body.$_JSON['template']; ob_clean(); ob_clean();
	$_JSON['template'] = phpErrorDeprecatedHandler($_JSON['template']);
	$_JSON['template'] = ob_get_contents().$body.$_JSON['template'];
	if( empty($_JSON['template']) ){ unset($_JSON['template']);  }
	ob_end_clean();
	ob_end_clean();
	$_JSON = json_encode($_JSON);
	echo $_JSON;
	exit();
} else {
	header("HTTP/1.0 401"); // ставим заголовок к кодом 401 (требуется авторизация)
	$body = ob_get_contents().$body; ob_clean();
	$body = phpErrorDeprecatedHandler($body);
	$body = ob_get_contents().$body; ob_clean();
	$body .= '<script type="text/javascript">window.onload = function(){ JCMS.init('.(json_encode(array('path'=>PATH))).'); };</script>';
	ob_end_clean();
	$_JCMS->tpl->load("core/main.tpl");
	$_JCMS->tpl->tag("{JCMS_YEAR}", date("Y"));
	$_JCMS->tpl->tag("{BODY}", $body); 
	$_JCMS->tpl->tag("{HOME_URL}", $_JCMS->getConfig('site_url'));
	$_JCMS->tpl->tag("{JENSENCMS_VERSION}", $_JCMS->getVersion());
	$t = $_JCMS->tpl->compile();
	echo $_JCMS->compressOutput($t);
	exit();
}

// END.
?>