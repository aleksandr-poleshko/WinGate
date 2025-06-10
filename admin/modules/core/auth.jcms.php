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

class AUTH implements JCMS_MODULE_COMPONENT{
	function __construct(){}
	/* инициаизация модуля */ 
	function setup(){
		$setup["title"]	= "Компонент для  авторизации в панели управления сайтом ";
		return $setup;
	}
	
	function logout(&$json){
		global $_SESSION;
		// завершение сессии
		$json['load_module'] = "core/auth";
		$json['callback'][] = "JCMS.unload_interface";
		$json['callback'][] = "JCMS.modules.auth.result";
		$json['auth_result'] = 'logout';
#		setcookie('jcms2_auth_token', NULL, -1, '/');
		$_SESSION["JENSENCMS"]['core_auth']['logout'] = 1;
		unset($_SESSION["JENSENCMS"]['AUTH']);
	}
	/* воркер, обрабатывающий запрос к этому модулю */
	function work(){
		global $_JCMS;
		
		$json = array();
		// проверяем разрешен ли доступ с указанного IP
		$allow_ip = $_JCMS->getConfig('auth_allowIP');
		if( !empty($allow_ip) ){
			// включен контроль доступа по IP (в списке есть хотя бы один IP)
			$allow_ip = explode(',',$allow_ip);
			$ip = $this->getUserIP();
			if( !in_array($ip, $allow_ip) ){
				$json['module_title'] = "Доступ запрещён!";
				$_JCMS->message("Извините, доступ к панели управления с Вашего IP адреса запрещён!", "", 'error');
				$json['access_deny'] = 1;
				return $json;
			}			
		}
		if( $this->checkAuth() ){
			if( $_POST['auth'] == 'logout' ){
				$this->logout($json);
				return $json;
			}

			return $json; 
		}
		// юзер не авторизован
		if( $_POST['form_submit'] == 1 && $_POST['form_auth'] == 1 ){
			$authToken1 = $_SESSION["JENSENCMS"]['authToken1'];
			$authToken2 = $_SESSION["JENSENCMS"]['authToken2'];
			// регенерация токенов, на случай если запрос некорректный. 
			$_SESSION["JENSENCMS"]['authToken1'] = $json['authToken1'] = md5(mt_rand().mt_rand().mt_rand().uniqid());
			$_SESSION["JENSENCMS"]['captcha'] = strtolower($_SESSION["JENSENCMS"]['captcha']);
			$_POST['jcms_authCaptcha'] = strtolower($_POST['jcms_authCaptcha']);
			if( empty($_SESSION["JENSENCMS"]['captcha']) || $_SESSION["JENSENCMS"]['captcha'] != $_POST['jcms_authCaptcha'] ){
				$_JCMS->message("Ошибка: защитный код введён неверно!", "Проверьте правильность ввода данных и попробуйте снова.", 'error');
				return $json;
			}
			$d['username'] = $_JCMS->db->escape_string($_POST['jcms_authUsername']);
			if( empty($authToken1) || $authToken1 !== $_POST['jcms_authToken'] ){
				$_JCMS->syslog->add('Попытка обхода защиты в системе авторизации!<br /><b>Логин:</b> '.$d['username'].'<br><b>Капча пройдена</b>.', 1);
				$_JCMS->message("Hacking attempt!", '', 'error');
				return $json;					
			}
			$d['password'] = $_JCMS->db->escape_string(md5($_POST['jcms_authPassword'])); // md5 хэш пароля
			$d['shortSession'] = strval($_POST['jcms_authShortSession'])=='1'?TRUE:FALSE;
			$sql_code = "SELECT * FROM `jcms2_admins` WHERE (`admin_login` = '{$d[username]}' OR `admin_email` = '{$d[username]}') AND `admin_password` = '{$d[password]}'";
			if( !$res = $_JCMS->db->query($sql_code) ){ return $_JCMS->message("Ошибка базы данных! [php/core.auth#".__LINE__."]", "MYSQL ERROR #".$_JCMS->db->errno." - ".$_JCMS->db->error, 'error'); }
			if( $res->num_rows !== 1 ){
				if( $_SESSION['JENSENCMS']['AUTH_ERROR_COUNT'] ) $_SESSION['JENSENCMS']['AUTH_ERROR_COUNT']++; else $_SESSION['JENSENCMS']['AUTH_ERROR_COUNT'] = 1;
				if( $_SESSION['JENSENCMS']['AUTH_ERROR_COUNT'] % 3 == 0 ){
					$_JCMS->syslog->add('Попытка подбора пароля к логину: <b>'.$d['username'].'</b>!<br />Количество неудачных попыток входа &ndash; '.$_SESSION['JENSENCMS']['AUTH_ERROR_COUNT'].'.<br>Капча пройдена.', 1);
				}
				$_JCMS->message("Ошибка: пользователь с указанным данными не найден!", "Проверьте правильность ввода данных и попробуйте снова!", 'error');
				return $json;
			}
			unset($_SESSION['JENSENCMS']['AUTH_ERROR_COUNT']);
			// авторизация успешна
			// токены больше не нужны
			unset($json['authToken1'], $json['authToken2'], $_SESSION["JENSENCMS"]['authToken1'], $_SESSION["JENSENCMS"]['authToken2']);
			$data = $res->fetch_assoc();
			if( $d['shortSession'] ){
				$auth_shortSessionLifetime = $_JCMS->getConfig('auth_shortSessionLifetime');
				if( $auth_shortSessionLifetime < 1 ) $auth_shortSessionLifetime = 60; // default
				$data['shortSessionEnd'] = time() + $auth_shortSessionLifetime*60; // время жизни короткой сессии ({$auth_shortSessionLifetime} мин)
				$json['shortSession'] = true;
			} else {
				$json['shortSession'] = false;
			}
			$data['lastActivity'] = time();
			$data['admin_auth_token'] = $json['auth_token'] = md5(mt_rand().mt_rand().mt_rand().uniqid()); // токен для cookie
			setcookie('jcms2_auth_login', $data['admin_login'],mktime(0,0,0,1,1,date('Y')+1), '/');
			$_SESSION["JENSENCMS"]['AUTH'] = $data; 

			// обновляем токен авторизации в базе
			$sql_code = "UPDATE `jcms2_admins` SET `admin_auth_token` = '{$data[admin_auth_token]}', `admin_lastAuthDate` = CURRENT_TIMESTAMP WHERE (`admin_login` = '{$data[admin_login]}' OR `admin_email` = '{$data[admin_email]}') AND `admin_password` = '{$data[admin_password]}' AND `admin_status` = '1'";
			if( !$res = $_JCMS->db->query($sql_code) ){ return $_JCMS->message("Ошибка базы данных! [php/core.auth#".__LINE__."]", "MYSQL ERROR #".$_JCMS->db->errno." - ".$_JCMS->db->error, 'error'); }
			
			$this->checkAuth();
			
			$json['admin_name'] = (!empty($data['admin_name'])?$data['admin_name']:$data['admin_login']);
			$_JCMS->syslog->add('Успешная авторизация в панели управления.<b><br />Логин/E-mail:</b> '.$d['username'].'<br /><b>IP:</b> '.$this->getUserIP().($json['shortSession']==1?'<br /><b>Короткая сессия:</b> до '.date('d.m.Y H:i:s',$data['shortSessionEnd']):''), 3);
			$json['auth_result'] = 'success';
			$json['auth_shortSessionLifetime'] = $_JCMS->getConfig('auth_shortSessionLifetime')<1?60:$_JCMS->getConfig('auth_shortSessionLifetime');
			$json['auth_inactiveSessionLifetime'] = $_JCMS->getConfig('auth_inactiveSessionLifetime')<1?30:$_JCMS->getConfig('auth_inactiveSessionLifetime');
			
			return $json;
		} else {
			if( $_SESSION["JENSENCMS"]['core_auth']['logout'] == 1 ){
				if( !$_SESSION["JENSENCMS"]['core_auth']['auto_logout'] && !$_SESSION['JENSENCMS']['core_profile']['changePassword_success'] ){
					// не показываем сообщение если сессия закрыта автоматом
					$_JCMS->message("Сессия авторизации успешно завершена!", "", 'notice');
				}
				unset($_SESSION["JENSENCMS"]['core_auth']['logout']);
			}
			if( $_SESSION['JENSENCMS']['core_profile']['changePassword_success'] ){
				$_JCMS->message("Изменение пароля аккаунта успешно выполнено!", "Необходима повторная авторизаци с новым паролем.", 'notice');
				unset($_SESSION['JENSENCMS']['core_profile']['changePassword_success']);
			}
			$json['result'] = 1;
			$json['module_title'] = "Авторизация";
			$_SESSION["JENSENCMS"]['authToken1'] = $json['authToken1'] = md5(mt_rand().mt_rand().mt_rand().uniqid());
			// выводим форму входа
			$_JCMS->tpl->load("core/auth.tpl");
			$_JCMS->tpl->tag("{HOME_URL}", $_JCMS->getConfig['home_url']);
			$json['template'] = $_JCMS->tpl->compile();
			$json['callback'] = "JCMS.modules.auth.showForm";
			$json['load_module'] = "core/auth";
			return $json;
		}
	}
	function isAuth(){
		return $this->checkAuth();
	}
	/* Проверка валидности сессии авторизации */
	function checkAuth(){
		global $_JCMS, $_SESSION, $_JSON;
		if( !$_SESSION["JENSENCMS"]['AUTH'] ){ return false; } // нет сессии авторизации
		$sess = $_SESSION["JENSENCMS"]['AUTH'];
		if( $sess['shortSessionEnd'] > 0 ){
			// сессия ограничена по времени, проверяем не просрочена ли?
			if( $sess['shortSessionEnd'] <= time() ){
				// просрочена
				$_SESSION["JENSENCMS"]['core_auth']['auto_logout'] = 1;
				$auth_shortSessionLifetime = $_JCMS->getConfig('auth_shortSessionLifetime');
				if( $auth_shortSessionLifetime < 1 ) $auth_shortSessionLifetime = 60; // default
				$_JCMS->message("Cессия доступа была принудительно завершена по истечению срока действия ({$auth_shortSessionLifetime} мин.)", "", 'error');
				$_JCMS->syslog->add('Cессия доступа была принудительно завершена по истечению срока действия ('.$auth_shortSessionLifetime.' мин.)', 3);
				$_JSON['no_redir'] = 1;
				$this->logout($_JSON);
				return false;
			}
		}
		$auth_inactiveSessionLifetime = $_JCMS->getConfig('auth_inactiveSessionLifetime');
		if( $auth_inactiveSessionLifetime < 1 ) $auth_inactiveSessionLifetime = 30; // default
		// проверяем последнюю активность... 
		if( $sess['lastActivity'] < time()-$auth_inactiveSessionLifetime*60){
			// $auth_inactiveSessionLifetime минут бездействия прошло... 
			$_SESSION["JENSENCMS"]['core_auth']['auto_logout'] = 1;
			$_JCMS->message("Cессия доступа была принудительно завершена из-за отсутствия активности в течение {$auth_inactiveSessionLifetime} мин.", "", 'error');
			$_JCMS->syslog->add("Сессия авторизации принудительно завершена из-за отсутствия активности в течение {$auth_inactiveSessionLifetime} мин.", 3);
			$_JSON['no_redir'] = 1;
			$this->logout($_JSON);
			return false;
		} else {
			$_SESSION["JENSENCMS"]['AUTH']['lastActivity'] = time();			
		}
		// проверяем разрешен ли доступ с указанного IP
		$allow_ip = $_JCMS->getConfig('auth_allowIP');
		if( !empty($allow_ip) ){
			// включен контроль доступа по IP (в списке есть хотя бы один IP)
			$allow_ip = explode(',',$allow_ip);
			$ip = $this->getUserIP();
			if( !in_array($ip, $allow_ip) ){
				$_SESSION["JENSENCMS"]['core_auth']['auto_logout'] = 1;
				$_JCMS->message("Cессия доступа была принудительно завершена!","", 'error');
				$_JCMS->syslog->add('Сессия авторизации принудительно завершена.<br />Доступ к панели управления с IP '.$ip.' - запрещён конфигурацией!', 3);
				$_JSON['no_redir'] = 1;
				$this->logout($_JSON);
				return false;
			}			
		}
		
		$sql_code = "SELECT * FROM `jcms2_admins` WHERE (`admin_login` = '{$sess[admin_login]}' OR `admin_email` = '{$sess[admin_email]}') AND `admin_password` = '{$sess[admin_password]}' AND `admin_status` = '1'";
		if( !$res = $_JCMS->db->query($sql_code) ){ return $_JCMS->message("Ошибка базы данных! [php/core.auth#".__LINE__."]", "MYSQL ERROR #".$_JCMS->db->errno." - ".$_JCMS->db->error, 'error'); }
		if( $res->num_rows !== 1 ){
			// расхождение данных в сессии юзера и в базе. Нужен повторный вход
			$_SESSION["JENSENCMS"]['core_auth']['auto_logout'] = 1;
			$_JCMS->message("Cессия доступа была принудительно завершена!", "Возможно, были изменены учётные данные аккаунта или Ваш аккаунт заблокирован. Попробуйте авторизоваться повторно или обратитесь к администратору.", 'error');
			$_JCMS->syslog->add('Сессия авторизации принудительно завершена из-за изменения учетных данных аккаунта.', 3);
			$_JSON['no_redir'] = 1;
			$this->logout($_JSON);
			return false;
		}
		$data = $res->fetch_assoc(); // актуальный профиль из базы
		foreach($data as $key=>$val){
			$_SESSION["JENSENCMS"]['AUTH'][$key] = $val; // обновляем поля из базы
		}

		if( $sess['admin_auth_token'] != $data['admin_auth_token'] ){
			$_SESSION["JENSENCMS"]['core_auth']['auto_logout'] = 1;
			$_JCMS->syslog->add('Попытка обхода защиты в системе авторизации!<br /><b>Логин:</b> '.$data['admin_login'].'<br>Токен авторизации сессии не совпадает с данными в куках. Возможно, была открыта новая сессия авторизация для этого аккаунта.', 1);
			$_JCMS->message("Hacking attempt!", '', 'error');
			$_JSON['no_redir'] = 1;
			$this->logout($_JSON);
			return false;
		}
		$sess = $_SESSION["JENSENCMS"]['AUTH'];

		// получаем текущие права группы
		$sql_code = "SELECT * FROM `jcms2_groups` WHERE `group_id` = '{$sess[group_id]}'";
		if( !$res = $_JCMS->db->query($sql_code) ){ return $_JCMS->message("Ошибка базы данных! [php/core.auth#".__LINE__."]", "MYSQL ERROR #".$_JCMS->db->errno." - ".$_JCMS->db->error, 'error'); }
		if( $res->num_rows === 1 ){
			$perm = $res->fetch_assoc();
			$_SESSION["JENSENCMS"]['AUTH']['permission'] = json_decode($perm['group_permission'],1);
			if( !is_array($_SESSION["JENSENCMS"]['AUTH']['permission']) ) $_SESSION["JENSENCMS"]['AUTH']['permission'] = array();
			
			foreach($perm as $key=>$val){
				if( in_array($key, array('group_permission')) ) continue;
				$_SESSION["JENSENCMS"]['AUTH'][$key] = $val;
			}
		}
		
		return true;
	}
	function getProfile(){
		$t = $_SESSION["JENSENCMS"]['AUTH'];
		return $t;
	}
	
	function getUserIP() {
		// check for shared internet/ISP IP
		if (!empty($_SERVER['HTTP_CLIENT_IP']) && $this->validate_ip($_SERVER['HTTP_CLIENT_IP'])) {
			return $_SERVER['HTTP_CLIENT_IP'];
		}
	
		// check for IPs passing through proxies
		if (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
			// check if multiple ips exist in var
			if (strpos($_SERVER['HTTP_X_FORWARDED_FOR'], ',') !== false) {
				$iplist = explode(',', $_SERVER['HTTP_X_FORWARDED_FOR']);
				foreach ($iplist as $ip) {
					if ($this->validate_ip($ip))
						return $ip;
				}
			} else {
				if ($this->validate_ip($_SERVER['HTTP_X_FORWARDED_FOR']))
					return $_SERVER['HTTP_X_FORWARDED_FOR'];
			}
		}
		if (!empty($_SERVER['HTTP_X_FORWARDED']) && $this->validate_ip($_SERVER['HTTP_X_FORWARDED']))
			return $_SERVER['HTTP_X_FORWARDED'];
		if (!empty($_SERVER['HTTP_X_CLUSTER_CLIENT_IP']) && $this->validate_ip($_SERVER['HTTP_X_CLUSTER_CLIENT_IP']))
			return $_SERVER['HTTP_X_CLUSTER_CLIENT_IP'];
		if (!empty($_SERVER['HTTP_FORWARDED_FOR']) && $this->validate_ip($_SERVER['HTTP_FORWARDED_FOR']))
			return $_SERVER['HTTP_FORWARDED_FOR'];
		if (!empty($_SERVER['HTTP_FORWARDED']) && $this->validate_ip($_SERVER['HTTP_FORWARDED']))
			return $_SERVER['HTTP_FORWARDED'];
	
		// return unreliable ip since all else failed
		return $_SERVER['REMOTE_ADDR'];
	}
	
	/**
	 * Ensures an ip address is both a valid IP and does not fall within
	 * a private network range.
	 */
	protected function validate_ip($ip) {
		if (strtolower($ip) === 'unknown')
			return false;
	
		// generate ipv4 network address
		$ip = ip2long($ip);
	
		// if the ip is set and not equivalent to 255.255.255.255
		if ($ip !== false && $ip !== -1) {
			// make sure to get unsigned long representation of ip
			// due to discrepancies between 32 and 64 bit OSes and
			// signed numbers (ints default to signed in PHP)
			$ip = sprintf('%u', $ip);
			// do private network range checking
			if ($ip >= 0 && $ip <= 50331647) return false;
			if ($ip >= 167772160 && $ip <= 184549375) return false;
			if ($ip >= 2130706432 && $ip <= 2147483647) return false;
			if ($ip >= 2851995648 && $ip <= 2852061183) return false;
			if ($ip >= 2886729728 && $ip <= 2887778303) return false;
			if ($ip >= 3221225984 && $ip <= 3221226239) return false;
			if ($ip >= 3232235520 && $ip <= 3232301055) return false;
			if ($ip >= 4294967040) return false;
		}
		return true;
	}	
}

// END.
?>