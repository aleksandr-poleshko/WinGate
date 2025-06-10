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

class DATABASE implements JCMS_MODULE_COMPONENT{
	function __construct(){}
	
	/* инициаизация модуля */ 
	function setup(){
		$setup["title"]	= "Компонент для работы с базой данных";
		return $setup;
	}

	function connect($hostname, $username, $password, $dbname){
		global $_JCMS;
		$conn = new mysqli($hostname, $username, $password, $dbname);
		
		if( mysqli_connect_error() ){
			$_JCMS->message("Ошибка при подключении к базе данных! [php/core#".__LINE__."]", "MYSQL ERROR #" . $conn->connect_errno . ' - ' . $conn->connect_error, 'error');
		}
		if( !$conn->set_charset("utf8") ){
			$_JCMS->message("Ошибка определения кодировки для работы с базой данных! [php/core#".__LINE__."]", "", 'error');
		}	
		
		return $conn;
	}
	public function work(){}
}

// END.
?>