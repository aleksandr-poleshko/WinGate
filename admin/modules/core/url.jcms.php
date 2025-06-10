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

if( !defined("JENSENCMS2") ) { exit("Hacking attempt!"); }

class URL implements JCMS_MODULE_COMPONENT{
	function __construct(){}

	/* инициаизация модуля */ 
	function setup(){
		$setup["title"]	= "Компонент для работы ЧПУ в панели управления";
		return $setup;
	} 
	
	function work(){ 
		// смещение скрипта от корня сайта (например скрипт находится в подпапке)
		$sub_path = str_replace($_SERVER['DOCUMENT_ROOT'], "", dirname($_SERVER['SCRIPT_FILENAME']));
		$req_url = $_SERVER['REDIRECT_URL']; // запрошенный ЧПУ
		// убираем из запроса путь до подпапки скрипта, т.к. он не являетяс частью ЧПУ запроса.
		$req_url = str_replace($sub_path, "", $req_url);
		$req_url = explode(DIRECTORY_SEPARATOR, $req_url);
		/* удаляем пустые элементы */
		foreach($req_url as $key=>$val){ if( !empty($val) ) $_req_url[]=$val; }
		
		return $index!=false?$_req_url[$index]:$_req_url;
	}
}

// END.
?>