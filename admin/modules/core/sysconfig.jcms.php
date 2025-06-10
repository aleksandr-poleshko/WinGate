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

class SYSCONFIG implements JCMS_MODULE_COMPONENT{
	
	function __construct(){}
	
	/* инициаизация модуля */ 
	function setup(){
		$setup = array(); 
		$setup["title"]	= "Модуль &laquo;Конфигурация Jensen CMS&raquo;";
		$cat = "core"; 
		$setup["nav"][$cat]['items'][] = array(
			"title" => "Конфигурация Jensen CMS",
			"href" => "core/sysconfig"
		);
		return $setup; 
	}
	
	
	/* воркер, обрабатывающий запрос к этому модулю */
	function work(){
		global $_JCMS;		
		$json = array();
		$json['module_title'] = "Конфигурация Jensen CMS"; 
		$config_file = $_JCMS->config_file;
		
		if( $_SESSION['JENSENCMS']['saveConfig_result'] == 'success' ){
			$_JCMS->message("Новая конфигурация успешно сохранена!", "Изменения в конфигурации уже вступили в силу.", 'notice');
			unset($_SESSION['JENSENCMS']['saveConfig_result']);
		}
		
		if( file_exists($config_file) ){
			// если файл существует, проверяем на него права на чтение и запись
			$cur_chmod = substr(sprintf('%o', fileperms($config_file)), -3);
			$perms = $this->checkFilePerms($config_file);	
		} else {
			// файл конфигурации не существует, возможно новая установка.
		}

		if( $_POST['form_submit'] == 1 && $perms === true ) return $this->saveConfig($_POST);
		
		if( $perms === false ){
			// проблема с права на файл, уходим...
			$json['result'] = 2;
			$json['load_module'] = "core/sysconfig";
			$json['callback'] = "JCMS.modules.sysconfig.result";
			
			return $json;
		}
		if( $perms === -1 ){ $json['read_only'] = 1; }
		
		$json['result'] = 1;
		
		// загружает все моудли и получаем их страницы конфигурации
		$res = $_JCMS->getAllModules();
		$modules = array();
		// собираем информацию о модулях
		foreach($res as $moduleName=>$modules_arr){ // все модули
			foreach($modules_arr as $subModules_arr){ // модуль
				foreach($subModules_arr as $subModuleName=>$subModule_arr){ // компоненты (подмодули)
					if( $moduleName != $subModuleName || !is_array($subModule_arr) ) continue; // нужен только управляющий компонент
					$data = array();
					if( $moduleName == 'core' ) $title = "Конфигурация ядра Jensen CMS"; else $title = $subModule_arr['title'];
					$data['title'] = $title;
					if( method_exists($_JCMS->$moduleName, 'getConfigPage') ){
						$data['template'] = trim($_JCMS->$moduleName->getConfigPage());
					}
					if( empty($data['template']) ) $data['template']	= "<i>Модуль не имеет настраиваемых параметров</i>";
					if( $moduleName == 'core' ) array_unshift($modules, $data); else $modules[] = $data; // конфигурация ядра первая в списке
				}
			}
		}
		
		// собираем общую страницу
		$body = "";
		foreach($modules as $key=>$val){
			$body .= '<div class="panel panel-default">';
			$body .= 	'<div class="panel-heading">';
			$body .= 		'<h4 class="panel-title">';
			$body .= 			'<a data-toggle="collapse" data-parent="#accordion" href="#panel'.($key+1).'">'.$val['title'].'</a>';
			$body .= 		'</h4>';
			$body .= 	'</div>';
			$body .= 	'<div id="panel'.($key+1).'" class="panel-collapse collapse'.($key==0?' in':'').'">';
			$body .= 		'<div class="panel-body">';
			$body .= 			$val['template'];
			$body .= 		'</div>';
			$body .= 	'</div>';
			$body .= '</div>';
		}
		
		// заменяем теги шаблонизатора, текущими данными из конфига
		$conf = $_JCMS->getConfig();
		if( is_array($conf) ){
			$t = array(); $t2 = array();
			foreach($conf as $key=>$val){
				$t[] = strtoupper("{".$key."}");
				$t2[] = $val;
			}
			$body = str_replace($t, $t2, $body);
		}

		$_JCMS->tpl->load("core/sysconfig.tpl");
		$_JCMS->tpl->tag("{BODY}", $body);		
		
		### СТАТУС САЙТА
		$_JCMS->tpl->tag("{SITE_STATUSES}", '<option value="on"'.($conf['site_status']=='on'?' selected="selected"':'').'>РАБОТАЕТ</option><option value="off"'.($conf['site_status']=='off'?' selected="selected"':'').'>ВЫКЛЮЧЕН</option>');
		###################
		
		### УПРАВЛЕНИЕ МОДУЛЯМИ
		$modules = array();
		foreach($res as $module=>$modules_arr){ // все модули
			$_module = $module;
			if( $_module  == 'core' ){ $_module  = 'jcms_core'; } else { $_module  = "mod_".$_module ; }
			foreach($modules_arr as $subModules_arr){ // модуль
				foreach($subModules_arr as $subModule=>$subModule_arr){ // компоненты (подмодули)
					$nav = array();
					$title = $subModule_arr['title'];
					if( is_array($subModule_arr['nav']) && !empty($subModule_arr['nav']) ){
						foreach($subModule_arr['nav'] as $cat_name=>$nav_cats){ // категории меню
							if( $subModule == $module && !empty($subModule_arr['title']) ){
								$title = $subModule_arr['title'];
							}
							foreach($nav_cats['items'] as $nav_item){ // элементы меню
								$nav[] = $nav_item['title'];
							}
						}
					}
					$m = $module==$subModule?$module:$module.'/'.$subModule;
					$t = array('module'=>$m, 'sysmodule'=>$module.'/'.$subModule, 'submodule'=>$subModule, 'title'=>$title, 'nav'=>$nav, 'status'=>is_array($subModule_arr)?1:$subModule_arr);
					if( $module==$subModule && $module == 'core' ) array_unshift($modules, $t); else $modules[] = $t;
				}
			}
		}
		$mod_list = ""; $def_mod_list="";
		foreach($modules as $val){
			$uniq = uniqid();
			$chk = is_array($conf['sysmodules'])&&isset($conf['sysmodules'][$val['sysmodule']]);
			if( in_array(strtoupper($val['submodule']), $_JCMS->sysmodules) ){ $dis = 1; } else { $dis = 0; }
			$mod_list .= '<input'.($chk||$dis?' checked="checked"':'').' type="checkbox" id="'.$uniq.'" '.($dis?' disabled="disabled"':' name="sysmodules['.$val['sysmodule'].']" value="1"').'> <label for="'.$uniq.'">'.($val['status']!=1?$val['module'].'.jcms.php':($val['title'].' ['.$val['module'].']')).'</label><br />';

			foreach($val['nav'] as $val2){
				$selected= $val['sysmodule']==$_JCMS->getConfig('def_module')?' selected="selected"':'';
				$def_mod_list .= '<option value="'.$val['sysmodule'].'"'.$selected.'>'.$val2.' ['.$val['module'].']</option>';
			}
		}
		$_JCMS->tpl->tag("{MODULES_CONTROL}", $mod_list);
		$_JCMS->tpl->tag("{DEF_MODULES}", $def_mod_list);
		###################
		$show_php_errors_mod_list = '<option value="1"'.($_JCMS->getConfig('show_php_errors')==1?' selected="selected"':'').'>ДА</option>';
		$show_php_errors_mod_list .= '<option value="0"'.($_JCMS->getConfig('show_php_errors')!=1?' selected="selected"':'').'>НЕТ</option>';
		$_JCMS->tpl->tag("{SHOW_PHP_ERROR}", $show_php_errors_mod_list);
		###################
		$minify_output = '<option value="1"'.($_JCMS->getConfig('minify_output')==1?' selected="selected"':'').'>ДА</option>';
		$minify_output .= '<option value="0"'.($_JCMS->getConfig('minify_output')!=1?' selected="selected"':'').'>НЕТ</option>';
		$_JCMS->tpl->tag("{MINIFY_OUTPUT_}", $minify_output);
 		###################
		$_JCMS->tpl->tag("{HOSTNAME}", $_SERVER['HTTP_HOST']);	
		$_JCMS->tpl->tag("{HOME_URL}", $_JCMS->getConfig('site_url'));
		$_JCMS->tpl->tag("{__CURRENT_IP__}", $_JCMS->auth->getUserIP());

		$json['template'] = $_JCMS->tpl->compile();
		/* удаляем все необработанные теги шаблонизатора (несуществующий параметр в конифгурации, например для только что установленных модулей) */
		$json['template'] = preg_replace("/\{[\w-]+\}/i", "", $json['template']);
		$json['load_module'] = "core/sysconfig";
		$json['callback'] = "JCMS.modules.sysconfig.showPage";
		return $json;
	}
	
	function checkFilePerms($file){
		global $_JCMS;
		// проверяем права на чтение
		if( !is_readable($file) ){
			$f = str_replace($_SERVER['DOCUMENT_ROOT'], '', $file);
			$_JCMS->message("ВНИМАНИЕ! Файл конфигурации (<tt>".$f."</tt>) не доступен для чтения!", "Невозможно загрузить текущую конфигурацию.<br />Для просмотра и изменения конфигурации, установите на файл <tt>".$f."</tt> права на чтение и запись (chmod 666).", 'error');
			return false;
		} else 
		// проверяем права на запись
		if( !is_writable($file) ){
			$f = str_replace($_SERVER['DOCUMENT_ROOT'], '', $file);
			$_JCMS->message("ВНИМАНИЕ! Файл конфигурации (<tt>".$f."</tt>) не доступен для изменения!", "Сейчас доступен только просмотр текущей конфигурации, без возможности внесения изменений.<br />Для изменения конфигурации, установите на файл <tt>".$f."</tt> права на чтение и запись (chmod 666).", 'error');
			return -1;
		}
		return true;
	}
	
	
	function saveConfig(){
		global $_JCMS;
		$json = array();
		$json['result'] = 1;
		unset($_POST['form_submit']);
		// удаляем пробелы...
		foreach($_POST as $key=>$val){
			$_POST[$key]=array_trim($val);
		}
		// опрашиваем модули для согласования валидности нового конфига
		$res = $_JCMS->getAllModules();
		$errors = array(); // список ошибок в конфиге
		foreach($res as $moduleName=>$modules_arr){ // все модули
			foreach($modules_arr as $subModules_arr){ // модуль
				foreach($subModules_arr as $subModuleName=>$subModule_arr){ // компоненты (подмодули)
					if( $moduleName != $subModuleName ) continue; // нужен только управляющий компонент
					// проверяем, существует ли функция проверки конфигу у этого модуля
					if( method_exists($_JCMS->$moduleName, 'checkConfig') ){
						$_JCMS->$moduleName->checkConfig($errors);
					} else {
						// функция не сущесвует, ничего не проверяем... Переходим к следующему...
					}
				}
			}
		}
		
		if( !empty($errors) ){
			// в конфиге есть ошибки, выводим их...
			$msg = "";
			foreach($errors as $err){
				$msg .= "<li>{$err}</li>";
			}
			$_JCMS->message("Изменения не сохранены, обнаружены следующие ошибки:", "<ol style=\"margin-bottom:0px;\">{$msg}</ol>", 'error');
			return $json; // прерываем работу...
		}
		
		// сохраняем новый конфиг в файл
		
		
$new_conf = "<?PHP\n/*  =================================  ##\n##              Jensen CMS 2           ##\n##  =================================  ##\n##          Copyright (c) 2015         ##\n##         www.JensenStudio.net        ##\n##  =================================  ##\n##   WWW: www.JensenStudio.net         ##\n##   EMAIL: support@JensenStudio.net   ##\n##  =================================  */\n\nif( !defined(\"JENSENCMS2\") ) { header(\"HTTP/1.1 403\"); exit(\"Hacking attempt!\"); }\n\n/* * * * * * * * * * * * * * * * * *\n * ФАЙЛ СГЕНЕРИРОВАН АВТОМАТИЧЕСКИ *\n * НИЧЕГО НЕ ИЗМЕНЯТЬ В ЭТОМ ФАЙЛЕ *\n * * * * * * * * * * * * * * * * * */\n\n\$conf = ".var_export($_POST, 1).";\n\n// END.\n?>";

		$f = str_replace($_SERVER['DOCUMENT_ROOT'], '', $_JCMS->config_file);
		if( !$fp = fopen($_JCMS->config_file, 'w') ){
			// ошибка открытия файла на запись
			$_JCMS->message("Ошибка: не удалось открыть файл <tt>{$f}</tt> для записи!", "Изменения конфигурации не были сохранены! Произошла неизвестная ошибка.", 'error');			
		} else {
			if( fwrite($fp, $new_conf) !== false ){
				// успешно записано
				$_SESSION['JENSENCMS']['saveConfig_result'] = $json['saveConfig_result'] = 'success';
			} else {
				// ошибка записи в файл
				$_JCMS->message("Ошибка: не удалось записать изменения в файл <tt>{$f}</tt>!", "Изменения конфигурации не были сохранены! Произошла неизвестная ошибка.", 'error');
			}			
			fclose($fp);
		}
		
		return $json;
	}
}

// END.
?>