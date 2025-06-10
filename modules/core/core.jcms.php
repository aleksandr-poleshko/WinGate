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

if( !defined("JENSENCMS2") ) { header("HTTP/1.1 403"); exit("Hacking attempt!"); }

final class JENSENCMS{
	var $show_php_errors=0;
	protected $config = array();
	var $err_loadConf = false;
	var $config_file = NULL;
	var $sysmodules = array('CORE', 'DATABASE', 'URL');
	private $load_modules=array();

	final function __construct(){
		$this->config_file = realpath(ROOT_PATH."/config")."/jcms.config.php";
	}
	/* 
	 * Инициализация основных компонентов JensenCMS
	 */
	final function init(){	
		global $_JCMS,$_META;	
		$this->show_php_errors = $this->getConfig('show_php_errors')==1?true:false;
		/* Шаблонизатор */
		include_once(ROOT_PATH."/include/templates.class.php");
		$this->tpl = new TPL(ROOT_PATH.'/modules/');
		/* ЧПУ */
		$this->load_module('core/url',1);
		$_JCMS->query = $_JCMS->modules['URL']->work();
		/* БД */
		$this->load_module('core/database',1);
		if( !include_once(ROOT_PATH."/config/db.config.php") ){ $_JCMS->message("Ошибка: не удалось загрузить файл конфигурации базы данных! [php/core.init#".__LINE__."]", "", 'error'); }
		$_JCMS->db = $_JCMS->modules['DATABASE']->connect($db_conf['hostname'], $db_conf['username'], $db_conf['password'], $db_conf['database']);
		unset($db_conf);
		// язык
		if( $_JCMS->query[1] != 'payment_freekassa' && $_JCMS->query[0] != 'api.php' ){ // фрикасса не умеет редиректы, для неё не делаем их... да и не нужен ей мультиязык...
			$langs = array('ru', 'en');
			if( !in_array($_GET['lang'], $langs) ){
				// язык не определен... редиректим на первый в списке...
				if( $_COOKIE['jcms2_siteLang'] && in_array($_COOKIE['jcms2_siteLang'], $langs) ){
					// берем язык из куков...
					$_JCMS->lang = $_COOKIE['jcms2_siteLang']; 
				} else {
					// по дефолту берем первый язык из списка...
					$_JCMS->lang = array_shift($langs);
					setcookie('jcms2_siteLang', $_JCMS->lang, 0, '/');
					ob_end_clean();
					unset($_GET['lang']);
					$q = http_build_query($_GET);
					header("Location: ".$_JCMS->getConfig('site_url').'/'.implode('/', $_JCMS->query).($q?'?'.$q:''));
					exit();
				}
			} else {
				$_JCMS->lang = $_GET['lang'];
				// язык не определен... редиректим на первый в списке...
				if( $_COOKIE['jcms2_siteLang'] != $_GET['lang'] ){
					setcookie('jcms2_siteLang', $_JCMS->lang, 0, '/');
					ob_end_clean();
					unset($_GET['lang']);
					$q = http_build_query($_GET);
					header("Location: ".$_JCMS->getConfig('site_url').'/'.implode('/', $_JCMS->query).($q?'?'.$q:''));
					exit();
				}
			}
		}
		if( $_GET['lang'] ){
			unset($_GET['lang']);
			$q = http_build_query($_GET);
			header("Location: ".$_JCMS->getConfig('site_url').'/'.implode('/', $_JCMS->query).($q?'?'.$q:''));
			exit();			
		}
		// загрузка модулей ядра
		if( ($_JCMS->query[0] == 'sitemap' || $_JCMS->query[0] == 'sitemap.xml' ) && !$_JCMS->query[1] ){
			$_JCMS->sitemap = 1;
		}
		if( $_JCMS->query[0] == 'robots.txt' && !$_JCMS->query[1] ){			
			$_JCMS->robots = 1;
		}
		foreach($this->sysmodules as $val){
			$this->load_module('core/'.$val);
		}
		
		// загрузка модулей расширения
		foreach($this->getConfig('sysmodules') as $val=>$state){
			$this->load_module($val);
		}
		
		if( $_JCMS->sitemap == 1 ){
			$_JCMS->genSitemap();
		}
		if( $_JCMS->robots == 1 ){
			// доступ разрешен только для поисковых систем
			$bot_name_mask = "/(Yandex|Googlebot|StackRambler|Yahoo|WebAlta|msnbot)/i";
			if( true||preg_match($bot_name_mask, $_SERVER['HTTP_USER_AGENT']) == 1 ){
				ob_end_clean();
				header("HTTP/1.1 200");
				header('Content-Type: text/plain');
echo "User-agent: *
Sitemap: ".$this->getConfig('site_url')."/sitemap.xml
Allow: /
Disallow: /admin/
Disallow: *.php
Disallow: *.tpl
Disallow: /cabinet/cart?action=add&id=*
";				
				exit();
			}
		}
		
	}

	final function getConfig($param=NULL){
		global $_JCMS;
		if( empty($_JCMS->config) ){
			if( (include_once($_JCMS->config_file)) !== 1 ){
				// не удалось загрузить файл, пробуем выяснить почему...
				if( !$this->err_loadConf ){
					$this->err_loadConf = true;
					// проверку существование файл и папки конфига
					if( file_exists($_JCMS->config_file) && file_exists(dirname($_JCMS->config_file)) ){
						// существуют. Проверяем доступность их на чтение
						if( is_readable($_JCMS->config_file) && is_readable(dirname($_JCMS->config_file)) ){
							// неизвестная ошибка
							$_JCMS->message("Ошибка: не удалось загрузить файл конфигурации! [php/core.getConfig#".__LINE__."]", "", 'error');							
						} else {
							// нет прав на чтение
							$_JCMS->message("Ошибка: не удалось загрузить файл конфигурации! [php/core.getConfig#".__LINE__."]", "Возможно файл не существует или к нему нет доступа.", 'error');							
						}
					} else {
						// папка или файл не существуют
						$_JCMS->message("Ошибка: не удалось загрузить файл конфигурации! [php/core.getConfig#".__LINE__."]", "Файл ещё не создан или к нему нет доступа.", 'error');
					}
				} else {
					// ошибка уже выводилась ранее, игнорируем...
				}
			} else {
				// конфиг успешно загружен
				$_JCMS->config = $conf;
				unset($conf);
			}
		}
		return !empty($param)?$_JCMS->config[$param]:$_JCMS->config;
	}
	
	final function load_module($_module_name, $initOnly=false){
		global $_JCMS, $_JSON;
		$_module_name = strtolower($_module_name);
		$module_name = explode('/',$_module_name,2);
		if( $module_name[0] == 'module' ){ $module_name[0] = $module_name[1]; }
		if( empty($module_name[1]) ) $module_name[1] = $module_name[0];
		$allowModules = $_JCMS->getConfig('sysmodules');
		if( !$allowModules[$module_name[0].'/'.$module_name[1]] && !in_array(strtoupper($module_name[1]), $_JCMS->sysmodules) ) return; // модуль не разрешен к запуску

		$filename = ROOT_PATH.'/modules/'.$module_name[0].'/'.$module_name[1].'.jcms.php';
		if( $_module_name == 'core' || $_module_name == 'core/core' ){ $mod = 'JENSENCMS'; $initOnly = true; } else $mod = strtoupper($module_name[1]);
		if( !class_exists($mod) ){
			if( !is_file($filename) || (include_once($filename)) !== 1 ){
				$_JCMS->message("Ошибка: не удалось загрузить модуль <tt>&laquo;".strtoupper($_module_name)."&raquo;</tt> [php/core#".__LINE__."]", "Управляющий файл модуля не существует или к нему нет доступа.", 'error');
				return false;
			} else {
				if( !class_exists($mod) ){
					$_JCMS->message("Ошибка: <tt>&laquo;".basename($filename)."&raquo;</tt> не является модулем Jensen CMS! [php/core#".__LINE__."]", "", 'warning');
					return false;
				}				
			}
		}
		
		// проверяем реализован ли в модуле интерфейс модуля Jensen CMS
		$interfaces = class_implements($mod);
		if( !in_array('JCMS_MODULE', $interfaces) && $mod != __CLASS__ ){
			if( $_JCMS->load_modules[$mod] != -1 ){
				$_JCMS->load_modules[$mod] = -1;
				$_JCMS->message("Ошибка: <tt>&laquo;".basename($filename)."&raquo;</tt> не является модулем Jensen CMS! [php/core#".__LINE__."]", "", 'warning');
			}
			return false;
		}
		if( $_JCMS->load_modules[$mod] != 1 ){
			$_JCMS->load_modules[$mod] = 1;
			$_JCMS->modules[$mod] = new $mod();
			$_JCMS->$module_name[1] = &$_JCMS->modules[$mod];
		}
		if( $initOnly == false  ){
			$json = $_JCMS->$module_name[1]->work();
			if( $_JCMS->sitemap == 1 && method_exists($_JCMS->$module_name[1], 'sitemap') ){
				if( !is_array($_JCMS->_sitemap) ) $_JCMS->_sitemap = array();
				$_JCMS->_sitemap = array_merge_recursive($_JCMS->_sitemap,$_JCMS->$module_name[1]->sitemap());
			}
			if( $json == NULL || $json == false ) return false;
		}
		return true; 
	}
	
	final function message($title, $text='', $type='info', $return=false){
		if( !in_array($type, array('error', 'warning', 'notice')) ) $type = 'error';
		$template = '<div class="message message-'.$type.'"><span>'.$title.'</span><br />'.$text.'</div>';
		if( $return !== false ){
			return $template;
		} else {
			echo $template;
		} 
	}
	
	final function phpErrorHandler($errno, $errstr, $errfile, $errline){
/*		if (!(error_reporting() & $errno)) { return; }*/
		if( $errno == E_NOTICE ) return;
		if( !$this->show_php_errors ) return;
		$errfile = str_replace($_SERVER['DOCUMENT_ROOT'], '',$errfile); // убираем абсолюный путь к файлам
		$errstr = str_replace($_SERVER['DOCUMENT_ROOT'], '',$errstr); // убираем абсолюный путь к файлам
		$array = array(E_ERROR => "error", E_WARNING => 'warning', E_NOTICE => 'info');
		if( in_array($errno, $array) ){ $type = $array[$errno]; } else { $type= 'info';  }
		$this->message("PHP ".strtoupper(str_replace(array("E_","_"),"",$this->FriendlyPhpErrorType($errno))).": {$errstr}.","<i>Строка {$errline} файла <tt>&lt;{$errfile}&gt;</tt>.</i>", $type);
		return false;
	}
	
	final function FriendlyPhpErrorType($type){
		if( !$this->show_php_errors ) return; 
		switch($type) 
		{ 
			case E_ERROR: // 1 // 
				return 'E_ERROR'; 
			case E_WARNING: // 2 // 
				return 'E_WARNING'; 
			case E_PARSE: // 4 // 
				return 'E_PARSE'; 
			case E_NOTICE: // 8 // 
				return 'E_NOTICE'; 
			case E_CORE_ERROR: // 16 // 
				return 'E_CORE_ERROR'; 
			case E_CORE_WARNING: // 32 // 
				return 'E_CORE_WARNING'; 
			case E_COMPILE_ERROR: // 64 // 
				return 'E_COMPILE_ERROR'; 
			case E_COMPILE_WARNING: // 128 // 
				return 'E_COMPILE_WARNING'; 
			case E_USER_ERROR: // 256 // 
				return 'E_USER_ERROR'; 
			case E_USER_WARNING: // 512 // 
				return 'E_USER_WARNING'; 
			case E_USER_NOTICE: // 1024 // 
				return 'E_USER_NOTICE'; 
			case E_STRICT: // 2048 // 
				return 'E_STRICT'; 
			case E_RECOVERABLE_ERROR: // 4096 // 
				return 'E_RECOVERABLE_ERROR'; 
			case E_DEPRECATED: // 8192 // 
				return 'E_DEPRECATED'; 
			case E_USER_DEPRECATED: // 16384 // 
				return 'E_USER_DEPRECATED'; 
		} 
		
		return ""; 
	} 	
	protected function genSitemap_build($data, $isXML=false){
		if( !$isXML ){
			$tree .= "<ul>";
			foreach($data as $val){
				if( !is_array($val) ) continue;
				$tree .= "<li>";
				if( $val['url'] ) $tree .= "<a href=\"{$val[url]}\">{$val[title]}</a>"; else $tree .= $val['title'];
				if( $val['items'] ) $tree .= $this->genSitemap_build($val['items'], $isXML);
				$tree .= "</li>";
			}
			$tree .= "</ul>";
		} else {
			foreach($data as $val){
				if( $val['url'] ){
					$val['url'] = str_replace("{SITE_URL}", $this->getConfig('site_url'), $val['url']);
					$tree .= "\t<url>\r\n";
					$p = parse_url($val['url']);
					$q = _urlencode($p['query']);
					if( !empty($q) ){ $q = '?'.$q; }
					$tree .= "\t\t<loc>".$p['scheme']."://".$p['host'].$p['path'].$q."</loc>\r\n";
					
					if( $val['date'] ){
						$tree .= "\t\t<lastmod>".date("Y-m-d", strtotime($val['date']))."</lastmod>\r\n";
					}
					$tree .= "\t</url>\r\n";
				}
				if( $val['items'] ) $tree .= $this->genSitemap_build($val['items'], $isXML);			
			}
		}
		return $tree;
	}
	
	final function genSitemap(){
		global $_JCMS, $_META;
		if( $_JCMS->query[0] == 'sitemap' ){
			header("HTTP/1.1 200");
			$_JCMS->tpl->load("pages/page.tpl");
			$_META['title'] = "Карта сайта";
			$_JCMS->tpl->tag("{PAGE_TITLE}", $_META['title']);
			if( $_JCMS->_sitemap['pages'] ){
				$tmp = array('pages'=>$_JCMS->_sitemap['pages']);
				unset($_JCMS->_sitemap['pages']);
				if( !$tmp['pages']['items'] ) $tmp['pages']['items'] = array();
				$_JCMS->_sitemap = array_merge_recursive($tmp['pages']['items'], $_JCMS->_sitemap);
			}
			$tree = $this->genSitemap_build($_JCMS->_sitemap);
			
			$text = '<div class="sitemap">'.$tree.'</div>';
			$_JCMS->tpl->tag("{PAGE_TEXT}", $text);
			ob_clean();
			echo $_JCMS->tpl->compile("PAGE_SITEMAP");

		}else
		if( $_JCMS->query[0] == 'sitemap.xml' ){
			// доступ разрешен только для поисковых систем
			$bot_name_mask = "/(Yandex|Googlebot|StackRambler|Yahoo|WebAlta|msnbot)/i";
			if( !preg_match($bot_name_mask, $_SERVER['HTTP_USER_AGENT']) == 1 ) return;
			header("HTTP/1.1 200");
			function _urlencode($a){
				$a = str_replace("&", "&amp;", $a);
				$a = str_replace("'", "&apos;", $a);
				$a = str_replace("\"", "&quot;", $a);
				$a = str_replace(">", "&gt;", $a);
				$a = str_replace("<", "&lt;", $a);
				return $a;
			}
			ob_end_clean();
			header('Content-Type: application/xml');
			echo "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\r\n";
			echo "<urlset xmlns=\"http://www.sitemaps.org/schemas/sitemap/0.9\">\r\n";

			if( $_JCMS->_sitemap['pages'] ){
				$tmp = array('pages'=>$_JCMS->_sitemap['pages']);
				unset($_JCMS->_sitemap['pages']);
				if( !$tmp['pages']['items'] ) $tmp['pages']['items'] = array();
				$_JCMS->_sitemap = array_merge_recursive($tmp['pages']['items'], $_JCMS->_sitemap);
			}
			$tree = $this->genSitemap_build($_JCMS->_sitemap,1);
			echo $tree;
			echo "</urlset>\r\n";	
			exit();	
		}		
	}
	
	function send_email($to, $subject, $text, $attach=false){
		if( empty($to) || !filter_var($to, FILTER_VALIDATE_EMAIL) || empty($subject) || empty($text) ){ return false; }
		include(ROOT_PATH.'/include/phpmailer/class.phpmailer.php');
		$mail = new PHPMailer();
		try {
		  $mail->Host = $this->getConfig('smtp_server');
		  $mail->Port   = $this->getConfig('smtp_port');
		  if( $mail->Port == 465 ) $mail->SMTPSecure = 'ssl';
		  $mail->Username   = $this->getConfig('smtp_username');
		  $mail->Password   = $this->getConfig('smtp_password');
		  $mail->SetFrom($this->getConfig('smtp_from_email'), $this->getConfig('smtp_from_name'));
		  $mail->AddAddress($to); 
		  $mail->Subject = iconv("utf-8",'cp1251',$subject);
		  $mail->MsgHTML(iconv("utf-8",'cp1251',$text)); 
		  $mail->SMTPDebug = 9;
		  $mail->Debugoutput = 9; 

 			if( is_array($attach) ){
				foreach($attach as $file){
					/* file = array('name' => '', 'path' => ''); */
					$mail->addAttachment(iconv("utf-8",'cp1251',$file['path']), iconv("utf-8",'cp1251',$file['name']));
				}
			}
		  if( $mail->Send() ) ob_clean();
		  return true;
		} catch (phpmailerException $e) {
		  return $e->errorMessage(); //Pretty error messages from PHPMailer
		} catch (Exception $e) {
		  return $e->getMessage(); //Boring error messages from anything else!
		}  
		return false;
	}
	function escape($arr, $striptags=false){
		if( is_array($arr) ){
			foreach($arr as $key=>$val){
				if(is_array($val)){
					$data[$this->db->escape_string($key)] = $this->escape($val, $striptags); 
					continue; 
				}
				if( $striptags ) $data[$this->db->escape_string($key)] = $this->db->escape_string(strip_tags(trim($val))); else $data[$this->db->escape_string($key)] = $this->db->escape_string(trim($val));
			}
		} else {
			return $this->db->escape_string(strip_tags(trim($arr)));
		}
		return $data;
	}
	function compressOutput(&$body) {
		$search = array(
				'/\>[^\S ]+/s',  // strip whitespaces after tags, except space
				'/[^\S ]+\</s',  // strip whitespaces before tags, except space
				'/(\s)+/s'       // shorten multiple whitespace sequences
			);
		
			$replace = array(
				'>',
				'<',
				'\\1'
			);
		
			$body = preg_replace($search, $replace, $body);
			return $body;
	}
}

interface JCMS_MODULE{
	public function work();
}

// END.
?>