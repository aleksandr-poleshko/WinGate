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
	protected $config = array();
	var $err_loadConf = false;
	var $config_file = NULL;
	var $sysmodules = array('CORE', 'AUTH', 'DATABASE', 'URL', 'PROFILE');
	private $load_modules=array();

	final function __construct(){
		$this->config_file = realpath(ROOT_PATH."/../config")."/jcms.config.php";
	}
	final function getVersion($plain_text=false){
		$version = 'v.2.2.16';
		return $plain_text?strip_tags($version):$version;
	}
	/* 
	 * Инициализация основных компонентов JensenCMS
	 */
	final function init(){	
		global $_JCMS;	
		/* Шаблонизатор */
		include_once(ROOT_PATH."/../include/templates.class.php");
		$this->tpl = new TPL(ROOT_PATH.'/modules/');
		/* ЧПУ */
		$this->load_module('core/url',1);
		$_JCMS->query = $_JCMS->modules['URL']->work();
		/* БД */
		$this->load_module('core/database',1);
		if( !include_once(ROOT_PATH."/../config/db.config.php") ){ $_JCMS->message("Ошибка: не удалось загрузить файл конфигурации базы данных! [php/core.init#".__LINE__."]", "", 'error'); }
		$_JCMS->db = $_JCMS->modules['DATABASE']->connect($db_conf['hostname'], $db_conf['username'], $db_conf['password'], $db_conf['database']);
		unset($db_conf); 
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
		if( !in_array('JCMS_MODULE', $interfaces) && !in_array('JCMS_MODULE_COMPONENT', $interfaces) && $mod != __CLASS__ ){
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
			if( $json == NULL || $json == false ) return false;
			if( is_array($json) ){ $_JSON = array_merge_recursive($_JSON, $json); } else { $_JSON[] = $json; }
		}
		return true; 
	}
	
	/* сканируется папку и возвращает список всех доступных модулей и доп. компонентов модулей */
	final function getAllModules(){
		$modules = array();
		$path = ROOT_PATH.'/modules/';
		$scandir = scandir($path);
		foreach($scandir as $val){
			if( !is_dir($path.$val) || in_array($val, array('.','..'))  ){ continue; }
			// загрузка модуля и доп. компонентов модуля, если есть...
			$this->getAllSubModules($val, $modules);
		}
		return $modules;
	}
	
	/* сканирует папку указанного модуля и загружает все его компоненты (если они есть) */
	final protected function getAllSubModules($module, &$modules){
		global $_JCMS;
		$path = ROOT_PATH.'/modules/'.$module.'/';
		$scandir = scandir($path);
		$allowModules = $_JCMS->getConfig('sysmodules');
		foreach($scandir as $val){
			if( !is_file($path.$val) || in_array($val, array('.','..')) || !preg_match('/\.jcms\.php$/',$val) ){ continue; }
			$mod = strtoupper(basename($val, '.jcms.php'));
			$_mod = strtolower($mod);
			if( !$allowModules[strtolower($module.'/'.$_mod)] && !in_array($mod, $_JCMS->sysmodules) ){ $modules[$module][][$_mod] = -2; continue; } // модуль не разрешен к запуску
			if( !$this->load_module($module.'/'.$_mod, 1) ){ $modules[$module][][$_mod] = -1; continue; } // ошибка загрузки модуля

			if( $mod == 'CORE' ) $mod = 'JENSENCMS';
			if( method_exists($_JCMS->modules[$mod], 'setup') ){
				$setup = $_JCMS->modules[$mod]->setup();
			} else {
				$setup = NULL;
			}
			$modules[$module][][$_mod] = $setup;
		}
		
		return $modules;
	} 
	
	/* генерация данных для интерфейса*/
	final function load_interface(){
		global $_JCMS, $_JSON;
		$json = array();
		if( empty($_SESSION["JENSENCMS"]['AUTH']) ) return false;
		if( $_POST['load_interface'] != 1 ){ return true; }
		$sess = $_SESSION["JENSENCMS"]['AUTH'];
		$_JCMS->tpl->load("core/userbar.main.tpl");
		$_JCMS->tpl->tag("{SITE_URL}", $this->getConfig('site_url'));
		$_JCMS->tpl->tag("{USERNAME}", (!empty($sess['admin_name'])?$sess['admin_name']:$sess['admin_login']));
		$json['userbar_template'] = $_JCMS->tpl->compile();
		
		/* +++ ГЕНЕРАЦИЯ МЕНЮ +++ */
		$res = $this->getAllModules();
		$_nav = array();
		foreach($res as $module=>$modules_arr){ // все модули
			foreach($modules_arr as $subModules_arr){ // модуль
				foreach($subModules_arr as $subModule=>$subModule_arr){ // компоненты (подмодули)
					$tmp = array();
					$perms = $_JCMS->auth->getProfile(); $perms = $perms['permission'];
					$mname = $module==$subModule?'module/'.$module:$module.'/'.$subModule;
					if( !($module=='core'&&in_array(strtoupper($subModule), $this->sysmodules)) ){ // для системных модулей, права не проверяем
						if( !$perms[$mname] || !in_array("init", $perms[$mname]) ){
							continue;
						}
					}
					if( !is_array($subModule_arr['nav']) || empty($subModule_arr['nav']) ) continue;
					foreach($subModule_arr['nav'] as $cat_name=>$nav_cats){ // категории меню
						foreach($nav_cats['items'] as $nav_item){ // элементы меню
							$_nav[$cat_name]['items'][] = array('title'=>$nav_item['title'], 'href'=>$nav_item['href']);
						}
						// Фиксированный заголовок для системных разделов меню (core и module)
						if( $cat_name == 'core' ){
							$_nav[$cat_name]['title'] = 'Jensen CMS';
						} elseif( $cat_name == 'module' ){
							$_nav[$cat_name]['title'] = 'Модули';
						} else {
							$_nav[$cat_name]['title'] = $nav_cats['title'];
						}
					}
				}
			}
		}
		$nav = "<ul class=\"nav navbar-nav\">";
		foreach($_nav as $cats){ // категории
			$nav .= '<li class="dropdown">';
			$nav .= '<a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">'.$cats['title'].' <span class="caret"></span></a>';
			$nav .= '<ul class="dropdown-menu">';
			foreach($cats['items'] as $item){ // элементы
				$item['href'] = PATH.($item['href'][0]=='/'||PATH=='/'?'':'/').$item['href']; // делаем корректный путь, добавляя к ссылке смещение пути скрипта относительно корня сайта
				$nav .= '<li><a href="'.$item['href'].'">'.$item['title'].'</a></li>';
			}
			$nav .= '</ul>';
			$nav .= '</li>';
		}
		$nav .= "</ul>";
		/* --- -------------- --- */
		$json['menu_template'] = $nav;
		$json['callback'] = 'JCMS.load_interface';
		if( is_array($json) ){ $_JSON = array_merge_recursive($_JSON, $json); } elseif( !empty($json) ){ $_JSON[] = $json; }
		return true;
	}
	
	final function message($title, $text, $type='info', $return=false){
		switch($type){
			case "error": $type = "danger"; break;
			case "warning": $type = "warning"; break;
			case "notice": $type = "success"; break;
			case "info": $type = "info"; break;
			default: $type="info";	
		}
		$template = '<div class="alert alert-'.$type.' fade in"><a href="#" class="close" data-dismiss="alert" title="Закрыть">&times;</a><b>'.$title.'</b><br />'.$text.'</div>';
		if( $return !== false ){
			return $template;
		} else {
			echo $template;
		} 
	}
	
	final function phpErrorHandler($errno, $errstr, $errfile, $errline){
/*		if (!(error_reporting() & $errno)) { return; }*/
		if( $errno == E_NOTICE ) return;
		$errfile = str_replace($_SERVER['DOCUMENT_ROOT'], '',$errfile); // убираем абсолюный путь к файлам
		$errstr = str_replace($_SERVER['DOCUMENT_ROOT'], '',$errstr); // убираем абсолюный путь к файлам
		$array = array(E_ERROR => "error", E_WARNING => 'warning', E_NOTICE => 'info');
		if( in_array($errno, $array) ){ $type = $array[$errno]; } else { $type= 'info';  }
		$this->message("PHP ".strtoupper(str_replace(array("E_","_"),"",$this->FriendlyPhpErrorType($errno))).": {$errstr}.","<i>Строка {$errline} файла <tt>&lt;{$errfile}&gt;</tt>.</i>", $type);
		return false;
	}
	
	final function FriendlyPhpErrorType($type){ 
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
	
	final function setup(){
		if( get_class($this) != 'JENSENCMS' ) return; // метод вызван из дочерних классов. Этот метод не общий...
		$setup["title"] = "Ядро Jensen CMS (".$this->getVersion(1).")";
		return $setup;
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

	final function getConfigPage(){
		global $_JCMS;		
		$_JCMS->tpl->load('core/core.config.tpl');
		$path = explode('/',PATH);array_pop($path);;
		$_JCMS->tpl->tag("{DEF_SITE_URL}", 'http://'.$_SERVER['HTTP_HOST'].'/'.implode('',$path));
		return $_JCMS->tpl->compile();
	}
	
	/* Проверяет параметры конфигурации модуля на валидность, перед сохранением в конфигурационном файле */
	final function checkConfig(&$errors){
		global $_JCMS;
		if( get_class($this) != 'JENSENCMS' ) return; // метод вызван из дочерних классов. Этот метод не общий...
		if( empty($_POST['site_title']) ){ $_POST['site_title'] = $_SERVER['HTTP_HOST']; } // default
		if( empty($_POST['site_url']) ){ $path = explode('/',PATH);array_pop($path);$_POST['site_url'] = 'http://'.$_SERVER['HTTP_HOST'].'/'.implode('',$path); } // default
		// проверяем URL сайта, должен быть (http|https)://domain/path
		$t = parse_url($_POST['site_url']);
		if( !$t || !isset($t['scheme'], $t['host']) ){
			$errors[] = "поле &laquo;Полный URL сайта&raquo; не заполнено или заполнено не правильно";
		} else {
			$t['path'] = preg_replace("/\/\//", "", $t['path']);
			if( mb_substr($t['path'], -1,1, 'utf-8') == '/' ) $t['path'] = mb_substr($t['path'], 0, -1,'utf-8');
			$_POST['site_url'] = (empty($t['scheme'])||!in_array($t['scheme'],array('http','https'))?'http':$t['scheme']).'://'.(empty($t['host'])?$_SERVER['HTTP_HOST']:$t['host']).(empty($t['path'])||$t['path']=='/'?'':$t['path']);
		}
		// --
		if( !empty($_POST['site_description']) && mb_strlen($_POST['site_description'],'utf-8') > 200 ){ $_POST['site_description'] = substr($_POST['site_description'],0,200); } // max_len=200

		### УПРАВЛЕНИЕ МОДУЛЯМИ
		if( empty($_POST['sysmodules']) ){
			$_POST['sysmodules'] = array(); // default 
		}	
		$res = $this->getAllModules();
		$modules = array();
		foreach($res as $module=>$modules_arr){ // все модули
			foreach($modules_arr as $subModules_arr){ // модуль
				foreach($subModules_arr as $subModule=>$subModule_arr){ // компоненты (подмодули)
					$modules[$module.'/'.$subModule] = 1;
				}
			}
		}
		$t = array();
		foreach($modules as $module=>$arr){
			if( $_POST['sysmodules'][$module] == 1 ){
				$t[$module] = 1;
			}
		}
		$_POST['sysmodules'] = $t; unset($t);
		###################
		
	}
	function send_email($to, $subject, $text){
		if( empty($to) || !filter_var($to, FILTER_VALIDATE_EMAIL) || empty($subject) || empty($text) ){ return false; }
		        
		if( !class_exists('MultipleInterfaceMailer') ) include_once(ROOT_PATH.'/../include/phpmailer/class.phpmailer.php');
		$mail = new MultipleInterfaceMailer(true);
		$mail->IsSMTPX($this->getConfig('smtp_server'));
		try {
		  $mail->Port   = $this->getConfig('smtp_port');
		  $mail->Username   = $this->getConfig('smtp_username');
		  $mail->Password   = $this->getConfig('smtp_password');
		  $mail->SetFrom($this->getConfig('smtp_from_email'), $this->getConfig('smtp_from_name'));
		  $mail->AddAddress($to); 
		  $mail->Subject = iconv("utf-8",'cp1251',$subject);
		  $mail->MsgHTML(iconv("utf-8",'cp1251',$text));
		  $mail->SMTPDebug = 0;
		  $mail->Debugoutput = 0;
		  $mail->Send();
		  return true;
		} catch (phpmailerException $e) {
		  return $e->errorMessage(); //Pretty error messages from PHPMailer
		} catch (Exception $e) {
		  return $e->getMessage(); //Boring error messages from anything else!
		}  
		return false;
	}
	function compressOutput(&$body) {
		//remove redundant (white-space) characters
		$replace = array(
			//remove tabs before and after HTML tags
			'/\>[^\S ]+/s'   => '>',
			'/[^\S ]+\</s'   => '<',
			//shorten multiple whitespace sequences; keep new-line characters because they matter in JS!!!
			'/([\t ])+/s'  => ' ',
			//remove leading and trailing spaces
			'/^([\t ])+/m' => '',
			'/([\t ])+$/m' => '',
			// remove JS line comments (simple only); do NOT remove lines containing URL (e.g. 'src="http://server.com/"')!!!
			'~//[a-zA-Z0-9 ]+$~m' => '',
			//remove empty lines (sequence of line-end and white-space characters)
			'/[\r\n]+([\t ]?[\r\n]+)+/s'  => "\n",
			//remove empty lines (between HTML tags); cannot remove just any line-end characters because in inline JS they can matter!
			'/\>[\r\n\t ]+\</s'    => '><',
	/*        //remove "empty" lines containing only JS's block end character; join with next line (e.g. "}\n}\n</script>" --> "}}</script>" */
			'/}[\r\n\t ]+/s'  => '}',
			'/}[\r\n\t ]+,[\r\n\t ]+/s'  => '},',
			//remove new-line after JS's function or condition start; join with next line
			'/\)[\r\n\t ]?{[\r\n\t ]+/s'  => '){',
			'/,[\r\n\t ]?{[\r\n\t ]+/s'  => ',{',
			//remove new-line after JS's line end (only most obvious and safe cases)
			'/\),[\r\n\t ]+/s'  => '),',
			//remove quotes from HTML attributes that does not contain spaces; keep quotes around URLs!
			'~([\r\n\t ])?([a-zA-Z0-9]+)="([a-zA-Z0-9_/\\-]+)"([\r\n\t ])?~s' => '$1$2=$3$4', //$1 and $4 insert first white-space character found before/after attribute
		);
		$body = preg_replace(array_keys($replace), array_values($replace), $body);
	 
		//remove optional ending tags (see http://www.w3.org/TR/html5/syntax.html#syntax-tag-omission )
		$remove = array(
			'</option>', '</li>', '</dt>', '</dd>', '</tr>', '</th>', '</td>'
		);
		$body = str_ireplace($remove, '', $body);
		return $body;
	}
}

interface JCMS_MODULE{
	public function setup();
	public function work();
	public function getConfigPage();
	public function checkConfig(&$errors);
}
interface JCMS_MODULE_COMPONENT{
	public function setup();
	public function work();
}

// END.
?>