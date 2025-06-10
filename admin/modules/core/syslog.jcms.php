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

class SYSLOG implements JCMS_MODULE_COMPONENT{
	function __construct(){}
	/* инициаизация модуля */ 
	function setup(){
		if( get_class($this) != 'SYSLOG' ) return; // метод вызван из дочерних классов. Этот метод не общий...
		$setup = array();
		$setup["title"]	= "Модуль &laquo;Журнал аудита&raquo;";
		$cat = "core"; 
		$setup["nav"][$cat]['items'][] = array(
			"title" => "Журнал аудита",
			"href" => "core/syslog"
		);
		return $setup;
	}
	
	/* воркер, обрабатывающий запрос к этому модулю */
	function work(){
		global $_JCMS, $_JSON;		
		$json = array();
		$this->add('Просмотр &laquo;Журнала аудита&raquo;', 4);

		// данные для таблицы (AJAX)
		if( $_GET['action'] == 'getTableData' ){
			$res = $this->getTableDataAjax($_POST);
			$res['template'] = trim(ob_get_contents().$res['template']);
			ob_end_clean();
			exit(json_encode($res));
		}

		// данные для таблицы (AJAX)
		if( $_POST['action'] == 'getEvent' &['id'] ){
			$res = $this->getEventByID($_POST);
			$res['template'] = trim(ob_get_contents().$res['template']);
			ob_end_clean();
			exit(json_encode($res));
		}
		
		$json['result'] = 1;
		$json['module_title'] = "Журнал аудита";
				
		$_JCMS->tpl->load('core/syslog.tpl');
		$_JCMS->tpl->tag("{TABLE_DATA}", $table_data);
		$json['template'] = $_JCMS->tpl->compile();
		$json['load_module'] = 'core/syslog';
		$json['callback'] = 'JCMS.modules.syslog.showPage';
		return $json;
	}
	
	function getEventByID(){
		global $_JCMS;
		$id = $_JCMS->db->escape_string(intval($_POST['id']));
		if( $id < 1 ) return;
		$sql_code = "SELECT `jcms2_syslog`.* FROM `jcms2_syslog` WHERE `syslog_id` = '{$id}'";
		if( !$res = $_JCMS->db->query($sql_code) ){ $_JCMS->message("Ошибка базы данных! [php/core.pages#".__LINE__."]", "MYSQL ERROR #".$_JCMS->db->errno." - ".$_JCMS->db->error, 'error'); return; }
		
		if( $res->num_rows === 1 ){
			$data = $res->fetch_assoc();
			
			if( !filter_var($data['admin_id'], FILTER_VALIDATE_IP) ){
				$sql_code = "SELECT * FROM `jcms2_admins` WHERE `admin_id` = '{$data[admin_id]}'";
				if( !$_res = $_JCMS->db->query($sql_code) ){ $_JCMS->message("Ошибка базы данных! [php/core.pages#".__LINE__."]", "MYSQL ERROR #".$_JCMS->db->errno." - ".$_JCMS->db->error, 'error'); return $json; }
				if( $_res->num_rows === 1 ){
					$_data = $_res->fetch_assoc();
					$admin_name = '<span class="_tooltip" rel="tooltip" title="<b>ID:</b> '.$_data['admin_id'].'<br /><b>Login:</b> '.$_data['admin_login'].'">'.mb_ucfirst($_data['admin_name']?$_data['admin_name']:$_data['admin_login']).'</span>';					
				} else { $admin_name = '[#'.$data['admin_id'].'] ???'; }
			} else { $admin_name = $data['admin_id']; }
			$data['admin_id'] = $admin_name;
			$data['syslog_date'] = str_replace(" ", "&nbsp;", date("d.m.Y H:i:s",strtotime($data['syslog_date'])));
			
			switch($data['syslog_level']){
				case "1Ошибка": $syslog_level = '<span class="level error"></span>Ошибка'; break;
				case "2Предупреждение": $syslog_level = '<span class="level warning"></span>Предупреждение'; break;
				case "3Уведомление": $syslog_level = '<span class="level notice"></span>Уведомление'; break;
				case "4Прочее": 
				default: $syslog_level = '<span class="level message"></span>Прочее'; break;
			}
			$data['syslog_level'] = $syslog_level;
			return $data;
		} else {
			$_JCMS->message('Ошибка: запрошенная запись не найдена в БД!', '', 'error');
		}
		
	}

	/* выбирает из базы строки, подходящие запросу юзера(с учетом поиска и сортировки) и формирует ответ в формате, подходящем для JS плагина DataTables */
	function getTableDataAjax(){
		global $_JCMS;
		$resp = array();
		$resp['draw'] = strval($_POST['draw']);		
		$resp['error'] = NULL;
		// столбы таблицы в порядке следования в интерфейсе
		$columns = array('syslog_id', 'syslog_level', 'syslog_event', 'syslog_date', 'admin_id');

		$_POST['search']['value'] = trim(strval($_POST['search']['value']));
		if( !empty($_POST['search']['value']) ){ $search = $_JCMS->db->escape_string($_POST['search']['value']);; } else { $search = false; }
		
		$sql_search = $sql_order = "";
		foreach($columns as $key=>$val){
			if( $search ){
				// SQL фрагмент запроса на поиск
				if( !empty($sql_search) ){ $sql_search .= "OR"; }
				if( is_array($val) ){
					$t = '';
					foreach($val as $val2){
						if( !empty($t) ){ $t .= "OR"; }
						$t .= " `".$val2."` LIKE '%".$search."%' ";
					}
					$sql_search .= $t; unset($t);
				} else {
					$sql_search .= " `".$val."` LIKE '%".$search."%' ";					
				}
			}
			if( is_array($_POST['order']) ){
				foreach($_POST['order'] as $arr){
					if( $arr['column'] == $key ){
						// SQL фрагмент запроса на сортировку
						if( !empty($sql_order) ){ $sql_order .= ","; } else { $sql_order = "ORDER BY"; }
						if( is_array($val) ){
							foreach($val as $arr2){
								if( $sql_order != 'ORDER BY' ){ $sql_order .= ","; }
								$sql_order .= " `".$arr2."` ".strtoupper($_JCMS->db->escape_string($arr['dir']))." ";
							}
						} else {
							$sql_order .= " `".$val."` ".strtoupper($_JCMS->db->escape_string($arr['dir']))." ";
						}
					}
					unset($arr);
				}
			}
		}
		$sql_where = NULL;
		if( !empty($sql_search) ){ $sql_where .= $sql_search; unset($sql_search); }
		if( $sql_where ) $sql_where = 'WHERE '.$sql_where;
		$sql_limit = "LIMIT ".intval($_POST['start']).",".intval($_POST['length']);

		$sql_code = "SELECT SQL_CALC_FOUND_ROWS `jcms2_syslog`.* FROM `jcms2_syslog` {$sql_where} {$sql_order} {$sql_limit}";
		if( !$res = $_JCMS->db->query($sql_code) ){ $_JCMS->message("Ошибка базы данных! [php/core.pages#".__LINE__."]", "MYSQL ERROR #".$_JCMS->db->errno." - ".$_JCMS->db->error, 'error'); return $json; }
		
		// всего записей с учетом фильтра, но без LIMIT
		$sql_code = "SELECT FOUND_ROWS() as total_filtered;";
		if( !$_res = $_JCMS->db->query($sql_code) ){ $_JCMS->message("Ошибка базы данных! [php/core.pages#".__LINE__."]", "MYSQL ERROR #".$_JCMS->db->errno." - ".$_JCMS->db->error, 'error'); return $json; } else {
			$_data = $_res->fetch_assoc();
			$total_filtered = $_data['total_filtered'];
		}

		$table_data = array();
		if( $res->num_rows > 0 ){
			// table data
			while( $data = $res->fetch_assoc() ){
				if( !filter_var($data['admin_id'], FILTER_VALIDATE_IP) ){
					$sql_code = "SELECT * FROM `jcms2_admins` WHERE `admin_id` = '{$data[admin_id]}'";
					if( !$_res = $_JCMS->db->query($sql_code) ){ $_JCMS->message("Ошибка базы данных! [php/core.pages#".__LINE__."]", "MYSQL ERROR #".$_JCMS->db->errno." - ".$_JCMS->db->error, 'error'); return $json; }
					if( $_res->num_rows === 1 ){
						$_data = $_res->fetch_assoc();
						$admin_name = '<span class="_tooltip" rel="tooltip" title="<b>ID:</b> '.$_data['admin_id'].'<br /><b>Login:</b> '.$_data['admin_login'].'">'.mb_ucfirst($_data['admin_name']?$_data['admin_name']:$_data['admin_login']).'</span>';					
					} else { $admin_name = '[#'.$data['admin_id'].'] ???'; }
				} else { $admin_name = $data['admin_id']; }
				$syslog_date = str_replace(" ", "&nbsp;", date("d.m.Y H:i:s",strtotime($data['syslog_date'])));
				
				switch($data['syslog_level']){
					case "1Ошибка": $syslog_level = '<span class="level error"></span>Ошибка'; break;
					case "2Предупреждение": $syslog_level = '<span class="level warning"></span>Предупреждение'; break;
					case "3Уведомление": $syslog_level = '<span class="level notice"></span>Уведомление'; break;
					case "4Прочее": 
					default: $syslog_level = '<span class="level message"></span>Прочее'; break;
				}
				$syslog_event = strip_tags(html_entity_decode($data['syslog_event'],ENT_QUOTES,'utf-8'));
				if( mb_strlen($syslog_event, 'utf-8') > 40 ) $event = mb_substr($syslog_event,0,37, 'utf-8').'...'; else $event = $syslog_event;
				$table_data[] = array($data['syslog_id'], $syslog_level, $event, $syslog_date, $admin_name);
			}
		}
		
		// всего записей в таблице
		$sql_code = "SELECT COUNT(`syslog_id`) as total FROM `jcms2_syslog`";
		if( !$res = $_JCMS->db->query($sql_code) ){
			$_JCMS->message("Ошибка базы данных! [php/core.pages#".__LINE__."]", "MYSQL ERROR #".$_JCMS->db->errno." - ".$_JCMS->db->error, 'error');return $json;
		} else {
			$data = $res->fetch_assoc();
			$total = $data['total'];
		}
		
		$resp['recordsTotal'] = $total;
		$resp['recordsFiltered'] = $total_filtered;		
		$resp['data'] = $table_data; unset($table_data);
		
		return $resp;
	}
	
	function add($message, $level=4){
		global $_JCMS;
		if( $_JCMS->auth ) $profile = $_JCMS->auth->getProfile();
		if( $profile && $_JCMS->auth ){ $admin_id = $profile['admin_id']; } else { $admin_id = $_JCMS->auth->getUserIP(); }
		if( !in_array($level, array(1,2,3,4)) ) $level = 4;
		switch($level){
			case 1: $level = "1Ошибка"; break;
			case 2: $level = "2Предупреждение"; break;
			case 3: $level = "3Уведомление"; break;
			case 4:
			default: $level = "4Прочее"; break;
		}
		$message = $_JCMS->db->escape_string($message);
		$sql_code = "INSERT INTO `jcms2_syslog`(`syslog_level`, `syslog_event`, `syslog_date`, `admin_id`) VALUES ('{$level}', '{$message}', CURRENT_TIMESTAMP, '{$admin_id}')";
		if( !$res = $_JCMS->db->query($sql_code) ){
			$_JCMS->message("Ошибка базы данных! [php/core.pages#".__LINE__."]", "MYSQL ERROR #".$_JCMS->db->errno." - ".$_JCMS->db->error, 'error');
			return $json;
		}
	}
}

// END.
?>