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
ob_start();
session_start();
error_reporting(E_ALL & ~E_NOTICE|E_DEPRECATED|E_STRICT);
ini_set('display_errors', 0); 
header('Content-type: text/html; charset=utf-8');
set_time_limit(5*60); // макс лимит времени выполнения (5 мин)

define("JENSENCMS2", TRUE);
define("ROOT_PATH", dirname(__FILE__));
define("PATH", dirname($_SERVER['PHP_SELF'])=='/'?dirname($_SERVER['PHP_SELF']):dirname($_SERVER['PHP_SELF']).'/');
define("AJAX", $_SERVER['HTTP_X_REQUESTED_WITH'] == 'XMLHttpRequest'?TRUE:FALSE);

if( $_GET['ref'] ){
	// получен реферал... Сохраянем в кукахи редиректим на сайт..
	setcookie("JCMS2_LK_REFERAL", strval($_GET['ref']));
}

// запрет прямого доступа к init.php
if(preg_match('#^/init.php#i', $_SERVER["REQUEST_URI"])){ ob_end_clean(); header('HTTP/1.1 301'); header("Location: ".PATH); exit(); }

if( $_REQUEST['__error__'] ){
	/* Обработчик для страниц ошибок сервера */
	$err = intval($_REQUEST['__error__']);
	echo $intval;
	exit();
}

include_once("./include/functions.php");


if( !include_once("./modules/core/core.jcms.php") ){ exit("<h3>Критическая ошибка Jensen CMS! Не удалось загрузить ядро Jensen CMS! [php/init#".__LINE__."]</h3>"); }
$_JCMS = new JENSENCMS;
if($_JCMS->show_php_errors){ set_error_handler(array($_JCMS,'phpErrorHandler')); ini_set('display_errors', 1);  }
$_JCMS->init();
/* +++ CUSTOM ACTIONS +++ */

/* ---------------------- */
$body = ob_get_contents().$body; ob_clean();

if( $_JCMS->query[0] ){
	if( $body  == '' ){
		header("HTTP/1.1 404");
		$_JCMS->message("Ошибка: запрошенная страница не найдена", "", 'error');
	}
}
if($_JCMS->show_php_errors){ $body = phpErrorDeprecatedHandler($body); }
$body = ob_get_contents().$body; ob_clean();
ob_end_clean();

if( $_JCMS->getConfig('site_status') == 'on' ){
	if( is_array($_META['title']) ) $_META['title'] = implode(' / ', array_reverse($_META['title']));
	if( is_array($_META['breadcrumb']) ){
		if( count($_META['breadcrumb']) == 1 ){ $body .= "<style>.breadcrumb{display:none;}</style>"; }
		$tmp = "\n";
		foreach($_META['breadcrumb'] as $val){
			$val['url'] = str_replace("{SITE_URL}", $_JCMS->getConfig('site_url'), $val['url']);
			$tmp .= "\t".'<li itemscope itemtype="http://data-vocabulary.org/Breadcrumb"><a href="'.$val['url'].'" itemprop="url"><span itemprop="title">'.$val['title'].'</span></a></li>'."\n";
		}
		$_META['breadcrumb'] = "\n<ul>".$tmp."</ul>\n";
		unset($tmp);
	}
	$_JCMS->tpl->load("core/main_{$_JCMS->lang}.tpl");		

	$_JCMS->tpl->tag("{BODY}", $body);
	$_JCMS->tpl->tag("{BREADCRUMB}", $_META['breadcrumb']);
	$_JCMS->tpl->tag("{RELATED_ITEMS}", $_RELATED_ITEMS);
	$_JCMS->tpl->tag("{HTML_TITLE}", ($_META['title']?$_META['title'].' | ':NULL).$_JCMS->getConfig('site_title'));
	$_JCMS->tpl->tag("{SITE_TITLE}", $_JCMS->getConfig('site_title'));
	$_JCMS->tpl->tag("{SITE_URL}", $_JCMS->getConfig('site_url'));
	$_JCMS->tpl->tag("{SITE_LANG}", $_JCMS->lang);
	$_JCMS->tpl->tag("{SITE_KEYWORDS}", $_META['keywords']?$_META['keywords']:$_JCMS->getConfig('site_keywords'));
	$_JCMS->tpl->tag("{SITE_DESCRIPTION}", $_META['description']?$_META['description']:$_JCMS->getConfig('site_description'));
	if( $_JCMS->getConfig('minify_output') ){
		$body = $_JCMS->tpl->compile();
		$body = $_JCMS->compressOutput($body);
		echo $_JCMS->compressOutput($body);
	} else { 
		echo $_JCMS->tpl->compile();		
	}
	exit();
} else {
	header("HTTP/1.1 503 Service Temporarily unavailable");
    header("Retry-After: 3600");
	$_JCMS->tpl->load("core/offline.tpl");
	$_JCMS->tpl->tag("{OFFLINE_REASON}", $_JCMS->getConfig('site_statusComment'));
	if( $_JCMS->getConfig('minify_output') ){
		$body = $_JCMS->tpl->compile();
		$body = $_JCMS->compressOutput($body);
		echo $_JCMS->compressOutput($body);
	} else { 
		echo $_JCMS->tpl->compile();		
	}
}



// END.
?>