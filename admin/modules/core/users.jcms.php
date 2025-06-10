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

class USERS implements JCMS_MODULE_COMPONENT{
	function __construct(){}
	/* инициаизация модуля */ 
	function setup(){
		if( get_class($this) != 'USERS' ) return; // метод вызван из дочерних классов. Этот метод не общий...
		$setup = array();
		$setup["title"]	= "Модуль &laquo;Пользователи и политика доступа&raquo;";
		$cat = "core"; 
		$setup["nav"][$cat]['items'][] = array(
			"title" => "Пользователи и группы",
			"href" => "core/users"
		);
		return $setup;
	}
	
	/* воркер, обрабатывающий запрос к этому модулю */
	function work(){
		global $_JCMS;
		$json = array();
		// создание страницы
		if( $_JCMS->query[2] == 'add_user' ){ return $this->showPage_addUser(); }		
		if( $_JCMS->query[2] == 'add_group' ){ return $this->showPage_addGroup(); }		

		// изменение страницы
		if( $_JCMS->query[2] == 'edit' ){		
			if( $_POST['action'] == 'deleteUser' ){
				$json = $this->actionUserDelete();
			} else
			if( $_POST['action'] == 'deleteGroup' ){
				$json = $this->actionGroupDelete();
			} else {
				if( $_GET['user_id'] ){ return $this->showPage_editUser(); } else
				if( $_GET['group_id'] ){ return $this->showPage_editGroup(); }
			}
			return $json;
		}		
		// данные для таблицы 1 (AJAX)
		if( $_GET['action'] == 'getTable1Data' ){
			$res = $this->getTable1DataAjax($_POST);
			$res['template'] = trim(ob_get_contents().$res['template']);
			ob_end_clean();
			exit(json_encode($res));
		}

		// данные для таблицы 2 (AJAX)
		if( $_GET['action'] == 'getTable2Data' ){
			$res = $this->getTable2DataAjax($_POST);
			$res['template'] = trim(ob_get_contents().$res['template']);
			ob_end_clean();
			exit(json_encode($res));
		}

		if( $_SESSION['JENSENCMS']['mod_users']['addUser_success'] > 0 ){
			$id = $_SESSION['JENSENCMS']['mod_users']['addUser_success'];
			$_JCMS->message('Создание аккаунта #'.$id.' &ndash; успешно выполнено!', '', 'notice');
			unset($_SESSION['JENSENCMS']['mod_users']['addUser_success']);
		}
		if( $_SESSION['JENSENCMS']['mod_users']['editUser_success'] > 0 ){
			$id = $_SESSION['JENSENCMS']['mod_users']['editUser_success'];
			$_JCMS->message('Редактирование аккаунта #'.$id.' &ndash; успешно выполнено!', '', 'notice');
			unset($_SESSION['JENSENCMS']['mod_users']['editUser_success']);
		}		
		if( $_SESSION['JENSENCMS']['mod_users']['deleteUser_success'] > 0 ){
			$id = $_SESSION['JENSENCMS']['mod_users']['deleteUser_success'];
			$_JCMS->message('Удаление аккаунта #'.$id.' &ndash; успешно выполнено!', '', 'notice');
			unset($_SESSION['JENSENCMS']['mod_users']['deleteUser_success']);
		}

		if( $_SESSION['JENSENCMS']['mod_users']['addGroup_success'] > 0 ){
			$id = $_SESSION['JENSENCMS']['mod_users']['addGroup_success'];
			$_JCMS->message('Создание группы #'.$id.' &ndash; успешно выполнено!', '', 'notice');
			unset($_SESSION['JENSENCMS']['mod_users']['addGroup_success']);
		}
		if( $_SESSION['JENSENCMS']['mod_users']['editGroup_success'] > 0 ){
			$id = $_SESSION['JENSENCMS']['mod_users']['editGroup_success'];
			$_JCMS->message('Редактирование группы #'.$id.' &ndash; успешно выполнено!', '', 'notice');
			unset($_SESSION['JENSENCMS']['mod_users']['editGroup_success']);
		}		
		if( $_SESSION['JENSENCMS']['mod_users']['deleteGroup_success'] > 0 ){
			$id = $_SESSION['JENSENCMS']['mod_users']['deleteGroup_success'];
			$_JCMS->message('Удаление группы #'.$id.' &ndash; успешно выполнено!', '', 'notice');
			unset($_SESSION['JENSENCMS']['mod_users']['deleteGroup_success']);
		}
		
		$_JCMS->tpl->load('core/usersView.tpl');
		$json['result'] = 1;
		$json['module_title'] = 'Пользователи и группы';
		$json['template'] = $_JCMS->tpl->compile();;
		$json['load_module'] = 'core/users';
		$json['callback'] = 'JCMS.modules.users.showMainPage';
		
		return $json;
	}
	
	function showPage_addUser(){
		global $_JCMS;
		
		if( $_POST['form_submit'] == 1 ){
			$json['result'] = 1;
			$json['callback'] = 'JCMS.modules.users.result';
			$data = array();
			// экранируем входящие данные
			foreach($_POST as $key=>$val){ if(is_array($val)){ $data[$key]=$val;continue; } $data[$key] = trim($_JCMS->db->escape_string(strval($val))); } 
			
			// проверка входящих данных
			if( empty($data['user_login']) || !preg_match("/[a-z0-9]/i", $data['user_login']) ){ $_JCMS->message("Ошибка: поле &laquo;Логин&raquo; не заполнено или заполнено неправильно!", "В этом поле допустимо использовать только латинские буквы и цифры. Проверьте правильность заполнения поля и попробуйте снова.", 'error'); return $json; }

			if( empty($data['user_passw1']) || !preg_match("/[a-z0-9]/i", $data['user_passw1']) || mb_strlen($data['user_passw1']) < 8 ){ $_JCMS->message("Ошибка: поле &laquo;Пароль&raquo; не заполнено или заполнено неправильно!", "Пароль должен состоять из символов латинского алфавита и цифр. Минимальная длина пароля 8 символов.<br />Проверьте правильность заполнения поля и попробуйте снова.", 'error'); return $json; }

			if( empty($data['user_passw1']) || empty($data['user_passw2']) || $data['user_passw1'] !== $data['user_passw2'] ){ $_JCMS->message("Ошибка: введенные пароли не совпадают!", "Проверьте правильность заполнения полей и попробуйте снова.", 'error'); return $json; } else { $data['user_passw'] = md5($data['user_passw1']); }

			if( empty($data['user_name']) ){ $_JCMS->message("Ошибка: поле &laquo;Имя пользователя&raquo; не заполнено!", "Проверьте правильность заполнения поля и попробуйте снова.", 'error'); return $json; }
			
			if( empty($data['user_email']) || !filter_var($data['user_email'], FILTER_VALIDATE_EMAIL) ){ $_JCMS->message("Ошибка: поле &laquo;Адрес эл. почты&raquo; не заполнено или заполнено неправильно!", "Проверьте правильность заполнения поля и попробуйте снова.", 'error'); return $json; }

			if( !in_array($data['user_status'], array('0','1','2')) ){ $_JCMS->message("Ошибка: в поле &laquo;Статус&raquo; выбраное недопустимое значение!", "Проверьте правильность заполнения поля и попробуйте снова.", 'error'); return $json; }

			if( empty($data['user_group']) || $data['user_group'] < 1 ){ $_JCMS->message("Ошибка: в поле &laquo;Группа доступа&raquo; выбраное недопустимое значение!", "Проверьте правильность заполнения поля и попробуйте снова.", 'error'); return $json; }
			
			$profile = $_JCMS->auth->getProfile();
			$data['admin_id'] = $profile['admin_id'];			
			
			$sql_code = "INSERT INTO `jcms2_admins`(`admin_login`, `admin_email`, `admin_password`, `admin_name`, `group_id`, `admin_regDate`, `admin_status`) VALUES ('{$data[user_login]}', '{$data[user_email]}', '{$data[user_passw]}', '{$data[user_name]}', '{$data[user_group]}', CURRENT_TIMESTAMP, '{$data[user_status]}')";
			
			if( !$res = $_JCMS->db->query($sql_code) ){
				if( preg_match("/Duplicate entry '.+' for key 'admin_login'/", $_JCMS->db->error) ){
					$_JCMS->message("Ошибка: администратор с таким &laquo;Логином&raquo; уже зарегистрирован!", "", 'error');					
				} else
				if( preg_match("/Duplicate entry '.+' for key 'admin_email'/", $_JCMS->db->error) ){
					$_JCMS->message("Ошибка: администратор с таким &laquo;Адресом эл. почты&raquo; уже зарегистрирован!", "", 'error');					
				} else {
					$_JCMS->message("Ошибка базы данных! [php/core.users#".__LINE__."]", "MYSQL ERROR #".$_JCMS->db->errno." - ".$_JCMS->db->error, 'error');
				}
				return $json;
			}
			$json['addUser'] = 'success';
			$_SESSION['JENSENCMS']['mod_users']['addUser_success'] = $_JCMS->db->insert_id;
			
			return $json;
		} // end if
		
		// список групп
		$sql_code = "SELECT * FROM `jcms2_groups` ORDER BY `group_id`";
		if( !$res = $_JCMS->db->query($sql_code) ){
			$_JCMS->message("Ошибка базы данных! [php/core.users#".__LINE__."]", "MYSQL ERROR #".$_JCMS->db->errno." - ".$_JCMS->db->error, 'error');return $json;
		} else {
			while( $data = $res->fetch_assoc() ){
				$user_group[] = "<option value=\"{$data[group_id]}\">[#{$data[group_id]}] {$data[group_title]}</option>";
			}
		}
		
		$json['result'] = 1;
		$json['module_title'] = 'Создание аккаунта / Пользователи и группы';
		$_JCMS->tpl->load("core/usersAddUser.tpl");
		$_JCMS->tpl->tag("{USER_GROUP}", implode("", $user_group));
		$json['template'] = $_JCMS->tpl->compile();
		$json['template'] = preg_replace("/\{[\w-]+\}/i", "", $json['template']);
		$json['load_module'] = 'core/users';
		$json['callback'] = 'JCMS.modules.users.showPage_addUser';
		return $json;
	}

	function showPage_addGroup(){
		global $_JCMS;
		
		if( $_POST['form_submit'] == 1 ){
			$json['result'] = 1;
			$json['callback'] = 'JCMS.modules.users.result';
			$data = array();
			// экранируем входящие данные
			foreach($_POST as $key=>$val){ if(is_array($val)){ $data[$key]=$val;continue; } $data[$key] = trim($_JCMS->db->escape_string(strval($val))); } 

			// проверка входящих данных
			if( empty($data['group_title']) ){ $_JCMS->message("Ошибка: поле &laquo;Название группы&raquo; не заполнено или заполнено неправильно!", "Проверьте правильность заполнения поля и попробуйте снова.", 'error'); return $json; }
			
			$data['group_perm'] = $_JCMS->db->escape_string(json_encode($data['group_perm']));
			
			$sql_code = "INSERT INTO `jcms2_groups`(`group_title`, `group_description`, `group_permission`) VALUES ('{$data[group_title]}', '{$data[group_descr]}', '{$data[group_perm]}')";
			
			if( !$res = $_JCMS->db->query($sql_code) ){
				$_JCMS->message("Ошибка базы данных! [php/core.users#".__LINE__."]", "MYSQL ERROR #".$_JCMS->db->errno." - ".$_JCMS->db->error, 'error');
				return $json;
			}
			$json['addGroup'] = 'success';
			$_SESSION['JENSENCMS']['mod_users']['addGroup_success'] = $_JCMS->db->insert_id;
			
			return $json;
		} // end if
		
		$json['result'] = 1;
		$json['module_title'] = 'Создание группы / Пользователи и группы';
		$_JCMS->tpl->load("core/usersAddGroup.tpl");

		$data['group_permission'] = json_decode($data['group_permission'],1);
		if( !is_array($data['group_permission']) ) $data['group_permission'] = array();
		
		$res = $_JCMS->getAllModules();
		$modules = array();
		// собираем информацию о модулях
		foreach($res as $moduleName=>$modules_arr){ // все модули
			foreach($modules_arr as $subModules_arr){ // модуль
				foreach($subModules_arr as $subModuleName=>$subModule_arr){ // компоненты (подмодули)
					if( in_array(strtoupper($subModuleName), $_JCMS->sysmodules) ){ continue; } 
					$_data = array();
					$title = $subModule_arr['title'];
					$_data['title'] = $title;
					$_data['name'] = $moduleName!='core'&&$moduleName==$subModuleName?'module/'.$moduleName:$moduleName.'/'.$subModuleName;
					$_data['perm'] = $subModule_arr['perm'];
					if( $moduleName == 'core' ) array_unshift($modules, $_data); else $modules[] = $_data; // конфигурация ядра первая в списке
				}
			}
		}
		$allowModules = $_JCMS->getConfig('sysmodules');
		$perm_list = "<ul class=\"perm_list\">";
		
		foreach($modules as $mod){
			if( !$mod['title'] ) continue;
			$_id = md5(uniqid());
			$chk = ($data['group_permission'][$mod['name']]&&in_array('init', $data['group_permission'][$mod['name']]))||in_array(strtoupper($mod['name']), $_JCMS->sysmodules)?'checked=\"checked\"':'';
			$perm_list .= "<li><input type=\"checkbox\" name=\"group_perm[".$mod['name']."][]\" value=\"init\" {$chk} id=\"{$_id}\" /> <label for=\"{$_id}\">{$mod[title]}</label>";
				$perm_list .= "<div><ul>";
				if( is_array($mod['perm']) ){
					foreach($mod['perm'] as $key=>$val){
						$_id = md5(uniqid());
						$chk2 = $data['group_permission'][$mod['name']]&&in_array($key, $data['group_permission'][$mod['name']])?'checked=\"checked\"':'';
						$perm_list .= "<li><input type=\"checkbox\" {$chk2} id=\"{$_id}\" name=\"group_perm[{$mod[name]}][]\" value=\"{$key}\" /> <label for=\"{$_id}\">{$val}</label></li>";  
					}
				}
				$perm_list .= "</ul></div>";
			$perm_list .= "</li>";
		}

		$_JCMS->tpl->tag("{PERM_TABLE}", $perm_list);
		$json['template'] = $_JCMS->tpl->compile();
		$json['template'] = preg_replace("/\{[\w-]+\}/i", "", $json['template']);
		$json['load_module'] = 'core/users';
		$json['callback'] = 'JCMS.modules.users.showPage_addGroup';
		return $json;
	}

	function showPage_EditUser(){
		global $_JCMS;
		$id = $_JCMS->db->escape_string(intval($_GET['user_id']));
		
		if( $_POST['form_submit'] == 1 ){
			$json['result'] = 1;
			$json['callback'] = 'JCMS.modules.users.result';
			$data = array();
			// экранируем входящие данные
			foreach($_POST as $key=>$val){ if(is_array($val)){ $data[$key]=$val;continue; } $data[$key] = trim($_JCMS->db->escape_string(strval($val))); } 
			
			// проверка входящих данных
			if( empty($data['user_login']) || !preg_match("/[a-z0-9]/i", $data['user_login']) ){ $_JCMS->message("Ошибка: поле &laquo;Логин&raquo; не заполнено или заполнено неправильно!", "В этом поле допустимо использовать только латинские буквы и цифры. Проверьте правильность заполнения поля и попробуйте снова.", 'error'); return $json; }

			if( !empty($data['user_passw1']) || !empty($data['user_passw2']) ){
				if( !preg_match("/[a-z0-9]/i", $data['user_passw1']) || mb_strlen($data['user_passw1']) < 8 ){ $_JCMS->message("Ошибка: поле &laquo;Пароль&raquo; не заполнено или заполнено неправильно!", "Пароль должен состоять из символов латинского алфавита и цифр. Минимальная длина пароля 8 символов.<br />Проверьте правильность заполнения поля и попробуйте снова.", 'error'); return $json; }
	
				if( empty($data['user_passw1']) || empty($data['user_passw2']) || $data['user_passw1'] !== $data['user_passw2'] ){ $_JCMS->message("Ошибка: введенные пароли не совпадают!", "Проверьте правильность заполнения полей и попробуйте снова.", 'error'); return $json; }
				
				$new_passw = ', `admin_password` = \''.md5($data['user_passw1']).'\'';
			} else { $new_passw = ""; }
			
			if( empty($data['user_name']) ){ $_JCMS->message("Ошибка: поле &laquo;Имя пользователя&raquo; не заполнено!", "Проверьте правильность заполнения поля и попробуйте снова.", 'error'); return $json; }
			
			if( empty($data['user_email']) || !filter_var($data['user_email'], FILTER_VALIDATE_EMAIL) ){ $_JCMS->message("Ошибка: поле &laquo;Адрес эл. почты&raquo; не заполнено или заполнено неправильно!", "Проверьте правильность заполнения поля и попробуйте снова.", 'error'); return $json; }

			if( !in_array($data['user_status'], array('0','1','2')) ){ $_JCMS->message("Ошибка: в поле &laquo;Статус&raquo; выбраное недопустимое значение!", "Проверьте правильность заполнения поля и попробуйте снова.", 'error'); return $json; }

			if( empty($data['user_group']) || $data['user_group'] < 1 ){ $_JCMS->message("Ошибка: в поле &laquo;Группа доступа&raquo; выбраное недопустимое значение!", "Проверьте правильность заполнения поля и попробуйте снова.", 'error'); return $json; }
			

			$sql_code = "UPDATE `jcms2_admins` SET `admin_login`='{$data[user_login]}', `admin_email`='{$data[user_email]}', `admin_name`='{$data[user_name]}', `group_id`='{$data[user_group]}', `admin_status`='{$data[user_status]}'{$new_passw} WHERE `admin_id` = '{$id}' LIMIT 1";

			if( !$res = $_JCMS->db->query($sql_code) ){
				if( preg_match("/Duplicate entry '.+' for key 'admin_login'/", $_JCMS->db->error) ){
					$_JCMS->message("Ошибка: администратор с таким &laquo;Логином&raquo; уже зарегистрирован!", "", 'error');					
				} else
				if( preg_match("/Duplicate entry '.+' for key 'admin_email'/", $_JCMS->db->error) ){
					$_JCMS->message("Ошибка: администратор с таким &laquo;Адресом эл. почты&raquo; уже зарегистрирован!", "", 'error');					
				} else {
					$_JCMS->message("Ошибка базы данных! [php/core.users#".__LINE__."]", "MYSQL ERROR #".$_JCMS->db->errno." - ".$_JCMS->db->error, 'error');
				}
				return $json;
			}
			$json['editUser'] = 'success';
			$_SESSION['JENSENCMS']['mod_users']['editUser_success'] = $id;
			
			return $json;
		} // end if

		$sql_code = "SELECT * FROM `jcms2_admins` WHERE `admin_id` = '{$id}' LIMIT 1";
		if( !$res = $_JCMS->db->query($sql_code) ){ $_JCMS->message("Ошибка базы данных! [php/core.users#".__LINE__."]", "MYSQL ERROR #".$_JCMS->db->errno." - ".$_JCMS->db->error, 'error'); return $json; }
		if( $res->num_rows !== 1 ){
			$_JCMS->message("Ошибка: запрошенный аккаунт не существует! [php/core.users#".__LINE__."]", "", 'error'); return $json;
		}
		$data = $res->fetch_assoc();

		// список групп
		$sql_code = "SELECT * FROM `jcms2_groups` ORDER BY `group_id`";
		if( !$res = $_JCMS->db->query($sql_code) ){
			$_JCMS->message("Ошибка базы данных! [php/core.users#".__LINE__."]", "MYSQL ERROR #".$_JCMS->db->errno." - ".$_JCMS->db->error, 'error');return $json;
		} else {
			while( $data2 = $res->fetch_assoc() ){
				$user_group[] = "<option value=\"{$data2[group_id]}\">[#{$data2[group_id]}] {$data2[group_title]}</option>";
			}
		}
		
		$json['result'] = 1;
		$json['module_title'] = 'Редактирование аккаунта #'.$id.' / Пользователи и группы';
		$_JCMS->tpl->load("core/usersEditUser.tpl");
		foreach($data as $key=>$val){ $_JCMS->tpl->tag("{".strtoupper($key)."}", $val); }
		$_JCMS->tpl->tag("{ADMIN_GROUP}", implode("", $user_group));
		$_JCMS->tpl->tag("{ID}", $id);
		$json['template'] = $_JCMS->tpl->compile();
		$json['template'] = preg_replace("/\{[\w-]+\}/i", "", $json['template']);
		$json['load_module'] = 'core/users';
		$json['group_id'] = $data['group_id'];
		$json['admin_status'] = $data['admin_status'];
		$json['callback'] = 'JCMS.modules.users.showPage_editUser';
		
		return $json;
	}

	function showPage_EditGroup(){
		global $_JCMS;
		$id = $_JCMS->db->escape_string(intval($_GET['group_id']));
		
		if( $_POST['form_submit'] == 1 ){
			$json['result'] = 1;
			$json['callback'] = 'JCMS.modules.users.result';
			$data = array();
			// экранируем входящие данные
			foreach($_POST as $key=>$val){ if(is_array($val)){ $data[$key]=$val;continue; } $data[$key] = trim($_JCMS->db->escape_string(strval($val))); } 
			
			// проверка входящих данных
			if( empty($data['group_title']) ){ $_JCMS->message("Ошибка: поле &laquo;Название группы&raquo; не заполнено или заполнено неправильно!", "Проверьте правильность заполнения поля и попробуйте снова.", 'error'); return $json; }
			
			$data['group_perm'] = $_JCMS->db->escape_string(json_encode($data['group_perm']));			

			$sql_code = "UPDATE `jcms2_groups` SET `group_title`='{$data[group_title]}', `group_description`='{$data[group_descr]}', `group_permission`='{$data[group_perm]}' WHERE `group_id` = '{$id}'";

			if( !$res = $_JCMS->db->query($sql_code) ){
				$_JCMS->message("Ошибка базы данных! [php/core.users#".__LINE__."]", "MYSQL ERROR #".$_JCMS->db->errno." - ".$_JCMS->db->error, 'error');
				return $json;
			}
			$json['editUser'] = 'success';
			$_SESSION['JENSENCMS']['mod_users']['editGroup_success'] = $id;
			$_JCMS->load_interface();
			return $json;
		} // end if

		$sql_code = "SELECT * FROM `jcms2_groups` WHERE `group_id` = '{$id}' LIMIT 1";
		if( !$res = $_JCMS->db->query($sql_code) ){ $_JCMS->message("Ошибка базы данных! [php/core.users#".__LINE__."]", "MYSQL ERROR #".$_JCMS->db->errno." - ".$_JCMS->db->error, 'error'); return $json; }
		if( $res->num_rows !== 1 ){
			$_JCMS->message("Ошибка: запрошенный аккаунт не существует! [php/core.users#".__LINE__."]", "", 'error'); return $json;
		}
		$data = $res->fetch_assoc();
		
		$data['group_permission'] = json_decode($data['group_permission'],1);
		if( !is_array($data['group_permission']) ) $data['group_permission'] = array();
		
		$res = $_JCMS->getAllModules();
		$modules = array();
		// собираем информацию о модулях
		foreach($res as $moduleName=>$modules_arr){ // все модули
			foreach($modules_arr as $subModules_arr){ // модуль
				foreach($subModules_arr as $subModuleName=>$subModule_arr){ // компоненты (подмодули)
					if( in_array(strtoupper($subModuleName), $_JCMS->sysmodules) ){ continue; } 
					$_data = array();
					$title = $subModule_arr['title'];
					$_data['title'] = $title;
					$_data['name'] = $moduleName!='core'&&$moduleName==$subModuleName?'module/'.$moduleName:$moduleName.'/'.$subModuleName;
					$_data['perm'] = $subModule_arr['perm'];
					if( $moduleName == 'core' ) array_unshift($modules, $_data); else $modules[] = $_data; // конфигурация ядра первая в списке
				}
			}
		}
		$allowModules = $_JCMS->getConfig('sysmodules');
		$perm_list = "<ul class=\"perm_list\">";
		
		foreach($modules as $mod){
			if( !$mod['title'] ) continue;
			$_id = md5(uniqid());
			$chk = ($data['group_permission'][$mod['name']]&&in_array('init', $data['group_permission'][$mod['name']]))||in_array(strtoupper($mod['name']), $_JCMS->sysmodules)?'checked=\"checked\"':'';
			$perm_list .= "<li><input type=\"checkbox\" name=\"group_perm[".$mod['name']."][]\" value=\"init\" {$chk} id=\"{$_id}\" /> <label for=\"{$_id}\">{$mod[title]}</label>";
				$perm_list .= "<div><ul>";
				if( is_array($mod['perm']) ){
					foreach($mod['perm'] as $key=>$val){
						$_id = md5(uniqid());
						$chk2 = $data['group_permission'][$mod['name']]&&in_array($key, $data['group_permission'][$mod['name']])?'checked=\"checked\"':'';
						$perm_list .= "<li><input type=\"checkbox\" {$chk2} id=\"{$_id}\" name=\"group_perm[{$mod[name]}][]\" value=\"{$key}\" /> <label for=\"{$_id}\">{$val}</label></li>";  
					}
				}
				$perm_list .= "</ul></div>";
			$perm_list .= "</li>";
		}
		
		$json['result'] = 1;
		$json['module_title'] = 'Редактирование группы #'.$id.' / Пользователи и группы';
		$_JCMS->tpl->load("core/usersEditGroup.tpl");
		foreach($data as $key=>$val){ $_JCMS->tpl->tag("{".strtoupper($key)."}", $val); }
		$_JCMS->tpl->tag("{ID}", $id);
		$_JCMS->tpl->tag("{PERM_TABLE}", $perm_list);
		$json['template'] = $_JCMS->tpl->compile();
		$json['template'] = preg_replace("/\{[\w-]+\}/i", "", $json['template']);
		$json['load_module'] = 'core/users';
		$json['group_id'] = $data['group_id'];
		$json['admin_status'] = $data['admin_status'];
		$json['callback'] = 'JCMS.modules.users.showPage_editGroup';
		
		return $json;
	}
	
	function actionUserDelete(){
		global $_JCMS;
		$json = array();
		$json['result'] = 1;
		$json['callback'] = 'JCMS.modules.users.result';
		$id = $_JCMS->db->escape_string(intval($_GET['user_id']));

		// после удаления должен быть хотя бы один активный аккаунт. Иначе доступ к адмнке будет закрыт
		$sql_code = "SELECT * FROM `jcms2_admins` WHERE `admin_status` = '1' AND `admin_id` != '{$id}' LIMIT 1";
		if( !$res = $_JCMS->db->query($sql_code) ){
			$_JCMS->message("Ошибка базы данных! [php/core.users#".__LINE__."]", "MYSQL ERROR #".$_JCMS->db->errno." - ".$_JCMS->db->error, 'error');return $json;
		} else {
			if( $res->num_rows < 1 ){			
				$_JCMS->message("Выполнение операции невозможно! [php/core.users#".__LINE__."]", "После удаление этого аккаунта в системе не останется ни одного активного аккаунта.", 'error');
				return $json;
			}
		}
		$sql_code = "DELETE FROM `jcms2_admins` WHERE `admin_id` = '{$id}' LIMIT 1";
		if( !$res = $_JCMS->db->query($sql_code) ){
			$_JCMS->message("Ошибка базы данных! [php/core.users#".__LINE__."]", "MYSQL ERROR #".$_JCMS->db->errno." - ".$_JCMS->db->error, 'error');
			return $json;
		} else {
			$json['deleteUser'] = 'success';
			$_SESSION['JENSENCMS']['mod_users']['deleteUser_success'] = $id;
		}
		
		return $json;
	}

	function actionGroupDelete(){
		global $_JCMS;
		$json = array();
		$json['result'] = 1;
		$json['callback'] = 'JCMS.modules.users.result';
		$id = $_JCMS->db->escape_string(intval($_GET['group_id']));

		// после удаления должен быть хотя бы один активный аккаунт в другой группе. Иначе доступ к адмнке будет закрыт
		$sql_code = "SELECT * FROM `jcms2_admins` WHERE `admin_status` = '1' AND `group_id` != '{$id}' LIMIT 1";
		if( !$res = $_JCMS->db->query($sql_code) ){
			$_JCMS->message("Ошибка базы данных! [php/core.users#".__LINE__."]", "MYSQL ERROR #".$_JCMS->db->errno." - ".$_JCMS->db->error, 'error');return $json;
		} else {
			if( $res->num_rows < 1 ){			
				$_JCMS->message("Выполнение операции невозможно! [php/core.users#".__LINE__."]", "После удаление этой группы в системе не останется ни одного активного аккаунта.", 'error');
				return $json;
			}
		}


		$sql_code = "DELETE FROM `jcms2_admins` WHERE `group_id` = '{$id}'";
		if( !$res = $_JCMS->db->query($sql_code) ){
			$_JCMS->message("Ошибка базы данных! [php/core.users#".__LINE__."]", "MYSQL ERROR #".$_JCMS->db->errno." - ".$_JCMS->db->error, 'error');
			return $json;
		}

		$sql_code = "DELETE FROM `jcms2_groups` WHERE `group_id` = '{$id}'";
		if( !$res = $_JCMS->db->query($sql_code) ){
			$_JCMS->message("Ошибка базы данных! [php/core.users#".__LINE__."]", "MYSQL ERROR #".$_JCMS->db->errno." - ".$_JCMS->db->error, 'error');
			return $json;
		}
		
		$json['deleteGroup'] = 'success';
		$_SESSION['JENSENCMS']['mod_users']['deleteGroup_success'] = $id;
		
		return $json;
	}
	
	/* выбирает из базы строки, подходящие запросу юзера(с учетом поиска и сортировки) и формирует ответ в формате, подходящем для JS плагина DataTables */
	function getTable1DataAjax(){
		global $_JCMS;
		$resp = array();
		$resp['draw'] = strval($_POST['draw']);		
		$resp['error'] = NULL;
		// столбы таблицы в порядке следования в интерфейсе
		$columns = array('admin_id', 'admin_login', 'admin_name', 'admin_email', array('jcms2_groups`.`group_id','jcms2_groups`.`group_title'), 'admin_regDate', 'admin_status');

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
		if( !empty($sql_search) ){ $sql_where = !$sql_where?"AND (":''; $sql_where .= $sql_search.')'; unset($sql_search); }
		$sql_limit = "LIMIT ".intval($_POST['start']).",".intval($_POST['length']);

		$sql_code = "SELECT SQL_CALC_FOUND_ROWS `jcms2_admins`.*, `jcms2_groups`.* FROM `jcms2_admins`, `jcms2_groups` WHERE `jcms2_admins`.`group_id` = `jcms2_groups`.`group_id` {$sql_where} {$sql_order} {$sql_limit}";

		if( !$res = $_JCMS->db->query($sql_code) ){ $_JCMS->message("Ошибка базы данных! [php/core.users#".__LINE__."]", "MYSQL ERROR #".$_JCMS->db->errno." - ".$_JCMS->db->error, 'error'); return $json; }
		$table_data = array();
		if( $res->num_rows > 0 ){
			// table data
			while( $data = $res->fetch_assoc() ){
				$admin_group = '<span class="_tooltip" rel="tooltip" title="<b>ID:</b> '.$data['group_id'].'">'.$data['group_title'].'</span>';
				$adminRegDate = date("d.m.Y H:i:s",strtotime($data['admin_regDate']));
				$admin_status = $data['admin_status'];
				$admin_status = $admin_status==0?'<span class="status0">Отключён</span>':($admin_status==1?'<span class="status1">Включён</span>':($admin_status==2?'<span class="status2">Не&nbsp;активирован</span>':'???')); 
			#	$data['admin_online'] = time()-strtotime($data['admin_lastAuthDate']);
			#	$data['admin_online'] = $data['admin_online'] < 1*60 ? '<span rel="tooltip" title="Онлайн" class="adminOnline"></span>' : '<span rel="tooltip" title="Офлайн" class="adminOffline"></span>';
				$table_data[] = array($data['admin_id'], $data['admin_online'].' '.$data['admin_login'], $data['admin_name'], $data['admin_email'], $admin_group, $adminRegDate, $admin_status);
			}
		}
		// всего записей с учетом фильтра, но без LIMIT
		$sql_code = "SELECT FOUND_ROWS() as total_filtered;";
		if( !$res = $_JCMS->db->query($sql_code) ){ $_JCMS->message("Ошибка базы данных! [php/core.users#".__LINE__."]", "MYSQL ERROR #".$_JCMS->db->errno." - ".$_JCMS->db->error, 'error'); return $json; } else {
			$data = $res->fetch_assoc();
			$total_filtered = $data['total_filtered'];
		}
		
		// всего записей в таблице
		$sql_code = "SELECT COUNT(`admin_id`) as total FROM `jcms2_admins`, `jcms2_groups` WHERE `jcms2_admins`.`group_id` = `jcms2_groups`.`group_id` ";
		if( !$res = $_JCMS->db->query($sql_code) ){
			$_JCMS->message("Ошибка базы данных! [php/core.users#".__LINE__."]", "MYSQL ERROR #".$_JCMS->db->errno." - ".$_JCMS->db->error, 'error');return $json;
		} else {
			$data = $res->fetch_assoc();
			$total = $data['total'];
		}
		
		$resp['recordsTotal'] = $total;
		$resp['recordsFiltered'] = $total_filtered;		
		$resp['data'] = $table_data; unset($table_data);
		
		return $resp;
	}

	/* выбирает из базы строки, подходящие запросу юзера(с учетом поиска и сортировки) и формирует ответ в формате, подходящем для JS плагина DataTables */
	function getTable2DataAjax(){
		global $_JCMS;
		$resp = array();
		$resp['draw'] = strval($_POST['draw']);		
		$resp['error'] = NULL;
		// столбы таблицы в порядке следования в интерфейсе
		$columns = array('group_id', 'group_title', 'group_description', 'group_status');

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
		if( !empty($sql_search) ){ $sql_where = !$sql_where?"WHERE ":''; $sql_where .= $sql_search; unset($sql_search); }
		$sql_limit = "LIMIT ".intval($_POST['start']).",".intval($_POST['length']);

		$sql_code = "SELECT SQL_CALC_FOUND_ROWS `jcms2_groups`.* FROM `jcms2_groups` {$sql_where} {$sql_order} {$sql_limit}";
		if( !$res = $_JCMS->db->query($sql_code) ){ $_JCMS->message("Ошибка базы данных! [php/core.users#".__LINE__."]", "MYSQL ERROR #".$_JCMS->db->errno." - ".$_JCMS->db->error, 'error'); return $json; }
		$table_data = array();
		if( $res->num_rows > 0 ){
			// table data
			while( $data = $res->fetch_assoc() ){
				$admin_name = '<span class="_tooltip" rel="tooltip" title="<b>ID:</b> '.$data['admin_id'].'<br /><b>Login:</b> '.$data['admin_login'].'">'.mb_ucfirst($data['admin_name']?$data['admin_name']:$data['admin_login']).'</span>';
				$page_dateChange = date("d.m.Y H:i:s",strtotime($data['page_dateChange']));
				$before_title .= !$before_title&&$data['page_type']==2?'<span class="glyphicon glyphicon-share" rel="tooltip" title="<b>Тип:</b> &laquo;Иморт URL&raquo;"></span> ':'';
				$before_title .= $data['page_status']==0?'<span class="glyphicon glyphicon-eye-close" rel="tooltip" title="Страница отключена"></span> ':'';
				$table_data[] = array($data['group_id'], $data['group_title'], $data['group_description']?$data['group_description']:'&mdash;');
			}
		}
		// всего записей с учетом фильтра, но без LIMIT
		$sql_code = "SELECT FOUND_ROWS() as total_filtered;";
		if( !$res = $_JCMS->db->query($sql_code) ){ $_JCMS->message("Ошибка базы данных! [php/core.users#".__LINE__."]", "MYSQL ERROR #".$_JCMS->db->errno." - ".$_JCMS->db->error, 'error'); return $json; } else {
			$data = $res->fetch_assoc();
			$total_filtered = $data['total_filtered'];
		}
		
		// всего записей в таблице
		$sql_code = "SELECT COUNT(`group_id`) as total FROM `jcms2_groups`";
		if( !$res = $_JCMS->db->query($sql_code) ){
			$_JCMS->message("Ошибка базы данных! [php/core.users#".__LINE__."]", "MYSQL ERROR #".$_JCMS->db->errno." - ".$_JCMS->db->error, 'error');return $json;
		} else {
			$data = $res->fetch_assoc();
			$total = $data['total'];
		}
		
		$resp['recordsTotal'] = $total;
		$resp['recordsFiltered'] = $total_filtered;		
		$resp['data'] = $table_data; unset($table_data);
		
		return $resp;
	}
	
}

// END.
?>