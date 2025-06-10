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

class PROFILE implements JCMS_MODULE_COMPONENT{
	function __construct(){}
	/* инициаизация модуля */ 
	function setup(){
		if( get_class($this) != 'PROFILE' ) return; // метод вызван из дочерних классов. Этот метод не общий...
		$setup = array();
		$setup["title"]	= "Модуль &laquo;Управление аккаунтом&raquo;";

		return $setup;
	}
	
	/* воркер, обрабатывающий запрос к этому модулю */
	function work(){
		global $_JCMS, $_JSON;		
		$json = array();
		
		if( $_POST['action'] == 'changePassword' ){
			$json = $this->changePassword($_POST);
			return $json;
		}
		if( $_POST['action'] == 'changeUsername' ){
			$json = $this->changeUsername($_POST);
			return $json;			
		}

		$json['result'] = 1;
		$json['module_title'] = "Управление аккаунтом";
				
		$_JCMS->tpl->load('core/profile.tpl');
		$profile = $_JCMS->auth->getProfile();
		
		$_JCMS->tpl->tag("{USER_LOGIN}", $profile['admin_login']?$profile['admin_login']:'&mdash;');
		$_JCMS->tpl->tag("{USER_EMAIL}", $profile['admin_email']?$profile['admin_email']:'&mdash;');
		$_JCMS->tpl->tag("{USER_NAME}", $profile['admin_name']?mb_ucfirst($profile['admin_name']):'&mdash;');
		$_JCMS->tpl->tag("{USER_GROUP}", $profile['group_title']?'[#'.$profile['group_id'].'] '.$profile['group_title']:'&mdash;');
		$_JCMS->tpl->tag("{USER_DATE_REG}", $profile['admin_regDate']?date('d.m.Y H:i:s', strtotime($profile['admin_regDate'])):'&mdash;');
		$_JCMS->tpl->tag("{USER_DATE_LAST_AUTH}", $profile['admin_lastAuthDate']?date('d.m.Y H:i:s', strtotime($profile['admin_lastAuthDate'])):'&mdash;');
		
		$json['template'] = $_JCMS->tpl->compile();
		$json['load_module'] = 'core/profile';
		$json['callback'] = 'JCMS.modules.profile.showPage';
		return $json;
	}

	function changeUsername(){
		global $_JCMS, $_JSON;
		$profile = $_JCMS->auth->getProfile();
		$json['result'] = 1;

		// экранируем входящие данные
		foreach($_POST as $key=>$val){ if(is_array($val)){ $data[$key]=$val;continue; } $data[$key] = trim($_JCMS->db->escape_string(strval($val))); } 
		
		// проверка входящих данных
		if( empty($data['user_newname']) ){
			$_JCMS->message("Ошибка: поле &laquo;Имя пользователя&raquo; не заполнено или заполнено неправильно!", "", 'error');
			return $json;
		}
		
		$sql_code = "UPDATE `jcms2_admins` SET `admin_name` = '".$data['user_newname']."' WHERE `admin_id` = '{$profile[admin_id]}' LIMIT 1";

		if( !$res = $_JCMS->db->query($sql_code) ){
			$_JCMS->message("Ошибка базы данных! [php/core.users#".__LINE__."]", "MYSQL ERROR #".$_JCMS->db->errno." - ".$_JCMS->db->error, 'error');
			return $json;
		}
		$_JCMS->auth->checkAuth();
		$json['changeUsername'] = 'success';
		$_JCMS->message("<b>Изменение имени пользователя</b> - успешно выполнено!", "", 'notice');

		return $json;	
	}
	
	function changePassword(){
		global $_JCMS, $_JSON;
		$profile = $_JCMS->auth->getProfile();
		$json['result'] = 1;
$json['action_result'] = 0;
		// экранируем входящие данные
		foreach($_POST as $key=>$val){ if(is_array($val)){ $data[$key]=$val;continue; } $data[$key] = trim($_JCMS->db->escape_string(strval($val))); } 
		
		// проверка входящих данных
		if( empty($profile['admin_password']) || $profile['admin_password'] !== md5($data['user_oldpassw']) ){
		#	$_JCMS->message("Ошибка: поле &laquo;Текущий пароль&raquo; не заполнено или заполнено неправильно!", "Введенный пароль не совпадает с текущим паролем.", 'error');
		#	return $json;
		}
		
		if( empty($data['user_newpassw1']) || empty($data['user_newpassw2']) || !preg_match("/[a-z0-9]/i", $data['user_newpassw1']) || mb_strlen($data['user_newpassw1']) < 8 ){ 
		#	$_JCMS->message("Ошибка: поле &laquo;Новый пароль&raquo; не заполнено или заполнено неправильно!", "Пароль должен состоять из символов латинского алфавита и цифр. Минимальная длина пароля 8 символов.<br />Проверьте правильность заполнения поля и попробуйте снова.", 'error');
		#	return $json;
		}

		if( $data['user_passw1'] !== $data['user_passw2'] ){ 
			$_JCMS->message("Ошибка: введенные пароли не совпадают!", "Проверьте правильность заполнения полей и попробуйте снова.", 'error');
			return $json;
		}

		$sql_code = "UPDATE `jcms2_admins` SET `admin_password` = '".md5($data['user_newpassw1'])."' WHERE `admin_id` = '{$profile[admin_id]}' LIMIT 1";

		if( !$res = $_JCMS->db->query($sql_code) ){
			$_JCMS->message("Ошибка базы данных! [php/core.users#".__LINE__."]", "MYSQL ERROR #".$_JCMS->db->errno." - ".$_JCMS->db->error, 'error');
			return $json;
		}

		$_JCMS->syslog->add('Изменение пароля аккаунта.<br /><b>Логин:</b> '.$profile['admin_login'], 3);
$json['action_result'] = 1;
		$_SESSION['JENSENCMS']['core_profile']['changePassword_success'] = 1;

		$_JCMS->auth->logout($_JSON);
		
		return $json;	
	}
}

// END.
?>