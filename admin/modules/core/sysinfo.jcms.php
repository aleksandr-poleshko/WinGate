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

class SYSINFO implements JCMS_MODULE_COMPONENT{
	function __construct(){}
	/* инициаизация модуля */ 
	function setup(){
		if( get_class($this) != 'SYSINFO' ) return; // метод вызван из дочерних классов. Этот метод не общий...
		$setup = array();
		$setup["title"]	= "Модуль &laquo;Информация о системе&raquo;";
		$cat = "core"; 
		$setup["nav"][$cat]['items'][] = array(
			"title" => "Информация о системе",
			"href" => "core/sysinfo"
		);
		return $setup;
	}
	
	/* воркер, обрабатывающий запрос к этому модулю */
	function work(){
		global $_JCMS, $_JSON;		
		$json = array();
		
		$json['result'] = 1;
		$json['module_title'] = "Информация о системе";
		
		$table_data = "";
		$res = $_JCMS->getAllModules();
		foreach($res as $module=>$modules_arr){ // все модули
			$_module = $module;
			$submodules = "";
			if( $_module  == 'core' ){ $_module  = 'jcms_core'; } else { $_module  = "mod_".$_module ; }
			foreach($modules_arr as $subModules_arr){ // модуль
				foreach($subModules_arr as $subModule=>$subModule_arr){ // компоненты (подмодули)
					if( !is_array($subModule_arr) ) continue 3;
					if( $subModule == $module ){
						$title = $subModule_arr['title'];
						$descr = $subModule_arr['descr'];
					} else {
						if( !empty($submodules) ){ $submodules .= ", "; }
						if( !empty($subModule_arr['title']) ){
							$submodules .= "<span class=\"_tooltip\" rel=\"tooltip\" title=\"".html_entity_decode(strip_tags($subModule_arr['title']),ENT_QUOTES,'utf-8')."\">".strtoupper($subModule)."</span>";
						} else {
							$submodules .= strtoupper($subModule);							
						}
					}
					if( is_array($subModule_arr['nav']) && !empty($subModule_arr['nav']) ){
						foreach($subModule_arr['nav'] as $cat_name=>$nav_cats){ // категории меню
							// Фиксированный заголовок для системных разделов меню (core и module)
							if( $cat_name == 'core' ){
								$nav_cats['title'] = 'Jensen CMS';
							} elseif( $cat_name == 'module' ){
								$nav_cats['title'] = 'Модули';
							}
							foreach($nav_cats['items'] as $nav_item){ // элементы меню
								if( !empty($nav) ) $nav .= "<br />";
								$nav .= $nav_cats['title']." &rarr; ".$nav_item['title'];
							}
						}
					}
				}
			}
			$table_data .= "<tr>";
			$_title = empty($title)?strtoupper($_module):"<span class=\"_tooltip\" rel=\"tooltip\" title=\"".html_entity_decode(strip_tags($title),ENT_QUOTES,'utf-8')."\">".strtoupper($_module)."</span>";
			$table_data .= "<td>".$_title."</td>";			
			$_descr = (empty($descr)?$title:$descr);
			$table_data .= "<td".(empty($_descr)?" align=\"center\">&mdash;":'>'.$_descr)."</td>";
			$table_data .= "<td".(empty($submodules)?" align=\"center\">&mdash;":">".$submodules)."</td>";
			$table_data .= "<td".(empty($nav)?" align=\"center\">&mdash;":">".$nav)."</td>";
			$table_data .= "</tr>";
			unset($_descr, $subModules, $nav);
		}

		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $_JCMS->getConfig('site_url').'/admin/');
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); 
		curl_setopt($ch, CURLOPT_ENCODING, '');
		curl_setopt($ch, CURLOPT_HEADER, 1);
		curl_setopt($ch, CURLOPT_NOBODY, 1);
		$t = curl_exec($ch);
		curl_close($ch);
		if( preg_match('#Content-Encoding:.*?gzip.*?#ism', $t) ){ $gzip = 1; } else { $gzip = 0; }
		
		$_JCMS->tpl->load('core/sysinfo.tpl');
		$_JCMS->tpl->tag("{JENSENCMS_VERSION}", $_JCMS->getVersion());
		$_JCMS->tpl->tag("{PHP_VERSION}", phpversion()<5.3?'<b style="color:red;">'.phpversion().' (!)</b>':'<span style="color:green;">'.phpversion().'</span>');
		$_JCMS->tpl->tag("{MYSQL_VERSION}", $_JCMS->db->server_info<5.1?'<b style="color:red;">'.$_JCMS->db->server_info.' (!)</b>':'<span style="color:green;">'.$_JCMS->db->server_info.'</span>');
		$gd_version = $this->gd_version();
		$_JCMS->tpl->tag("{GD_VERSION}", $gd_version?'<span style="color:green;">'.$gd_version.'</span>':'<b style="color:red;">не установлена (!)</b>');
		$_JCMS->tpl->tag("{GZIP_STATUS}", $gzip?'<span style="color:green;">работает</span>':'<b style="color:red;">не работает (!)</b>');
		$_JCMS->tpl->tag("{PHP_SAFEMODE}", !ini_get('safe_mode')?'<span style="color:green;">выключено</span>':'<b style="color:red;">включено (!)</b>');
		$_JCMS->tpl->tag("{PHP_REGGLOB}", !ini_get('register_globals')?'<span style="color:green;">выключено</span>':'<b style="color:red;">включено (!)</b>');
		$_JCMS->tpl->tag("{PHP_MAGQUOTESGPC}", !ini_get('magic_quotes_gpc')?'<span style="color:green;">выключено</span>':'<b style="color:red;">включено (!)</b>');
		$_JCMS->tpl->tag("{PHP_MAGQUOTESRNT}", !ini_get('magic_quotes_runtime')?'<span style="color:green;">выключено</span>':'<b style="color:red;">включено (!)</b>');
		$_JCMS->tpl->tag("{PHP_MAGQUOTESSB}", !ini_get('magic_quotes_sybase')?'<span style="color:green;">выключено</span>':'<b style="color:red;">включено (!)</b>');
		$http_mod_rewrite = (function_exists('apache_get_modules')) ? ((array_search("mod_rewrite", apache_get_modules())) ? 1 : 0) : 0;
		$_JCMS->tpl->tag("{HTTPD_MODREWRITE}", $http_mod_rewrite==1?'<span style="color:green;">включено</span>':'<b style="color:red;">выключено (!)</b>');
		$_JCMS->tpl->tag("{SERVER_TIME}", date('d.m.Y H:i:s <\i>(P, e)</\i>'));
		$_JCMS->tpl->tag("{SERVER_OS}", php_uname("s").' '.php_uname("r"));
		$ram = abs(round(memory_get_usage()/convertToBytes(ini_get('memory_limit'))*100-100,2));
		$_JCMS->tpl->tag("{RAM}", ($ram<5?'<b style="color:red;">'.$ram.'% (!)</b>':'<span style="color:green;">'.$ram.'%</span>').'  <i>(занято '.convertFromBytes(memory_get_usage()). ', всего '.convertFromBytes(convertToBytes(ini_get('memory_limit'))).')</i>');
		$hdd = abs(round(disk_free_space(ROOT_PATH.'/..')/disk_total_space(ROOT_PATH.'/..')*100-100,2));
		$_JCMS->tpl->tag("{HDD}", ($hdd<5?'<b style="color:red;">'.$hdd.'% (!)</b>':'<span style="color:green;">'.$hdd.'%</span>').' <i>(занято '.convertFromBytes(disk_free_space(ROOT_PATH.'/..')). ', всего '.convertFromBytes(disk_total_space(ROOT_PATH.'/..')).')</i>');
		$_JCMS->tpl->tag("{TABLE_DATA}", $table_data);
		$json['template'] = $_JCMS->tpl->compile();
		$json['load_module'] = 'core/sysinfo';
		$json['callback'] = 'JCMS.modules.sysinfo.showPage';
		return $json;
	}
	function gd_version() { ob_start(); phpinfo(8); $module_info = ob_get_contents(); ob_end_clean(); if (preg_match("/\bgd\s+version\b[^\d\n\r]+?([\d\.]+)/i", $module_info, $matches)) {$gdversion = $matches[1];} else { $gdversion = false; } return $gdversion; }

}

// END.
?>