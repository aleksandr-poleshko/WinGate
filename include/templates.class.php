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

class TPL{ 
	protected $dir = NULL;
	protected $template = NULL;
	protected $data = array();
	
	function __construct($dir=''){
		$this->dir = $dir;
	}
	
	function load($tpl_name){
		global $_JCMS;
		if( !is_readable($this->dir."/".$tpl_name) ) { 
			$_JCMS->message("Ошибка: не удалось загрузить шаблон &laquo;{$tpl_name}&raquo;. [php/tpl.load#".__LINE__."]", "", "warning");
			return false;
		} else {
			$this->template = file_get_contents($this->dir."/".$tpl_name);
		}
	}
	
	function tag($name, $var) {
		$this->data[$name] = $var;
	}
	
	function clear() {
		$this->data = array();
		$this->template = NULL;
	}
	
	function compile() {
		foreach ( $this->data as $key_find => $key_replace ) {
			$find[] = $key_find;
			$replace[] = $key_replace;
		}
		$result = @str_replace( $find, $replace, $this->template);
		return $result;
	}
}

// END.
?>