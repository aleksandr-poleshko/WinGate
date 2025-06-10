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

class CABINET implements JCMS_MODULE{

	var $tariff_periods = array(
		'1d' => array('title' => '1 день',	'interval' => '1 DAY'), 
		'7d' => array('title' => '7 дней',	'interval' => '7 DAY'),
		'1m' => array('title' => '1 месяц',	'interval' => '1 MONTH'),
		'3m' => array('title' => '3 месяца', 'interval'=> '3 MONTH'),
		'6m' => array('title' => '6 месяцев','interval'=> '6 MONTH'),
		'1y' => array('title' => '1 год', 	'interval' => '1 YEAR'),
	);
	
	var $order_status = array(
		'-1' => "заморожен",
		'0' => "в обработке",
		'1' => "активен",
		'2' => "завершён",
		'3' => "заблокирован",
	);
	
	var $tariff_status = array(
		'1' => "вкл.",
		'0' => "выкл.",
	);
	
	var $ticket_status = array(
		'0' => "ожидает ответа",
		'1' => "отвечен",
		'2' => "закрыт",
		'3' => "заблокирован",
	);
	
	/* инициаизация модуля */
	function setup(){
		$setup["title"] = "Модуль &laquo;Управление пользователями сайта&raquo;";
		$setup["descr"] = "";
		$cat = "modules"; 
		$setup["nav"][$cat]['title'] = "Модули";  
		$setup["nav"][$cat]['items'][] = array(
			"title" => "Пользователи сайта",
			"href" => "module/cabinet"
		);
		
		return $setup;
	}
	
	/* воркер, обрабатывающий запрос к этому модулю */
	function work(){
		global $_JCMS;
		$json = array();

		if( AJAX ){
			// AJAX CHAT
			if( $_POST['action'] == 'ticketDaemon' ){
				ob_end_clean();
				$json = array();
				$json['status'] = 0;
				$msg_id = $_JCMS->db->escape_string(intval($_POST['lastMsg']));
				if( !$res = $_JCMS->db->query("SELECT COUNT(*) as `count` FROM `jcms2_moduleClientsTickets` WHERE `ticket_status` = '0'")){
					$_JCMS->message("Ошибка базы данных! [php/module.cabinet#".__LINE__."]", $_JCMS->show_php_errors?("MYSQL ERROR #".$_JCMS->db->errno." - ".$_JCMS->db->error):'', 'error');
				} else {
					$data = $res->fetch_assoc();
					$json['status'] = 1;	
					$json['newThreads'] = intval($data['count']);
					$data = $res->fetch_assoc();
				}
				echo json_encode($json);
				exit();
			}
	
	
			
			// изменение пользователя
			if( $_JCMS->query[2] == 'edit_client' ){
				$json = $this->showPage_editClient(); 
				return $json;
			}
			// создание акции
			if( $_JCMS->query[2] == 'add_balance' ){
				$json = $this->showPage_addBalance();
				return $json;
			}		
			// изменение заказа
			if( $_JCMS->query[2] == 'edit_order' ){
				$json = $this->showPage_editOrder();
				return $json;
			}
			// создание акции
			if( $_JCMS->query[2] == 'add_tariff' ){
				$json = $this->showPage_addTariff();
				return $json;
			}		
			// изменение тарифа 
			if( $_JCMS->query[2] == 'edit_tariff' ){
				$json = $this->showPage_editTariff();
				return $json;
			}		
			// просмотр тикета
			if( $_JCMS->query[2] == 'view_ticket' ){
				$json = $this->showPage_viewTicket();
				return $json;
			}		
			// добавление email домена в блеклист
			if( $_JCMS->query[2] == 'add_email_blacklist' ){
				$json = $this->showPage_addEmailBlackList();
				return $json;
			}		
			// изменение email домена в блеклисте
			if( $_JCMS->query[2] == 'edit_email_blacklist' ){
				if( $_POST['action'] == 'delete_domain' ){
					$json = $this->actionEmailBlacklistDeleteDomain();  
				} else {
					$json = $this->showPage_editEmailBlackList();
				} 
				return $json;
			}
			
			// данные для таблицы (AJAX)
			if( $_GET['action'] == 'getTable1Data' ){
				$res = $this->getTable1DataAjax($_POST);
				$res['template'] = trim(ob_get_contents().$res['template']);
				ob_end_clean();
				exit(json_encode($res));
			}
			// данные для таблицы (AJAX)
			if( $_GET['action'] == 'getTable2Data' ){
				$res = $this->getTable2DataAjax($_POST);
				$res['template'] = trim(ob_get_contents().$res['template']);
				ob_end_clean();
				exit(json_encode($res));
			}
			// данные для таблицы (AJAX)
			if( $_GET['action'] == 'getTable3Data' ){
				$res = $this->getTable3DataAjax($_POST);
				$res['template'] = trim(ob_get_contents().$res['template']);
				ob_end_clean();
				exit(json_encode($res));
			}
			// данные для таблицы (AJAX)
			if( $_GET['action'] == 'getTable4Data' ){
				$res = $this->getTable4DataAjax($_POST);
				$res['template'] = trim(ob_get_contents().$res['template']);
				ob_end_clean();
				exit(json_encode($res));
			}
			// данные для таблицы (AJAX)
			if( $_GET['action'] == 'getTable5Data' ){
				$res = $this->getTable5DataAjax($_POST);
				$res['template'] = trim(ob_get_contents().$res['template']);
				ob_end_clean();
				exit(json_encode($res));
			}
			// данные для таблицы (AJAX)
			if( $_GET['action'] == 'getTable6Data' ){
				$res = $this->getTable6DataAjax($_POST);
				$res['template'] = trim(ob_get_contents().$res['template']);
				ob_end_clean();
				exit(json_encode($res));
			}
			// данные для таблицы (AJAX)
			if( $_GET['action'] == 'getTable7Data' ){
				$res = $this->getTable7DataAjax($_POST);
				$res['template'] = trim(ob_get_contents().$res['template']);
				ob_end_clean();
				exit(json_encode($res));
			}
			// данные для таблицы (AJAX)
			if( $_GET['action'] == 'getTable8Data' ){
				$res = $this->getTable8DataAjax($_POST);
				$res['template'] = trim(ob_get_contents().$res['template']);
				ob_end_clean();
				exit(json_encode($res));
			}
		}
		

		if( $_SESSION['JENSENCMS']['mod_cabinet']['editClient_success'] > 0 ){
			$id = $_SESSION['JENSENCMS']['mod_cabinet']['editClient_success'];
			$_JCMS->message('Редактирование аккаунта #'.$id.' &ndash; успешно выполнено!', '', 'notice');
			unset($_SESSION['JENSENCMS']['mod_cabinet']['editClient_success']);
		}

		if( $_SESSION['JENSENCMS']['mod_cabinet']['addBalance_success'] > 0 ){
			$id = $_SESSION['JENSENCMS']['mod_cabinet']['addBalance_success'];
			$_JCMS->message('Добавление фин. операции #'.$id.' &ndash; успешно выполнено!', '', 'notice');
			unset($_SESSION['JENSENCMS']['mod_cabinet']['addBalance_success']);
		}
		
		if( $_SESSION['JENSENCMS']['mod_cabinet']['editOrder_success'] > 0 ){
			$id = $_SESSION['JENSENCMS']['mod_cabinet']['editOrder_success'];
			$_JCMS->message('Редактирование заказа №'.$id.' &ndash; успешно выполнено!', '', 'notice');
			unset($_SESSION['JENSENCMS']['mod_cabinet']['editOrder_success']);
		}
		
		if( $_SESSION['JENSENCMS']['mod_cabinet']['addTariff_success'] > 0 ){
			$id = $_SESSION['JENSENCMS']['mod_cabinet']['addTariff_success'];
			$_JCMS->message('Добавление нового тарифа #'.$id.' &ndash; успешно выполнено!', '', 'notice');
			unset($_SESSION['JENSENCMS']['mod_cabinet']['addTariff_success']);
		}
		
		if( $_SESSION['JENSENCMS']['mod_cabinet']['editTariff_success'] > 0 ){
			$id = $_SESSION['JENSENCMS']['mod_cabinet']['editTariff_success'];
			$_JCMS->message('Редактирование тарифа #'.$id.' &ndash; успешно выполнено!', '', 'notice');
			unset($_SESSION['JENSENCMS']['mod_cabinet']['editTariff_success']);
		}	
		
		if( $_SESSION['JENSENCMS']['mod_cabinet']['addEmailBlackList_success'] > 0 ){
			$id = $_SESSION['JENSENCMS']['mod_cabinet']['addEmailBlackList_success'];
			$_JCMS->message('Добавления домена #'.$id.' &ndash; успешно выполнено!', '', 'notice');
			unset($_SESSION['JENSENCMS']['mod_cabinet']['addEmailBlackList_success']);
		}
		
		if( $_SESSION['JENSENCMS']['mod_cabinet']['editEmailBlackList_success'] > 0 ){
			$id = $_SESSION['JENSENCMS']['mod_cabinet']['editEmailBlackList_success'];
			$_JCMS->message('Изменение домена #'.$id.' &ndash; успешно выполнено!', '', 'notice');
			unset($_SESSION['JENSENCMS']['mod_cabinet']['editEmailBlackList_success']);
		}
		
		if( $_SESSION['JENSENCMS']['mod_cabinet']['deleteEmailBlackList_success'] > 0 ){
			$id = $_SESSION['JENSENCMS']['mod_cabinet']['deleteEmailBlackList_success'];
			$_JCMS->message('Удаление домена #'.$id.' &ndash; успешно выполнено!', '', 'notice');
			unset($_SESSION['JENSENCMS']['mod_cabinet']['deleteEmailBlackList_success']);
		}
		
		$_JCMS->tpl->load('cabinet/cabinetView.tpl');
		$_JCMS->tpl->tag('{CURRENT_TIMESTAMP}', date("d.m.Y H:i:s"));
		$json['result'] = 1; 
		$json['module_title'] = 'Управление пользователями сайта';
		$json['template'] = $_JCMS->tpl->compile();;
		$json['load_module'] = 'cabinet/cabinet';
		$json['callback'] = 'JCMS.modules.cabinet.showMainPage';
		
		return $json;
	}
	

	function showPage_editClient(){
		global $_JCMS;
		$id = $_JCMS->db->escape_string(intval($_GET['id']));
		
		if( $_POST['form_submit'] == 1 ){
			$json['result'] = 1;
			$json['callback'] = 'JCMS.modules.cabinet.result';
			$data = array();
			// экранируем входящие данные
			foreach($_POST as $key=>$val){ if(is_array($val)){ $data[$key]=$val;continue; } $data[$key] = trim($_JCMS->db->escape_string(strval($val))); } 
			
			// проверка входящих данных
			if( empty($data['client_email']) || !filter_var($data['client_email'], FILTER_VALIDATE_EMAIL) ){ $_JCMS->message("Ошибка: поле &laquo;Адрес эл. почты&raquo; не заполнено или заполнено неправильно!", "Проверьте правильность заполнения поля и попробуйте снова.", 'error'); return $json; }
			
			if( empty($data['client_login']) || !preg_match("/[a-z0-9]/i", $data['client_login']) || mb_strlen($data['client_login']) < 6 ){ $_JCMS->message("Ошибка: поле &laquo;Логин&raquo; не заполнено или заполнено неправильно!", "В этом поле допустимо использовать только латинские буквы и цифры. Минимальная длина логина 6 символов.<br>Проверьте правильность заполнения поля и попробуйте снова.", 'error'); return $json; }

			if( !empty($data['client_passw1']) || !empty($data['client_passw2']) ){
				if( empty($data['client_passw1']) || !preg_match("/[a-z0-9]/i", $data['client_passw1']) || mb_strlen($data['client_passw1']) < 8 ){ $_JCMS->message("Ошибка: поле &laquo;Пароль&raquo; не заполнено или заполнено неправильно!", "Пароль должен состоять из символов латинского алфавита и цифр. Минимальная длина пароля 8 символов.<br />Проверьте правильность заполнения поля и попробуйте снова.", 'error'); return $json; }
	
				if( empty($data['client_passw1']) || empty($data['client_passw2']) || $data['client_passw1'] !== $data['client_passw2'] ){ $_JCMS->message("Ошибка: введенные пароли не совпадают!", "Проверьте правильность заполнения полей и попробуйте снова.", 'error'); return $json; } else { $passw = ", `client_password` = '".md5($data['client_passw1'])."'"; }
			} else $passw = '';

			if( !in_array($data['client_status'], array('0', '1', '2')) ){ $_JCMS->message("Ошибка: в поле &laquo;Статус&raquo; ничего не выбрано!", "", 'error'); return $json; }
			
			$client_ref_rate = ', `client_ref_rate` = NULL';
			if( $data['client_ref_rate'] != '' && (floatval($data['client_ref_rate']) < 0 || floatval($data['client_ref_rate']) > 100) ){ $_JCMS->message("Ошибка: поле &laquo;Перс. партн. ставка&raquo; не заполнено или заполнено неправильно!", "Проверьте правильность заполнения поля и попробуйте снова.", 'error'); return $json; } elseif( $data['client_ref_rate'] != '' ){  $client_ref_rate = ", `client_ref_rate` = '".floatval($data['client_ref_rate'])."' "; }
			
			$sql_code = "UPDATE `jcms2_moduleClients` SET `client_login`='{$data[client_login]}', `client_email`='{$data[client_email]}', `client_status`='{$data[client_status]}', `client_email`='{$data[client_email]}', `client_adminNotes` = '{$data[client_adminNotes]}' {$passw} {$client_ref_rate} WHERE `client_id` = '{$id}' LIMIT 1";

			if( !$res = $_JCMS->db->query($sql_code) ){
				$_JCMS->message("Ошибка базы данных! [php/module.cabinet#".__LINE__."]", "MYSQL ERROR #".$_JCMS->db->errno." - ".$_JCMS->db->error, 'error');
				return $json;
			}
			$profile = $_JCMS->auth->getProfile();
			$_JCMS->syslog->add('Редактирование аккаунта #'.$id.'.',4);
			$json['edit'] = 'success';
			$_SESSION['JENSENCMS']['mod_cabinet']['editClient_success'] = $id;
			
			return $json;
		} // end if

		$sql_code = "
		SELECT *,
			(SELECT SUM(`summ`) FROM `jcms2_moduleClientsBalance` as `b` WHERE `b`.`client_id` = `a`.`client_id`) as `client_balance`,
			(SELECT SUM(`summ`) FROM `jcms2_moduleClientsBalance` as `b` WHERE `b`.`client_id` = `a`.`client_id` AND `b`.`summ` > 0 AND `b`.`ref_id` IS NULL) as `client_oborot`,
			(SELECT SUM(`summ`) FROM `jcms2_moduleClientsBalance` as `b` WHERE `b`.`client_id` = `a`.`client_id` AND `b`.`summ` > 0 AND `b`.`ref_id` IS NOT NULL) as `client_partn_oborot`
		FROM `jcms2_moduleClients` as `a`
		WHERE `client_id` = '{$id}'
";
		if( !$res = $_JCMS->db->query($sql_code) ){ $_JCMS->message("Ошибка базы данных! [php/module.cabinet#".__LINE__."]", "MYSQL ERROR #".$_JCMS->db->errno." - ".$_JCMS->db->error, 'error'); return $json; }
		if( $res->num_rows !== 1 ){
			$_JCMS->message("Ошибка: запрошенный пользователь не существует! [php/module.cabinet#".__LINE__."]", "", 'error'); return $json;
		}
		$data = $res->fetch_assoc();

		$json['result'] = 1;
		$json['client_status'] = $data['client_status'];
		$json['client_adminNotes'] = $data['client_adminNotes'];
		$json['module_title'] = 'Редактирование аккаунта #'.$id.' / Управление пользователями сайта';
		$_JCMS->tpl->load("cabinet/cabinetEditClient.tpl");
		foreach($data as $key=>$val){ $_JCMS->tpl->tag("{".strtoupper($key)."}", $val); }
		$_JCMS->tpl->tag("{ID}", $id);
		$_JCMS->tpl->tag("{CLIENT_LASTAUTHDATE}", date('d.m.Y H:i:s', strtotime($data['client_lastAuthDate'])));
		$_JCMS->tpl->tag("{CLIENT_REGDATE}", date('d.m.Y H:i:s', strtotime($data['client_regDate'])));
		$flag = $this->getGeoIp($data['client_regIP']);
		$_JCMS->tpl->tag("{CLIENT_REGIP_FLAG_TITLE}", $flag['title']);
		$_JCMS->tpl->tag("{CLIENT_REGIP_FLAG_IMG}", $flag['img']);
		$flag = $this->getGeoIp((!empty($data['client_lastIP'])?$data['client_lastIP']:$data['client_regIP']));
		$_JCMS->tpl->tag("{CLIENT_LASTIP_FLAG_TITLE}", $flag['title']);
		$_JCMS->tpl->tag("{CLIENT_LASTIP_FLAG_IMG}", $flag['img']);

		$_JCMS->tpl->tag("{CLIENT_BALANCE}", round(floatval($data['client_balance']),2));
		$_JCMS->tpl->tag("{CLIENT_OBOROT}", round(floatval($data['client_oborot']),2));
		$_JCMS->tpl->tag("{CLIENT_PARTN_OBOROT}", round(floatval($data['client_partn_oborot']),2));

		$sql_code = "
		SELECT *,
			(SELECT SUM(`summ`) FROM `jcms2_moduleClientsBalance` as `b` WHERE `b`.`ref_id` = `a`.`client_id`) as `client_referal_reward`
		FROM `jcms2_moduleClients` as `a`
		WHERE `client_ref_id` = '{$data['client_id']}'";
		if( !$res2 = $_JCMS->db->query($sql_code) ){ $_JCMS->message("Ошибка базы данных! [php/module.cabinet#".__LINE__."]", "MYSQL ERROR #".$_JCMS->db->errno." - ".$_JCMS->db->error, 'error'); return $json; }
		if( $res2->num_rows > 0 ){
			while( $data2 = $res2->fetch_assoc() ){
				unset($flag);
				$partn_table .= "<tr data-client_id=\"{$data2['client_id']}\">";
					$partn_table .= "<td>{$data2['client_login']}</td>";
					$partn_table .= "<td>\${$data2['client_referal_reward']}</td>";
					$flag = $this->getGeoIp($data2['client_regIP']);
					$url = "https://2ip.com.ua/ru/services/information-service/site-location?ip={$data2['client_regIP']}&a=act";
					$partn_table .= "<td nowrap><img src=\"{$_JCMS->getConfig('site_url')}/include/geo_ip/img/{$flag['img']}.png\" title=\"{$flag['title']}\"/> <a href=\"{$url}\" onclick=\"window.open('{$url}');\" target=\"_blank\">{$data2['client_regIP']}</a></td>";
					$ip = (!empty($data['client_lastIP'])?$data['client_lastIP']:$data['client_regIP']);
					$flag = $this->getGeoIp($ip);
					$url = "https://2ip.com.ua/ru/services/information-service/site-location?ip={$ip}&a=act";
					$partn_table .= "<td nowrap><img src=\"{$_JCMS->getConfig('site_url')}/include/geo_ip/img/{$flag['img']}.png\" title=\"{$flag['title']}\"/> <a href=\"{$url}\" onclick=\"window.open('{$url}');\" target=\"_blank\">{$ip}</a></td>";
					$partn_table .= "<td>".date('d.m.Y H:i:s', strtotime($data['client_lastAuthDate']))."</td>";
					$partn_table .= "<td>".htmlentities($data['client_lastUserAgent'], ENT_QUOTES, 'utf-8')."</td>";				
				$partn_table .= "</tr>";
			}
		} else {
			$partn_table = "<tr class=\"noselect\"><td colspan=\"6\" style=\"height:100px\">нет рефералов</td></tr>";
		}
		$_JCMS->tpl->tag("{CLIENT_PARTN_TBODY}", $partn_table);

		$json['template'] = $_JCMS->tpl->compile();
		$json['template'] = preg_replace("/\{[\w-]+\}/i", "", $json['template']);
		$json['load_module'] = 'cabinet/cabinet';
		$json['callback'] = 'JCMS.modules.cabinet.showPage_editClient';
		
		return $json;
	}
	 
	function showPage_addBalance(){
		global $_JCMS;
		
		if( $_POST['form_submit'] == 1 ){
			$json['result'] = 1;
			$json['callback'] = 'JCMS.modules.cabinet.result';
			$data = array();
			// экранируем входящие данные
			foreach($_POST as $key=>$val){ if(is_array($val)){ $data[$key]=$val;continue; } $data[$key] = trim($_JCMS->db->escape_string(strval($val))); } 
			
			// проверка входящих данных
			if( empty($data['bal_client']) || $data['bal_client'] < 0 ){ $_JCMS->message("Ошибка: в поле &laquo;Пользователь&raquo; ничего не выбрано!", "Проверьте правильность заполнения поля и попробуйте снова.", 'error'); return $json; }

			if( empty($data['bal_date']) ){ $_JCMS->message("Ошибка: поле &laquo;Дата&raquo; не заполнено или заполнено неправильно!", "Проверьте правильность заполнения поля и попробуйте снова.", 'error'); return $json; }

			if( empty($data['bal_time']) ){ $_JCMS->message("Ошибка: поле &laquo;Время&raquo; не заполнено или заполнено неправильно!", "Проверьте правильность заполнения поля и попробуйте снова.", 'error'); return $json; }
			
			$date = strtotime($data['bal_date'].' '.$data['bal_time']);

			if( !$date || $date > time() ){ $_JCMS->message("Ошибка: указана неверная или ещё ненаступившая дата!", "Проверьте правильность заполнения полей и попробуйте снова.", 'error'); return $json; }
  			
			$date = date("Y-m-d H:i:s", $date);

			if( empty($data['bal_summ']) || floatval($data['bal_summ']) == 0 ){ $_JCMS->message("Ошибка: поле &laquo;Сумма&raquo; не заполнено или заполнено неправильно!", "Проверьте правильность заполнения поля и попробуйте снова.", 'error'); return $json; }

  			$data['bal_summ'] = floatval($data['bal_summ']);

			if( empty($data['bal_descr']) || mb_strlen(trim(strip_tags($data['bal_descr'])), 'utf-8') < 1 ){ $_JCMS->message("Ошибка: поле &laquo;Описание&raquo; не заполнено!", "Проверьте правильность заполнения поля и попробуйте снова.", 'error'); return $json; }
				
			$sql_code = "INSERT INTO `jcms2_moduleClientsBalance`(`client_id`, `summ`, `descr`, `date`) VALUES ('{$data[bal_client]}', '{$data[bal_summ]}', '{$data[bal_descr]}', '{$date}')";
			
			if( !$res = $_JCMS->db->query($sql_code) ){
				$_JCMS->message("Ошибка базы данных! [php/module.cabinet#".__LINE__."]", "MYSQL ERROR #".$_JCMS->db->errno." - ".$_JCMS->db->error, 'error');
				return $json;
			}
			$id = $_JCMS->db->insert_id;
			$_JCMS->syslog->add('Добавление фин. операции #'.$id.'.',4);
			$json['add'] = 'success';
			$_SESSION['JENSENCMS']['mod_cabinet']['addBalance_success'] = $id; 
			
			return $json;
		} // end if

		
		$sql_code = "SELECT * FROM `jcms2_moduleClients` ORDER BY `client_id` ASC, `client_status` DESC";
		if( !$res = $_JCMS->db->query($sql_code) ){
			$_JCMS->message("Ошибка базы данных! [php/module.cabinet#".__LINE__."]", "MYSQL ERROR #".$_JCMS->db->errno." - ".$_JCMS->db->error, 'error');
			return $json;
		}
		$clients_list = '';
		while( $data = $res->fetch_assoc() ){
			if( $_REQUEST['client_id'] == $data['client_id'] ) $sel = 'selected'; else $sel = '';
			$clients_list .= "<option class=\"st{$data[client_status]}\" {$sel} value=\"{$data[client_id]}\">[#{$data['client_id']}] {$data['client_login']} &lt;{$data['client_email']}&gt;</option>";
		}
		
		$json['result'] = 1;
		$json['module_title'] = 'Добавление фин. операции / Управление пользователями сайта';
		$_JCMS->tpl->load("cabinet/cabinetAddBalance.tpl");
		$_JCMS->tpl->tag("{CLIENTS_LIST}", $clients_list);
		$_JCMS->tpl->tag("{BAL_DATE}", date('d.m.Y'));
		$_JCMS->tpl->tag("{BAL_TIME}", date('H:i:s'));
		$json['template'] = $_JCMS->tpl->compile();
		$json['template'] = preg_replace("/\{[\w-]+\}/i", "", $json['template']);
		$json['load_module'] = 'cabinet/cabinet';
		$json['callback'] = 'JCMS.modules.cabinet.showPage_addBalance';
		return $json;
	}

	function showPage_editOrder(){
		global $_JCMS;
		$id = $_JCMS->db->escape_string(intval($_GET['id']));
		
		if( $_POST['form_submit'] == 1 ){
			$json['result'] = 1;
			$json['callback'] = 'JCMS.modules.cabinet.result';
			$data = array();
			// экранируем входящие данные
			foreach($_POST as $key=>$val){ if(is_array($val)){ $data[$key]=$val;continue; } $data[$key] = trim($_JCMS->db->escape_string(strval($val))); } 
			
			// проверка входящих данных
			if( empty($data['order_date']) ){ $_JCMS->message("Ошибка: поле &laquo;Дата окончания&raquo; не заполнено или заполнено неправильно!", "Проверьте правильность заполнения поля и попробуйте снова.", 'error'); return $json; }

			if( empty($data['order_time']) ){ $_JCMS->message("Ошибка: поле &laquo;Время окончания&raquo; не заполнено или заполнено неправильно!", "Проверьте правильность заполнения поля и попробуйте снова.", 'error'); return $json; }
			
			$date = strtotime($data['order_date'].' '.$data['order_time']);

			if( !$date ){ $_JCMS->message("Ошибка: указана неверная дата!", "Проверьте правильность заполнения полей и попробуйте снова.", 'error'); return $json; }

			$sql_code = "SELECT * FROM `jcms2_moduleClientsOrders` WHERE `order_id` = '{$id}'";
			if( !$res = $_JCMS->db->query($sql_code) ){
				$_JCMS->message("Ошибка базы данных! [php/module.cabinet#".__LINE__."]", "MYSQL ERROR #".$_JCMS->db->errno." - ".$_JCMS->db->error, 'error');
				return $json;
			}
			$_data = $res->fetch_assoc();
			
			if( $date < strtotime($_data['order_addDate']) ){ $_JCMS->message("Ошибка: &laquo;Дата окончания&raquo; должны быть больше &laquo;Дата начала&raquo;", "Проверьте правильность заполнения полей и попробуйте снова.", 'error'); return $json; }
			  			
			$date = date("Y-m-d H:i:s", $date);

			$ip = array();
			foreach($_POST['order_ip'] as $val){							
				if( empty($val) ) continue;
				if( filter_var($val, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE|FILTER_FLAG_NO_RES_RANGE|FILTER_FLAG_IPV4) ){
					$ip[] = $val;
				} else {
					$json['error'] = '"'.$val.'" - неверный IP адрес!';
					break;
				}
				if( count($ip) >= 2 ) break;
			}
			if( $json['error'] ){
				$_JCMS->message("Ошибка: ".$json['error'], "Проверьте правильность заполнения полей и попробуйте снова.", 'error');
				 return $json;
			}

			if( $data['order_status'] != -1 && !in_array($data['order_status'], array('0', '1', '2', '3')) ){ $_JCMS->message("Ошибка: в поле &laquo;Статус&raquo; ничего не выбрано!", "", 'error'); return $json; }
			if( $data['order_status'] == -1 ){
				// заказ залочен, основной статус не меняем
				$data['order_status'] = '1';
				$_data['order_isLock'] = '1';
			} else {
				// заказ не залочен
				$_data['order_isLock'] = '0';
			}

			$_data['order_data'] = json_decode($_data['order_data'], 1);
			$_data['order_data']['ip'] = $ip;
			$_data['order_data'] = json_encode($_data['order_data']);

						
			$sql_code = "UPDATE `jcms2_moduleClientsOrders` SET `order_data`='{$_data[order_data]}', `order_paidBefore`='{$date}', `order_status`='{$data[order_status]}', `order_isLock`='{$_data[order_isLock]}' WHERE `order_id` = '{$id}' LIMIT 1";

			if( !$res = $_JCMS->db->query($sql_code) ){
				$_JCMS->message("Ошибка базы данных! [php/module.cabinet#".__LINE__."]", "MYSQL ERROR #".$_JCMS->db->errno." - ".$_JCMS->db->error, 'error');
				return $json;
			}
			$profile = $_JCMS->auth->getProfile();
			$_JCMS->syslog->add('Редактирование заказа #'.$id.'.',4);
			$json['edit'] = 'success';
			$_SESSION['JENSENCMS']['mod_cabinet']['editOrder_success'] = $id;
			
			return $json;
		} // end if

		$sql_code = "
		SELECT SQL_CALC_FOUND_ROWS `a`.*,
			(SELECT SUM(`summ`) FROM `jcms2_moduleClientsBalance` as `d` WHERE `d`.`order_id` = `a`.`order_id` AND `d`.`summ` < 0) as `order_summ`,
			`b`.`tariff_id`,
			`b`.`tariff_title`,
			`c`.`client_login`,
			`c`.`client_email`
		FROM `jcms2_moduleClientsOrders` as `a`
			LEFT JOIN `jcms2_moduleClientsTariffs` as `b` ON `b`.`tariff_id` = `a`.`tariff_id`
			LEFT JOIN `jcms2_moduleClients` as `c` ON `c`.`client_id` = `a`.`client_id`
		WHERE `a`.`order_id` = '{$id}'
";
		if( !$res = $_JCMS->db->query($sql_code) ){ $_JCMS->message("Ошибка базы данных! [php/module.cabinet#".__LINE__."]", "MYSQL ERROR #".$_JCMS->db->errno." - ".$_JCMS->db->error, 'error'); return $json; }
		if( $res->num_rows !== 1 ){
			$_JCMS->message("Ошибка: запрошенный заказ не существует! [php/module.cabinet#".__LINE__."]", "", 'error'); return $json;
		}
		$data = $res->fetch_assoc();
		$data['order_data'] = json_decode($data['order_data'], 1);
		if( !is_array($data['order_data']['ip']) ) $data['order_data']['ip'] = array();

		$json['result'] = 1;
		$json['order_status'] = $data['order_isLock']==1?-1:$data['order_status'];
		$json['isTestOrder'] = $data['order_isTest'];
		$json['isTestFake'] = $data['order_isTestFake'];
		$json['module_title'] = 'Редактирование заказа №'.$id.' / Управление пользователями сайта';
		$_JCMS->tpl->load("cabinet/cabinetEditOrder.tpl");
		foreach($data as $key=>$val){ $_JCMS->tpl->tag("{".strtoupper($key)."}", $val); }
		$_JCMS->tpl->tag("{ID}", $id);
		$_JCMS->tpl->tag("{ORDER_ADDDATE}", date('d.m.Y H:i:s', strtotime($data['order_addDate'])));
		$_JCMS->tpl->tag("{ORDER_DATE}", date('d.m.Y', strtotime($data['order_paidBefore'])));
		$_JCMS->tpl->tag("{ORDER_TIME}", date('H:i:s', strtotime($data['order_paidBefore'])));
		$_JCMS->tpl->tag("{ORDER_SUMM}", abs(round(floatval($data['order_summ']),2)));
		$_JCMS->tpl->tag("{ORDER_IP1}", $data['order_data']['ip'][0]);
		$_JCMS->tpl->tag("{ORDER_IP2}", $data['order_data']['ip'][1]);
		$_JCMS->tpl->tag("{CURRENT_TIMESTAMP}", date('d.m.Y H:i:s'));
		$json['template'] = $_JCMS->tpl->compile();
		$json['template'] = preg_replace("/\{[\w-]+\}/i", "", $json['template']);
		$json['load_module'] = 'cabinet/cabinet';
		$json['callback'] = 'JCMS.modules.cabinet.showPage_editOrder';
		
		return $json;
	}

	function showPage_addTariff(){
		global $_JCMS;
		
		if( $_POST['form_submit'] == 1 ){
			$json['result'] = 1;
			$json['callback'] = 'JCMS.modules.cabinet.result';
			$data = array();
			// экранируем входящие данные
			foreach($_POST as $key=>$val){ if(is_array($val)){ $data[$key]=$val;continue; } $data[$key] = trim($_JCMS->db->escape_string(strval($val))); } 
			
			// проверка входящих данных
			if( empty($data['tariff_title']) ){ $_JCMS->message("Ошибка: поле &laquo;Название&raquo; не заполнено или заполнено неправильно!", "Проверьте правильность заполнения поля и попробуйте снова.", 'error'); return $json; }

			if( empty($data['tariff_descr']) ){ $_JCMS->message("Ошибка: поле &laquo;Название&raquo; не заполнено или заполнено неправильно!", "Проверьте правильность заполнения поля и попробуйте снова.", 'error'); return $json; }
			
			$tariff_prices = array();
			foreach($data['tariff'] as $key=>$val){
				if( !$this->tariff_periods[$key]/* || floatval($data['tariff'][$key]) < 0*/ ){ $_JCMS->message("Ошибка: поле &laquo;Стоимость&raquo; не заполнено или заполнено неправильно!", "Проверьте правильность заполнения поля и попробуйте снова.", 'error'); return $json; }
				if( $val == '' ) continue;
				$tariff_prices[$key] = floatval($data['tariff'][$key]);
			} 
			$tariff_prices = json_encode($tariff_prices); 
			
			
			if( !in_array($data['tariff_status'], array('0', '1')) ){ $_JCMS->message("Ошибка: в поле &laquo;Статус&raquo; ничего не выбрано!", "", 'error'); return $json; }

			$sql_code = "INSERT INTO `jcms2_moduleClientsTariffs`(`tariff_title`, `tariff_descr`, `tariff_prices`, `tariff_status`) VALUES ('{$data[tariff_title]}', '{$data[tariff_descr]}', '{$tariff_prices}', '{$data['tariff_status']}')";
			
			if( !$res = $_JCMS->db->query($sql_code) ){
				$_JCMS->message("Ошибка базы данных! [php/module.cabinet#".__LINE__."]", "MYSQL ERROR #".$_JCMS->db->errno." - ".$_JCMS->db->error, 'error');
				return $json;
			}
			$id = $_JCMS->db->insert_id;
			$_JCMS->syslog->add('Добавление нового тарифа #'.$id.'.',4);
			$json['add'] = 'success';
			$_SESSION['JENSENCMS']['mod_cabinet']['addTariff_success'] = $id; 
			
			return $json;
		} // end if

		foreach($this->tariff_periods as $key=>$val){
			$tariffs_list .= '<div class="input-group" style="width:200px; margin-bottom:5px;"><span class="input-group-addon" style="width:140px; text-align:right;">'.$val['title'].' &rArr; $</span><input type="text" class="form-control"  name="tariff['.$key.']" placeholder="" /></div>';
		}
				
		$json['result'] = 1;
		$json['module_title'] = 'Добавление нового тарифа / Управление пользователями сайта';
		$_JCMS->tpl->load("cabinet/cabinetAddTariff.tpl");
		$_JCMS->tpl->tag("{TARIFFS_LIST}", $tariffs_list);
		$_JCMS->tpl->tag("{CURRENT_TIMESTAMP}", date('d.m.Y H:i:s'));
		
		$json['template'] = $_JCMS->tpl->compile();
		$json['template'] = preg_replace("/\{[\w-]+\}/i", "", $json['template']);
		$json['load_module'] = 'cabinet/cabinet';
		$json['callback'] = 'JCMS.modules.cabinet.showPage_addTariff';
		return $json;
	} 

	function showPage_editTariff(){
		global $_JCMS;
		$id = $_JCMS->db->escape_string(intval($_GET['id']));
		
		if( $_POST['form_submit'] == 1 ){
			$json['result'] = 1;
			$json['callback'] = 'JCMS.modules.cabinet.result';
			$data = array();
			// экранируем входящие данные
			foreach($_POST as $key=>$val){ if(is_array($val)){ $data[$key]=$val;continue; } $data[$key] = trim($_JCMS->db->escape_string(strval($val))); } 
			
			// проверка входящих данных
			if( empty($data['tariff_title']) ){ $_JCMS->message("Ошибка: поле &laquo;Название&raquo; не заполнено или заполнено неправильно!", "Проверьте правильность заполнения поля и попробуйте снова.", 'error'); return $json; }

			if( empty($data['tariff_descr']) ){ $_JCMS->message("Ошибка: поле &laquo;Название&raquo; не заполнено или заполнено неправильно!", "Проверьте правильность заполнения поля и попробуйте снова.", 'error'); return $json; }
			
			$tariff_prices = array();
			foreach($data['tariff'] as $key=>$val){
				if( !$this->tariff_periods[$key]/* || floatval($data['tariff'][$key]) < 0*/ ){ $_JCMS->message("Ошибка: поле &laquo;Стоимость&raquo; не заполнено или заполнено неправильно!", "Проверьте правильность заполнения поля и попробуйте снова.", 'error'); return $json; }
				if( $val == '' ) continue;
				$tariff_prices[$key] = floatval($data['tariff'][$key]);
			}
			
			$tariff_prices = json_encode($tariff_prices); 			
			
			if( !in_array($data['tariff_status'], array('0', '1')) ){ $_JCMS->message("Ошибка: в поле &laquo;Статус&raquo; ничего не выбрано!", "", 'error'); return $json; }
		
			$sql_code = "UPDATE `jcms2_moduleClientsTariffs` SET `tariff_title`='{$data['tariff_title']}', `tariff_descr`='{$data['tariff_descr']}', `tariff_prices`='{$tariff_prices}', `tariff_status`='{$data['tariff_status']}' WHERE `tariff_id` = '{$id}' LIMIT 1";

			if( !$res = $_JCMS->db->query($sql_code) ){
				$_JCMS->message("Ошибка базы данных! [php/module.cabinet#".__LINE__."]", "MYSQL ERROR #".$_JCMS->db->errno." - ".$_JCMS->db->error, 'error');
				return $json;
			} 
			$profile = $_JCMS->auth->getProfile();
			$_JCMS->syslog->add('Редактирование тарифа #'.$id.'.',4);
			$json['edit'] = 'success';
			$_SESSION['JENSENCMS']['mod_cabinet']['editTariff_success'] = $id;
			
			return $json;
		} // end if

		$sql_code = "
		SELECT SQL_CALC_FOUND_ROWS *
		FROM `jcms2_moduleClientsTariffs`
		WHERE `tariff_id` = '{$id}'
";
		if( !$res = $_JCMS->db->query($sql_code) ){ $_JCMS->message("Ошибка базы данных! [php/module.cabinet#".__LINE__."]", "MYSQL ERROR #".$_JCMS->db->errno." - ".$_JCMS->db->error, 'error'); return $json; }
		if( $res->num_rows !== 1 ){
			$_JCMS->message("Ошибка: запрошенный тариф не существует! [php/module.cabinet#".__LINE__."]", "", 'error'); return $json;
		}
		$data = $res->fetch_assoc();
		$data['tariff_prices'] = json_decode($data['tariff_prices'], 1);
		
		foreach($this->tariff_periods as $key=>$val){
			$tariffs_list .= '<div class="input-group" style="width:200px; margin-bottom:5px;"><span class="input-group-addon" style="width:140px; text-align:right;">'.$val['title'].' &rArr; $</span><input type="text" class="form-control"  name="tariff['.$key.']" placeholder="" value="'.$data['tariff_prices'][$key].'" /></div>';
		}

		$json['result'] = 1;
		$json['tariff_status'] = $data['tariff_status'];
		$json['module_title'] = 'Редактирование тарифа #'.$id.' / Управление пользователями сайта';
		$_JCMS->tpl->load("cabinet/cabinetEditTariff.tpl");
		foreach($data as $key=>$val){ $_JCMS->tpl->tag("{".strtoupper($key)."}", $val); }
		$_JCMS->tpl->tag("{ID}", $id);
		$_JCMS->tpl->tag("{TARIFFS_LIST}", $tariffs_list);
		$json['template'] = $_JCMS->tpl->compile();
		$json['template'] = preg_replace("/\{[\w-]+\}/i", "", $json['template']);
		$json['load_module'] = 'cabinet/cabinet';
		$json['callback'] = 'JCMS.modules.cabinet.showPage_editTariff';
		
		return $json;
	}

	function showPage_viewTicket(){
		global $_JCMS;
		$id = $_JCMS->db->escape_string(intval($_GET['id']));
		
		if( $_POST['form_submit'] == 1 ){
			$json['result'] = 1;
			$json['callback'] = 'JCMS.modules.cabinet.result';
			$data = array();
			// экранируем входящие данные
			foreach($_POST as $key=>$val){ if(is_array($val)){ $data[$key]=$val;continue; } $data[$key] = trim($_JCMS->db->escape_string(strval($val))); } 
			
			$profile = $_JCMS->auth->getProfile(); 
			if( !$res = $_JCMS->db->query("SELECT * FROM `jcms2_moduleClientsTickets` WHERE `ticket_id` = '{$id}'")){
				$_JCMS->message("Ошибка базы данных! [php/module.cabinet#".__LINE__."]", $_JCMS->show_php_errors?("MYSQL ERROR #".$_JCMS->db->errno." - ".$_JCMS->db->error):'', 'error');
			} elseif( $res->num_rows == 1 ){
				$data2 = $res->fetch_assoc();
				// AJAX CHAT
				if( $_POST['action'] == 'getChatLog' ){
					ob_end_clean();
					$json = array();
					$json['status'] = 0;
					$msg_id = $_JCMS->db->escape_string(intval($_POST['lastMsg']));
					if( !$res = $_JCMS->db->query("SELECT * FROM `jcms2_moduleClientsTicketsMsgs` WHERE `ticket_id` = '{$id}' AND `msg_id` > '{$msg_id}' ORDER BY `msg_date` ASC")){
						$_JCMS->message("Ошибка базы данных! [php/module.cabinet#".__LINE__."]", $_JCMS->show_php_errors?("MYSQL ERROR #".$_JCMS->db->errno." - ".$_JCMS->db->error):'', 'error');
					} else {
						$json['status'] = 1;	
						$json['ticket_status'] = $data2['ticket_status'];	
						while($data = $res->fetch_assoc() ){
							$json['chatlog'][] = array(
								'msg'	=> $data['msg_id'],
								'text'	=> $data['msg_text'],
								'type'	=> ($data['admin_id']!=NULL?'admin':'client'),
								'time'	=> date("H:i", strtotime($data['msg_date'])),
								'timestamp'	=> date("d.m.Y H:i", strtotime($data['msg_date'])),
							);
							if( $data['msg_id'] > $json['lastMsg'] ) $json['lastMsg'] = $data['msg_id'];
						}
					}
					$json['ticket_id'] = $id;
					echo json_encode($json);
					exit();
				}
				if( $_POST['action'] == 'newMsg' ){
					ob_end_clean();
					$json = array();
					$json['status'] = 0;
					$text = $_JCMS->db->escape_string(str_replace("\n", "<br>", trim(htmlentities(strip_tags($_POST['text']),ENT_QUOTES, 'utf-8'))));
					if( !empty($text) ){
						$data = $res->fetch_assoc();
						if( $data['ticket_status'] != 1 ){ 
							// меняем статус на "ожидает ответа админа"
							$sql_code = "UPDATE `jcms2_moduleClientsTickets` SET `ticket_status` = '1' WHERE `ticket_id` = '{$id}'";
							if( !$res = $_JCMS->db->query($sql_code)){
								$_JCMS->message("Ошибка базы данных! [php/module.cabinet#".__LINE__."]", $_JCMS->show_php_errors?("MYSQL ERROR #".$_JCMS->db->errno." - ".$_JCMS->db->error):'', 'error');
								return;
							} 								
						}
						// добавляем сообщение...
						$sql_code = "INSERT INTO `jcms2_moduleClientsTicketsMsgs`(`ticket_id`, `msg_text`, `admin_id`) VALUES ('{$id}', '{$text}', '{$profile[admin_id]}')";
						if( !$res = $_JCMS->db->query($sql_code)){
							$_JCMS->message("Ошибка базы данных! [php/module.cabinet#".__LINE__."]", $_JCMS->show_php_errors?("MYSQL ERROR #".$_JCMS->db->errno." - ".$_JCMS->db->error):'', 'error');
						} else {
							$json['status'] = 1; 
						}						
					}
					
					echo json_encode($json);
					exit();
				}
				if( $_POST['action'] == 'newStatus' ){
					ob_end_clean();
					$json = array();
					$json['status'] = 0;
					$ticket_status = $_JCMS->db->escape_string(intval($_POST['ticket_status']));
						// меняем статус на "ожидает ответа админа"
						$sql_code = "UPDATE `jcms2_moduleClientsTickets` SET `ticket_status` = '{$ticket_status}' WHERE `ticket_id` = '{$id}'";
						if( !$res = $_JCMS->db->query($sql_code)){
							$_JCMS->message("Ошибка базы данных! [php/module.cabinet#".__LINE__."]", $_JCMS->show_php_errors?("MYSQL ERROR #".$_JCMS->db->errno." - ".$_JCMS->db->error):'', 'error');
							return;
						} 								
						$json['status'] = 1;
					
					echo json_encode($json);
					exit();
				}
			}
						
			return $json;
		} // end if

		$sql_code = "
		SELECT `a`.*,
			`b`.`client_login`,
			`b`.`client_email`
		FROM `jcms2_moduleClientsTickets` as `a`
			LEFT JOIN `jcms2_moduleClients` as `b` ON `b`.`client_id` = `a`.`client_id`
		WHERE `ticket_id` = '{$id}'
";
		if( !$res = $_JCMS->db->query($sql_code) ){ $_JCMS->message("Ошибка базы данных! [php/module.cabinet#".__LINE__."]", "MYSQL ERROR #".$_JCMS->db->errno." - ".$_JCMS->db->error, 'error'); return $json; }
		if( $res->num_rows !== 1 ){
			$_JCMS->message("Ошибка: запрошенный тикет не существует! [php/module.cabinet#".__LINE__."]", "", 'error'); return $json;
		}
		$data = $res->fetch_assoc();
		
		foreach($this->ticket_status as $key=>$val){
			if( $key == $data['ticket_status'] ) $sel = ' selected'; else $sel = '';
			$ticket_status .= "<option {$sel} value=\"{$key}\">{$val}</option>";
		}
		
		$json['result'] = 1;
		$json['ticket_id'] = $data['ticket_id'];
		$json['module_title'] = 'Тикет #'.$id.' / Управление пользователями сайта';
		$_JCMS->tpl->load("cabinet/cabinetViewTicket.tpl");
		foreach($data as $key=>$val){ $_JCMS->tpl->tag("{".strtoupper($key)."}", $val); }
		$_JCMS->tpl->tag("{ID}", $id);
		$_JCMS->tpl->tag("{TICKET_STATUS}", $ticket_status);
		$_JCMS->tpl->tag("{TICKET_ADDDATE}", date('d.m.Y H:i:s', strtotime($data['ticket_addDate'])));

		$json['template'] = $_JCMS->tpl->compile();
		$json['template'] = preg_replace("/\{[\w-]+\}/i", "", $json['template']);
		$json['load_module'] = 'cabinet/cabinet';
		$json['callback'] = 'JCMS.modules.cabinet.showPage_viewTicket';
		
		return $json;
	}



	function showPage_addEmailBlackList(){
		global $_JCMS;
		
		if( $_POST['form_submit'] == 1 ){
			$json['result'] = 1;
			$json['callback'] = 'JCMS.modules.cabinet.result';
			$data = array();
			// экранируем входящие данные
			foreach($_POST as $key=>$val){ if(is_array($val)){ $data[$key]=$val;continue; } $data[$key] = trim($_JCMS->db->escape_string(strval($val))); } 
			
			// проверка входящих данных
			if( empty($data['bl_domain']) ){ $_JCMS->message("Ошибка: поле &laquo;Домен&raquo; не заполнено или заполнено неправильно!", "Проверьте правильность заполнения поля и попробуйте снова.", 'error'); return $json; }

			$sql_code = "SELECT * FROM `jcms2_blackListEmail` WHERE `domain` = '{$data['bl_domain']}'";
			if( !$res = $_JCMS->db->query($sql_code) ){
				$_JCMS->message("Ошибка базы данных! [php/module.cabinet#".__LINE__."]", "MYSQL ERROR #".$_JCMS->db->errno." - ".$_JCMS->db->error, 'error');
				return $json;
			}
			if( $res->num_rows != 0 ){
				$_JCMS->message("Ошибка: указанный &laquo;Домен&raquo; уже есть в базе!", "", 'error'); return $json; 
			}

			if( $data['bl_client'] > 0 ) $client = '\''.$data['bl_client'].'\''; else $client='NULL';
			
			$sql_code = "INSERT INTO `jcms2_blackListEmail`(`domain`, `client_id`) VALUES ('{$data['bl_domain']}', {$client})";
			
			if( !$res = $_JCMS->db->query($sql_code) ){
				$_JCMS->message("Ошибка базы данных! [php/module.cabinet#".__LINE__."]", "MYSQL ERROR #".$_JCMS->db->errno." - ".$_JCMS->db->error, 'error');
				return $json; 
			}
			$id = $_JCMS->db->insert_id;
			$_JCMS->syslog->add('Добавление домена #'.$id.' в черный список доменов электронной почты.',4);
			$json['add'] = 'success';
			$_SESSION['JENSENCMS']['mod_cabinet']['addEmailBlackList_success'] = $id; 
			
			return $json;
		} // end if

		$sql_code = "SELECT * FROM `jcms2_moduleClients` ORDER BY `client_id` ASC, `client_status` DESC";
		if( !$res = $_JCMS->db->query($sql_code) ){
			$_JCMS->message("Ошибка базы данных! [php/module.cabinet#".__LINE__."]", "MYSQL ERROR #".$_JCMS->db->errno." - ".$_JCMS->db->error, 'error');
			return $json;
		}
		$clients_list = '';
		while( $data = $res->fetch_assoc() ){
			if( $_REQUEST['client_id'] == $data['client_id'] ) $sel = 'selected'; else $sel = '';
			$clients_list .= "<option class=\"st{$data[client_status]}\" {$sel} value=\"{$data[client_id]}\">[#{$data['client_id']}] {$data['client_login']} &lt;{$data['client_email']}&gt;</option>";
		}
		
		$json['result'] = 1;
		$json['module_title'] = 'Добавление нового домена / Черный список доменов электронной почты';
		$_JCMS->tpl->load("cabinet/cabinetAddBlackList.tpl");
		$_JCMS->tpl->tag("{CLIENTS_LIST}", $clients_list);
		
		$json['template'] = $_JCMS->tpl->compile();
		$json['template'] = preg_replace("/\{[\w-]+\}/i", "", $json['template']);
		$json['load_module'] = 'cabinet/cabinet';
		$json['callback'] = 'JCMS.modules.cabinet.showPage_addEmailBlackList';
		return $json;
	} 
	
	function showPage_editEmailBlackList(){
		global $_JCMS;
		$id = $_JCMS->db->escape_string(intval($_GET['id']));
		
		if( $_POST['form_submit'] == 1 ){
			$json['result'] = 1;
			$json['callback'] = 'JCMS.modules.cabinet.result';
			$data = array();
			// экранируем входящие данные
			foreach($_POST as $key=>$val){ if(is_array($val)){ $data[$key]=$val;continue; } $data[$key] = trim($_JCMS->db->escape_string(strval($val))); } 
			
			// проверка входящих данных
			if( empty($data['bl_domain']) ){ $_JCMS->message("Ошибка: поле &laquo;Домен&raquo; не заполнено или заполнено неправильно!", "Проверьте правильность заполнения поля и попробуйте снова.", 'error'); return $json; }

			$sql_code = "SELECT * FROM `jcms2_blackListEmail` WHERE `domain` = '{$data['bl_domain']}' AND `id` != '{$id}'";
			if( !$res = $_JCMS->db->query($sql_code) ){
				$_JCMS->message("Ошибка базы данных! [php/module.cabinet#".__LINE__."]", "MYSQL ERROR #".$_JCMS->db->errno." - ".$_JCMS->db->error, 'error');
				return $json;
			}
			if( $res->num_rows != 0 ){
				$_JCMS->message("Ошибка: указанный &laquo;Домен&raquo; уже есть в базе!", "", 'error'); return $json; 
			}

			if( $data['bl_client'] > 0 ) $client = '\''.$data['bl_client'].'\''; else $client='NULL';
		
			$sql_code = "UPDATE `jcms2_blackListEmail` SET `domain`='{$data['bl_domain']}', `client_id`={$client} WHERE `id` = '{$id}' LIMIT 1";

			if( !$res = $_JCMS->db->query($sql_code) ){
				$_JCMS->message("Ошибка базы данных! [php/module.cabinet#".__LINE__."]", "MYSQL ERROR #".$_JCMS->db->errno." - ".$_JCMS->db->error, 'error');
				return $json;
			} 
			$profile = $_JCMS->auth->getProfile();
			$_JCMS->syslog->add('Редактирование домена #'.$id.' в черном списке доменов электронной почты.',4);
			$json['edit'] = 'success';
			$_SESSION['JENSENCMS']['mod_cabinet']['editEmailBlackList_success'] = $id;
			
			return $json;
		} // end if

		$sql_code = "
		SELECT *
		FROM `jcms2_blackListEmail`
		WHERE `id` = '{$id}'
";
		if( !$res = $_JCMS->db->query($sql_code) ){ $_JCMS->message("Ошибка базы данных! [php/module.cabinet#".__LINE__."]", "MYSQL ERROR #".$_JCMS->db->errno." - ".$_JCMS->db->error, 'error'); return $json; }
		if( $res->num_rows !== 1 ){
			$_JCMS->message("Ошибка: запрошенный домен не существует! [php/module.cabinet#".__LINE__."]", "", 'error'); return $json;
		}
		$data = $res->fetch_assoc();
		
		$sql_code = "SELECT * FROM `jcms2_moduleClients` ORDER BY `client_id` ASC, `client_status` DESC";
		if( !$res2 = $_JCMS->db->query($sql_code) ){
			$_JCMS->message("Ошибка базы данных! [php/module.cabinet#".__LINE__."]", "MYSQL ERROR #".$_JCMS->db->errno." - ".$_JCMS->db->error, 'error');
			return $json;
		}
		$clients_list = '';
		while( $data2 = $res2->fetch_assoc() ){
			if( ($_REQUEST['bl_client'] == $data2['client_id'] || $data2['client_id'] == $data['client_id'] ) && $data2['client_id'] > 0 ) $sel = 'selected'; else $sel = '';
			$clients_list .= "<option class=\"st{$data2[client_status]}\" {$sel} value=\"{$data2[client_id]}\">[#{$data2['client_id']}] {$data2['client_login']} &lt;{$data2['client_email']}&gt;</option>";
		}

		$json['result'] = 1;
		$json['id'] = $id;
		$json['module_title'] = 'Редактирование домена #'.$id.' / Черный список доменов электронной почты';
		$_JCMS->tpl->load("cabinet/cabinetAddBlackList.tpl");
		foreach($data as $key=>$val){ $_JCMS->tpl->tag("{".strtoupper($key)."}", $val); }
		$_JCMS->tpl->tag("{ID}", $id);
		$_JCMS->tpl->tag("{CLIENTS_LIST}", $clients_list);
		$_JCMS->tpl->tag("{BL_DOMAIN}", $_POST['bl_domain']?$_POST['bl_domain']:$data['domain']);
		$json['template'] = $_JCMS->tpl->compile();
		$json['template'] = preg_replace("/\{[\w-]+\}/i", "", $json['template']);
		$json['load_module'] = 'cabinet/cabinet';
		$json['callback'] = 'JCMS.modules.cabinet.showPage_editEmailBlackList';
		
		return $json;
	}

	function actionEmailBlacklistDeleteDomain(){
		global $_JCMS;
		$json = array();
		$json['result'] = 1;
		$json['callback'] = 'JCMS.modules.cabinet.result';
		$id = $_JCMS->db->escape_string(intval($_GET['id']));

		$sql_code = "DELETE FROM `jcms2_blackListEmail` WHERE `id` = '{$id}' LIMIT 1";
		if( !$res = $_JCMS->db->query($sql_code) ){
			$_JCMS->message("Ошибка базы данных! [php/module.cabinet#".__LINE__."]", "MYSQL ERROR #".$_JCMS->db->errno." - ".$_JCMS->db->error, 'error');
			return $json;
		} else {
			$profile = $_JCMS->auth->getProfile();
			$_JCMS->syslog->add('Удаление домена #'.$id.' из черного списка доменов электронной почты.',4);
			$json['delete'] = 'success'; 
			$_SESSION['JENSENCMS']['mod_cabinet']['deleteEmailBlackList_success'] = $id;
		}
		
		return $json;
	}
	 
	/* выбирает из базы строки, подходящие запросу юзера(с учетом поиска и сортировки) и формирует ответ в формате, подходящем для JS плагина DataTables */
	function getTable1DataAjax(){
		global $_JCMS;
		$resp = array();
		$resp['draw'] = strval($_POST['draw']);		
		$resp['error'] = NULL;
		// столбы таблицы в порядке следования в интерфейсе
		$columns = array('client_id', 'client_login', 'client_email', 'client_balance', 'client_lastAuthDate', 'client_lastIP', 'client_status', 'client_regDate', 'client_regIP', 'client_lastUserAgent', 'client_referalCode');

		$_POST['search']['value'] = trim(strval($_POST['search']['value']));
		if( !empty($_POST['search']['value']) ){ $search = $_JCMS->db->escape_string($_POST['search']['value']);; } else { $search = false; }
		
		$sql_search = $sql_order = "";
		foreach($columns as $key=>$val){
			if( $search ){
				// SQL фрагмент запроса на поиск
				if( $key != 3 ){ // не выполнять поиск в этих столбцах 
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
			}
			if( is_array($_POST['order']) ){
				foreach($_POST['order'] as $arr){
					if( $arr['column'] == $key ){
						// SQL фрагмент запроса на сортировку
						if( $val == 'client_lastIP' ) $val = '__client_lastIP__';
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
		if( !empty($sql_search) ){ $sql_where = !$sql_where?"WHERE (":''; $sql_where .= $sql_search.')'; unset($sql_search); }
		$sql_limit = "LIMIT ".intval($_POST['start']).",".intval($_POST['length']);

		$sql_code = "  
		SELECT SQL_CALC_FOUND_ROWS *,
			(SELECT SUM(`summ`) FROM `jcms2_moduleClientsBalance` as `b` WHERE `b`.`client_id` = `a`.`client_id`) as `client_balance`,
			inet_aton(`client_lastIP`) as `__client_lastIP__`		
		FROM `jcms2_moduleClients` as a
		{$sql_where}
		{$sql_order} {$sql_limit} 
				";

		if( !$res = $_JCMS->db->query($sql_code) ){ $_JCMS->message("Ошибка базы данных! [php/module.cabinet#".__LINE__."]", "MYSQL ERROR #".$_JCMS->db->errno." - ".$_JCMS->db->error, 'error'); return $json; }
		$table_data = array();
		if( $res->num_rows > 0 ){
			// table data
			while( $data = $res->fetch_assoc() ){
				switch($data['client_status']){
					case 0:$client_status='<span class="susr_st0">не активирован</span>'; break;
					case 1:$client_status='<span class="susr_st1">активирован</span>'; break;
					case 2:$client_status='<span class="susr_st2">заблокирован</span>'; break;
					default: $client_status='???';break;
				} 
				$lastIp = (!empty($data['client_lastIP'])?$data['client_lastIP']:$data['client_regIP']);
				$geoIp = $this->getGeoIp($lastIp);
				$url = 'https://2ip.com.ua/ru/services/information-service/site-location?ip='.$lastIp.'&a=act';
				$lastIp = '<img src="'.$_JCMS->getConfig('site_url').'/include/geo_ip/img/'.$geoIp['img'].'.png" title="'.$geoIp['title'].'"/> <a href="'.$url.'" onclick="window.open(\''.$url.'\');">'.$lastIp.'</a>';
				$table_data[] = array($data['client_id'], $data['client_login'], $data['client_email'], '$'.round($data['client_balance'],2), (strtotime($data['client_lastAuthDate'])>0?date("d.m.y H:i:s",strtotime($data['client_lastAuthDate'])):'&mdash;'), $lastIp, $client_status);
			}
		}
		// всего записей с учетом фильтра, но без LIMIT
		$sql_code = "SELECT FOUND_ROWS() as total_filtered;";
		if( !$res = $_JCMS->db->query($sql_code) ){ $_JCMS->message("Ошибка базы данных! [php/module.cabinet#".__LINE__."]", "MYSQL ERROR #".$_JCMS->db->errno." - ".$_JCMS->db->error, 'error'); return $json;
		} else {
			$data = $res->fetch_assoc();
			$total_filtered = $data['total_filtered'];
		}
		
		// всего записей в таблице
		$sql_code = "SELECT COUNT(*) as total FROM `jcms2_moduleClients`";
		if( !$res = $_JCMS->db->query($sql_code) ){
			$_JCMS->message("Ошибка базы данных! [php/module.cabinet#".__LINE__."]", "MYSQL ERROR #".$_JCMS->db->errno." - ".$_JCMS->db->error, 'error');return $json;
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
		$columns = array('bal_id', 'date', 'summ', 'descr', array('a`.`client_id','a`.`client_email','a`.`client_login'), array('ref_id','b`.`ref_email','b`.`ref_login'));

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
				// для поиска модицифированный sql запрос, алиасы нужно убарть... 
				$sql_search = str_replace(array("`a`.","`b`."), '', $sql_search);
			}
			if( is_array($_POST['order']) ){
				foreach($_POST['order'] as $arr){
					if( $arr['column'] == $key ){
						// SQL фрагмент запроса на сортировку
						if( $val == 'client_lastIP' ) $val = '__client_lastIP__';
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
		if( !empty($sql_search) ){ $sql_where = !$sql_where?"WHERE (":''; $sql_where .= $sql_search.')'; unset($sql_search); }
		$sql_limit = "LIMIT ".intval($_POST['start']).",".intval($_POST['length']);
if( $search ){
	// спец запрос на поиск	
		$sql_code = "  
SELECT SQL_CALC_FOUND_ROWS * FROM (
	SELECT `jcms2_moduleClientsBalance`.*,
		`a`.`client_login`,
		`a`.`client_email`,
		`b`.`client_email` as `ref_email`,
		`b`.`client_login` as `ref_login`    
	FROM `jcms2_moduleClientsBalance`
		LEFT JOIN `jcms2_moduleClients` as `a` ON `a`.`client_id` = `jcms2_moduleClientsBalance`.`client_id`
		LEFT JOIN `jcms2_moduleClients` as `b` ON `b`.`client_id` = `jcms2_moduleClientsBalance`.`ref_id`
		) as a
		{$sql_where}
		{$sql_order} {$sql_limit} 
				";
} else {
		$sql_code = "  
SELECT SQL_CALC_FOUND_ROWS `jcms2_moduleClientsBalance`.*,
	`a`.`client_login`,
    `a`.`client_email`,
    `b`.`client_email` as `ref_email`,
    `b`.`client_login` as `ref_login`    
FROM `jcms2_moduleClientsBalance`
	LEFT JOIN `jcms2_moduleClients` as `a` ON `a`.`client_id` = `jcms2_moduleClientsBalance`.`client_id`
	LEFT JOIN `jcms2_moduleClients` as `b` ON `b`.`client_id` = `jcms2_moduleClientsBalance`.`ref_id`
		{$sql_where}
		{$sql_order} {$sql_limit} 
				";
	}
	if( !$res = $_JCMS->db->query($sql_code) ){ $_JCMS->message("Ошибка базы данных! [php/module.cabinet#".__LINE__."]", "MYSQL ERROR #".$_JCMS->db->errno." - ".$_JCMS->db->error, 'error'); return $json; }
		$table_data = array();
		if( $res->num_rows > 0 ){
			// table data
			while( $data = $res->fetch_assoc() ){
				$summ = round(floatval($data['summ']),2);
				if( $summ < 0 ){ $summ = '<span class="susr_st2">&ndash; $'.abs(round(floatval($data['summ']),2)).'</span>'; } else { $summ = '<span class="susr_st1">+ $'.abs(round(floatval($data['summ']),2)).'</span>'; }
				$url = $_JCMS->getConfig('site_url').'/admin/module/cabinet/edit_client?id='.$data['client_id'];
				$user = '<a href="'.$url.'" onclick="window.open(\''.$url.'\'); return false;">[#'.$data['client_id'].']&nbsp;'.$data['client_login'].'</a>';
				
				if( $data['ref_id'] > 0 ){
					$url = $_JCMS->getConfig('site_url').'/admin/module/cabinet/edit_client?id='.$data['ref_id'];
					$data['descr'] .= ' <i>(связано&nbsp;с&nbsp;пользователем&nbsp;<a href="'.$url.'" onclick="window.open(\''.$url.'\'); return false;">[#'.$data['ref_id'].']&nbsp;'.$data['ref_login'].'</a>)</i>';
				}

				if( $data['order_id'] > 0 ){
					$url = $_JCMS->getConfig('site_url').'/admin/module/cabinet/edit_order?id='.$data['order_id'];
					$data['descr'] .= ' <i>(связано&nbsp;с&nbsp;заказом&nbsp;<a href="'.$url.'" onclick="window.open(\''.$url.'\'); return false;">№'.$data['order_id'].'</a>)</i>';
				}
				
				$table_data[] = array($data['bal_id'], date("d.m.y H:i:s",strtotime($data['date'])), $summ, $data['descr'], $user);
			}
		}
		// всего записей с учетом фильтра, но без LIMIT
		$sql_code = "SELECT FOUND_ROWS() as total_filtered;";
		if( !$res = $_JCMS->db->query($sql_code) ){ $_JCMS->message("Ошибка базы данных! [php/module.cabinet#".__LINE__."]", "MYSQL ERROR #".$_JCMS->db->errno." - ".$_JCMS->db->error, 'error'); return $json;
		} else {
			$data = $res->fetch_assoc();
			$total_filtered = $data['total_filtered'];
		}
		
		// всего записей в таблице
		$sql_code = "SELECT COUNT(*) as total FROM `jcms2_moduleClientsBalance`";
		if( !$res = $_JCMS->db->query($sql_code) ){
			$_JCMS->message("Ошибка базы данных! [php/module.cabinet#".__LINE__."]", "MYSQL ERROR #".$_JCMS->db->errno." - ".$_JCMS->db->error, 'error');return $json;
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
	function getTable3DataAjax(){
		global $_JCMS;
		$resp = array();
		$resp['draw'] = strval($_POST['draw']);		
		$resp['error'] = NULL;
		// столбы таблицы в порядке следования в интерфейсе
		$columns = array('order_id', 'c`.`client_id', 'b`.`tariff_id', 'order_addDate', 'order_paidBefore', 'order_summ', 'order_status', 'b`.`tariff_title','c`.`client_login', 'order_data');

		$_POST['search']['value'] = trim(strval($_POST['search']['value']));
		if( !empty($_POST['search']['value']) ){ $search = $_JCMS->db->escape_string($_POST['search']['value']);; } else { $search = false; }
		
		$sql_search = $sql_order = "";
		foreach($columns as $key=>$val){
			if( $search ){
				// SQL фрагмент запроса на поиск
				if( $key != 5 ){ // не выполнять поиск в этих столбцах 
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
			}
			if( is_array($_POST['order']) ){
				foreach($_POST['order'] as $arr){
					if( $arr['column'] == $key ){
						// SQL фрагмент запроса на сортировку
						if( $val == 'client_lastIP' ) $val = '__client_lastIP__';
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
		if( !empty($sql_search) ){ $sql_where = !$sql_where?" AND (":''; $sql_where .= $sql_search.')'; unset($sql_search); }
		$sql_limit = "LIMIT ".intval($_POST['start']).",".intval($_POST['length']);

		$sql_code = "  
		SELECT SQL_CALC_FOUND_ROWS `a`.*,
			(SELECT SUM(`summ`) FROM `jcms2_moduleClientsBalance` as `d` WHERE `d`.`order_id` = `a`.`order_id` AND `d`.`summ` < 0) as `order_summ`,
			`b`.`tariff_id`,
			`b`.`tariff_title`,
			`c`.`client_login`,
			`c`.`client_email`
		FROM `jcms2_moduleClientsOrders` as `a`
			LEFT JOIN `jcms2_moduleClientsTariffs` as `b` ON `b`.`tariff_id` = `a`.`tariff_id`
			LEFT JOIN `jcms2_moduleClients` as `c` ON `c`.`client_id` = `a`.`client_id`
		WHERE `order_isTest` = '0'
		{$sql_where}
		{$sql_order} {$sql_limit} 
				";
$resp['sql'] = str_replace(array("\r", "\n", "\t"), "", $sql_code);
		if( !$res = $_JCMS->db->query($sql_code) ){ $_JCMS->message("Ошибка базы данных! [php/module.cabinet#".__LINE__."]", "MYSQL ERROR #".$_JCMS->db->errno." - ".$_JCMS->db->error, 'error'); return $json; }
		$table_data = array();
		if( $res->num_rows > 0 ){
			// table data
			while( $data = $res->fetch_assoc() ){
				$data['order_status'] = $data['order_isLock']==1?-1:$data['order_status'];
				switch($data['order_status']){
					case -1:$order_status='<span class="susr_st0">'.$this->order_status[$data['order_status']].'</span>'; break;
					case 0:$order_status='<span class="susr_st0">'.$this->order_status[$data['order_status']].'</span>'; break;
					case 1:$order_status='<span class="susr_st1">'.$this->order_status[$data['order_status']].'</span>'; break;
					case 2:$order_status=$this->order_status[$data['order_status']]; break;
					case 3:$order_status='<span class="susr_st2">'.$this->order_status[$data['order_status']].'</span>'; break;
					default: $order_status='???';break;
				} 

				$order_summ = abs(round(floatval($data['order_summ']),2));
				
				$url = $_JCMS->getConfig('site_url').'/admin/module/cabinet/edit_client?id='.$data['client_id'];
				$client = '<a href="'.$url.'" onclick="window.open(\''.$url.'\'); return false;">[#'.$data['client_id'].']&nbsp;'.$data['client_login'].'</a>';

				$url = $_JCMS->getConfig('site_url').'/admin/module/cabinet/edit_tariff?id='.$data['tariff_id'];
				$tariff = '<a href="'.$url.'" onclick="window.open(\''.$url.'\'); return false;">[#'.$data['tariff_id'].']&nbsp;'.$data['tariff_title'].'</a>';

				$table_data[] = array($data['order_id'], $client, $tariff, date("d.m.y H:i:s",strtotime($data['order_addDate'])), date("d.m.y H:i:s",strtotime($data['order_paidBefore'])), '$'.$order_summ, $order_status); 
			}
		}
		// всего записей с учетом фильтра, но без LIMIT
		$sql_code = "SELECT FOUND_ROWS() as total_filtered;";
		if( !$res = $_JCMS->db->query($sql_code) ){ $_JCMS->message("Ошибка базы данных! [php/module.cabinet#".__LINE__."]", "MYSQL ERROR #".$_JCMS->db->errno." - ".$_JCMS->db->error, 'error'); return $json;
		} else {
			$data = $res->fetch_assoc();
			$total_filtered = $data['total_filtered'];
		}
		
		// всего записей в таблице
		$sql_code = "SELECT COUNT(*) as total FROM `jcms2_moduleClientsOrders` WHERE `order_isTest` = '0'";
		if( !$res = $_JCMS->db->query($sql_code) ){
			$_JCMS->message("Ошибка базы данных! [php/module.cabinet#".__LINE__."]", "MYSQL ERROR #".$_JCMS->db->errno." - ".$_JCMS->db->error, 'error');return $json;
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
	function getTable4DataAjax(){
		global $_JCMS;
		$resp = array();
		$resp['draw'] = strval($_POST['draw']);		
		$resp['error'] = NULL;
		// столбы таблицы в порядке следования в интерфейсе
		$columns = array('tariff_id', 'tariff_title', 'tariff_descr', 'tariff_status');

		$_POST['search']['value'] = trim(strval($_POST['search']['value']));
		if( !empty($_POST['search']['value']) ){ $search = $_JCMS->db->escape_string($_POST['search']['value']); } else { $search = false; }
		
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
						if( $val == 'client_lastIP' ) $val = '__client_lastIP__';
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
		if( !empty($sql_search) ){ $sql_where = !$sql_where?"WHERE (":''; $sql_where .= $sql_search.')'; unset($sql_search); }
		$sql_limit = "LIMIT ".intval($_POST['start']).",".intval($_POST['length']);

		$sql_code = "  
		SELECT SQL_CALC_FOUND_ROWS `jcms2_moduleClientsTariffs`.*
		
		FROM `jcms2_moduleClientsTariffs`
		
		{$sql_where}
		{$sql_order} {$sql_limit} 
				";

		if( !$res = $_JCMS->db->query($sql_code) ){ $_JCMS->message("Ошибка базы данных! [php/module.cabinet#".__LINE__."]", "MYSQL ERROR #".$_JCMS->db->errno." - ".$_JCMS->db->error, 'error'); return $json; }
		$table_data = array();
		if( $res->num_rows > 0 ){
			// table data
			while( $data = $res->fetch_assoc() ){
				switch($data['tariff_status']){
					case 0:$tariff_status='<span class="susr_st2">'.$this->tariff_status[$data['tariff_status']].'</span>'; break;
					case 1:$tariff_status='<span class="susr_st1">'.$this->tariff_status[$data['tariff_status']].'</span>'; break;
					default: $tariff_status='???';break;
				} 

				$order_summ = abs(round(floatval($data['order_summ']),2));
				
				$url = $_JCMS->getConfig('site_url').'/admin/module/cabinet/edit_client?id='.$data['client_id'];
				$client = '<a href="'.$url.'" onclick="window.open(\''.$url.'\'); return false;">[#'.$data['client_id'].']&nbsp;'.$data['client_login'].'</a>';

				$url = $_JCMS->getConfig('site_url').'/admin/module/cabinet/edit_tariff?id='.$data['tariff_id'];
				$tariff = '<a href="'.$url.'" onclick="window.open(\''.$url.'\'); return false;">[#'.$data['tariff_id'].']&nbsp;'.$data['tariff_title'].'</a>';

				$table_data[] = array($data['tariff_id'], $data['tariff_title'], $data['tariff_descr'], $tariff_status); 
			}
		}
		// всего записей с учетом фильтра, но без LIMIT
		$sql_code = "SELECT FOUND_ROWS() as total_filtered;";
		if( !$res = $_JCMS->db->query($sql_code) ){ $_JCMS->message("Ошибка базы данных! [php/module.cabinet#".__LINE__."]", "MYSQL ERROR #".$_JCMS->db->errno." - ".$_JCMS->db->error, 'error'); return $json;
		} else {
			$data = $res->fetch_assoc();
			$total_filtered = $data['total_filtered'];
		}
		
		// всего записей в таблице
		$sql_code = "SELECT COUNT(*) as total FROM `jcms2_moduleClientsTariffs`";
		if( !$res = $_JCMS->db->query($sql_code) ){
			$_JCMS->message("Ошибка базы данных! [php/module.cabinet#".__LINE__."]", "MYSQL ERROR #".$_JCMS->db->errno." - ".$_JCMS->db->error, 'error');return $json;
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
	function getTable5DataAjax(){
		global $_JCMS;
		$resp = array();
		$resp['draw'] = strval($_POST['draw']);		
		$resp['error'] = NULL;
		// столбы таблицы в порядке следования в интерфейсе
		$columns = array('client_id', 'client_login', 'client_partn_oborot', 'client_ref_rate', '__client_regIP__', '__client_lastIP__', 'client_lastAuthDate', 'client_totalReferals');

		$_POST['search']['value'] = trim(strval($_POST['search']['value']));
		if( !empty($_POST['search']['value']) ){ $search = $_JCMS->db->escape_string($_POST['search']['value']);; } else { $search = false; }
		
		$sql_search = $sql_order = "";
		foreach($columns as $key=>$val){
			if( $search ){
				// SQL фрагмент запроса на поиск
				if( in_array($key, array(2,3,7)) ){ // не выполнять поиск в этих столбцах 
					if( $key == 4 ) $val = 'client_regIP';
					if( $key == 5 ) $val = 'client_lastIP';
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
		if( !empty($sql_search) ){ $sql_where = !$sql_where?"WHERE (":''; $sql_where .= $sql_search.')'; unset($sql_search); }
		$sql_limit = "LIMIT ".intval($_POST['start']).",".intval($_POST['length']);

		$sql_code = "  
		SELECT SQL_CALC_FOUND_ROWS *,
			(SELECT SUM(`summ`) FROM `jcms2_moduleClientsBalance` as `b` WHERE `b`.`client_id` = `a`.`client_id`) as `client_balance`,
			(SELECT SUM(`summ`) FROM `jcms2_moduleClientsBalance` as `c` WHERE `c`.`client_id` = `a`.`client_id` AND `c`.`summ` > 0 AND `c`.`ref_id` IS NOT NULL) as `client_partn_oborot`,
			(SELECT COUNT(`client_id`) FROM `jcms2_moduleClients` as `d` WHERE `d`.`client_ref_id` = `a`.`client_id` AND `d`.`client_ref_id` IS NOT NULL) as `client_totalReferals`,
			inet_aton(`client_regIP`) as `__client_regIP__`,		
			inet_aton(`client_lastIP`) as `__client_lastIP__`		
		FROM `jcms2_moduleClients` as a
		{$sql_where}
		{$sql_order} {$sql_limit} 
				";

		if( !$res = $_JCMS->db->query($sql_code) ){ $_JCMS->message("Ошибка базы данных! [php/module.cabinet#".__LINE__."]", "MYSQL ERROR #".$_JCMS->db->errno." - ".$_JCMS->db->error, 'error'); return $json; }
		$table_data = array();
		if( $res->num_rows > 0 ){
			// table data
			while( $data = $res->fetch_assoc() ){
				switch($data['client_status']){
					case 0:$client_status='<span class="susr_st0">не активирован</span>'; break;
					case 1:$client_status='<span class="susr_st1">активирован</span>'; break;
					case 2:$client_status='<span class="susr_st2">заблокирован</span>'; break;
					default: $client_status='???';break;
				} 
				$lastIp = (!empty($data['client_lastIP'])?$data['client_lastIP']:$data['client_regIP']);
				$geoIp = $this->getGeoIp($lastIp);
				$url = 'https://2ip.com.ua/ru/services/information-service/site-location?ip='.$lastIp.'&a=act';
				$lastIp = '<img src="'.$_JCMS->getConfig('site_url').'/include/geo_ip/img/'.$geoIp['img'].'.png" title="'.$geoIp['title'].'"/> <a href="'.$url.'" onclick="window.open(\''.$url.'\');">'.$lastIp.'</a>';
				$regIp = $data['client_regIP'];
				$geoIp = $this->getGeoIp($regIp);
				$url = 'https://2ip.com.ua/ru/services/information-service/site-location?ip='.$regIp.'&a=act';
				$regIp = '<img src="'.$_JCMS->getConfig('site_url').'/include/geo_ip/img/'.$geoIp['img'].'.png" title="'.$geoIp['title'].'"/> <a href="'.$url.'" onclick="window.open(\''.$url.'\');">'.$regIp.'</a>';
				$table_data[] = array($data['client_id'], $data['client_login'], '$'.round(floatval($data['client_partn_oborot']),2), ($data['client_ref_rate']!=NULL?round(floatval($data['client_ref_rate']),2).'%':"&mdash;"), $regIp, $lastIp, (strtotime($data['client_lastAuthDate'])>0?date("d.m.y H:i:s",strtotime($data['client_lastAuthDate'])):'&mdash;'), intval($data['client_totalReferals']));
			}
		}
		// всего записей с учетом фильтра, но без LIMIT
		$sql_code = "SELECT FOUND_ROWS() as total_filtered;";
		if( !$res = $_JCMS->db->query($sql_code) ){ $_JCMS->message("Ошибка базы данных! [php/module.cabinet#".__LINE__."]", "MYSQL ERROR #".$_JCMS->db->errno." - ".$_JCMS->db->error, 'error'); return $json;
		} else {
			$data = $res->fetch_assoc();
			$total_filtered = $data['total_filtered'];
		}
		
		// всего записей в таблице
		$sql_code = "SELECT COUNT(*) as total FROM `jcms2_moduleClients`";
		if( !$res = $_JCMS->db->query($sql_code) ){
			$_JCMS->message("Ошибка базы данных! [php/module.cabinet#".__LINE__."]", "MYSQL ERROR #".$_JCMS->db->errno." - ".$_JCMS->db->error, 'error');return $json;
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
	function getTable6DataAjax(){
		global $_JCMS;
		$resp = array();
		$resp['draw'] = strval($_POST['draw']);		
		$resp['error'] = NULL;
		// столбы таблицы в порядке следования в интерфейсе
		$columns = array('ticket_id', 'b`.`client_id', 'ticket_title', 'ticket_addDate', 'ticket_status', 'b`.`client_login', 'b`.`client_email');

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
		if( !empty($sql_search) ){ $sql_where = !$sql_where?"WHERE (":''; $sql_where .= $sql_search.')'; unset($sql_search); }
		$sql_limit = "LIMIT ".intval($_POST['start']).",".intval($_POST['length']);

		$sql_code = "  
		SELECT SQL_CALC_FOUND_ROWS `a`.*,
			`b`.`client_login`,
			`b`.`client_email`
		FROM `jcms2_moduleClientsTickets` as `a`
			LEFT JOIN `jcms2_moduleClients` as `b` ON `b`.`client_id` = `a`.`client_id`
		{$sql_where}
		{$sql_order} {$sql_limit} 
				";

		if( !$res = $_JCMS->db->query($sql_code) ){ $_JCMS->message("Ошибка базы данных! [php/module.cabinet#".__LINE__."]", "MYSQL ERROR #".$_JCMS->db->errno." - ".$_JCMS->db->error, 'error'); return $json; }
		$table_data = array();
		if( $res->num_rows > 0 ){
			// table data
			while( $data = $res->fetch_assoc() ){
				switch($data['ticket_status']){
					case 0:$ticket_status='<span class="susr_st0">'.$this->ticket_status[$data['ticket_status']].'</span>'; break;
					case 1:$ticket_status='<span class="susr_st1">'.$this->ticket_status[$data['ticket_status']].'</span>'; break;
					case 2:$ticket_status=$this->ticket_status[$data['ticket_status']]; break;
					case 3:$ticket_status='<span class="susr_st2">'.$this->ticket_status[$data['ticket_status']].'</span>'; break;
					default: $ticket_status='???';break;
				} 

				$table_data[] = array($data['ticket_id'], $data['client_login'], $data['ticket_title'], date("d.m.y H:i:s",strtotime($data['ticket_addDate'])), $ticket_status);
			}
		}
		// всего записей с учетом фильтра, но без LIMIT
		$sql_code = "SELECT FOUND_ROWS() as total_filtered;";
		if( !$res = $_JCMS->db->query($sql_code) ){ $_JCMS->message("Ошибка базы данных! [php/module.cabinet#".__LINE__."]", "MYSQL ERROR #".$_JCMS->db->errno." - ".$_JCMS->db->error, 'error'); return $json;
		} else {
			$data = $res->fetch_assoc();
			$total_filtered = $data['total_filtered'];
		}
		
		// всего записей в таблице
		$sql_code = "SELECT COUNT(*) as total FROM `jcms2_moduleClientsTickets`";
		if( !$res = $_JCMS->db->query($sql_code) ){
			$_JCMS->message("Ошибка базы данных! [php/module.cabinet#".__LINE__."]", "MYSQL ERROR #".$_JCMS->db->errno." - ".$_JCMS->db->error, 'error');return $json;
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
	function getTable7DataAjax(){
		global $_JCMS;
		$resp = array();
		$resp['draw'] = strval($_POST['draw']);		
		$resp['error'] = NULL;
		// столбы таблицы в порядке следования в интерфейсе
		$columns = array('order_id', 'c`.`client_id', 'order_addDate', 'order_paidBefore', 'order_isTestFake', 'order_status', 'c`.`client_login', 'order_data');

		$_POST['search']['value'] = trim(strval($_POST['search']['value']));
		if( !empty($_POST['search']['value']) ){ $search = $_JCMS->db->escape_string($_POST['search']['value']);; } else { $search = false; }
		
		$sql_search = $sql_order = "";
		foreach($columns as $key=>$val){
			if( $search ){
				// SQL фрагмент запроса на поиск
				if( $key != 5 ){ // не выполнять поиск в этих столбцах 
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
		if( !empty($sql_search) ){ $sql_where = !$sql_where?" AND (":''; $sql_where .= $sql_search.')'; unset($sql_search); }
		$sql_limit = "LIMIT ".intval($_POST['start']).",".intval($_POST['length']);

		$sql_code = "  
		SELECT SQL_CALC_FOUND_ROWS `a`.*,
			`c`.`client_login`,
			`c`.`client_email`
		FROM `jcms2_moduleClientsOrders` as `a`
			LEFT JOIN `jcms2_moduleClients` as `c` ON `c`.`client_id` = `a`.`client_id`
		WHERE `order_isTest` = '1'
		{$sql_where}
		{$sql_order} {$sql_limit} 
				";

		if( !$res = $_JCMS->db->query($sql_code) ){ $_JCMS->message("Ошибка базы данных! [php/module.cabinet#".__LINE__."]", "MYSQL ERROR #".$_JCMS->db->errno." - ".$_JCMS->db->error, 'error'); return $json; }
		$table_data = array();
		if( $res->num_rows > 0 ){
			// table data
			while( $data = $res->fetch_assoc() ){
				$data['order_status'] = $data['order_isLock']==1?-1:$data['order_status'];
				switch($data['order_status']){
					case -1:$order_status='<span class="susr_st0">'.$this->order_status[$data['order_status']].'</span>'; break;
					case 0:$order_status='<span class="susr_st0">'.$this->order_status[$data['order_status']].'</span>'; break;
					case 1:$order_status='<span class="susr_st1">'.$this->order_status[$data['order_status']].'</span>'; break;
					case 2:$order_status=$this->order_status[$data['order_status']]; break;
					case 3:$order_status='<span class="susr_st2">'.$this->order_status[$data['order_status']].'</span>'; break;
					default: $order_status='???';break;
				} 

				switch($data['order_isTestFake']){
					case 0:$order_isTestFake='<span class="susr_st1">НЕТ</span>'; break;
					case 1:$order_isTestFake='<span class="susr_st2">ДА</span>'; break;
					default: $order_isTestFake='???';break;
				} 

				$order_summ = abs(round(floatval($data['order_summ']),2));
				
				$url = $_JCMS->getConfig('site_url').'/admin/module/cabinet/edit_client?id='.$data['client_id'];
				$client = '<a href="'.$url.'" onclick="window.open(\''.$url.'\'); return false;">[#'.$data['client_id'].']&nbsp;'.$data['client_login'].'</a>';

				$table_data[] = array($data['order_id'], $client, date("d.m.y H:i:s",strtotime($data['order_addDate'])), date("d.m.y H:i:s",strtotime($data['order_paidBefore'])), $order_isTestFake, $order_status); 
			}
		}
		// всего записей с учетом фильтра, но без LIMIT
		$sql_code = "SELECT FOUND_ROWS() as total_filtered;";
		if( !$res = $_JCMS->db->query($sql_code) ){ $_JCMS->message("Ошибка базы данных! [php/module.cabinet#".__LINE__."]", "MYSQL ERROR #".$_JCMS->db->errno." - ".$_JCMS->db->error, 'error'); return $json;
		} else {
			$data = $res->fetch_assoc();
			$total_filtered = $data['total_filtered'];
		}
		
		// всего записей в таблице
		$sql_code = "SELECT COUNT(*) as total FROM `jcms2_moduleClientsOrders` WHERE `order_isTest` = '1'";
		if( !$res = $_JCMS->db->query($sql_code) ){
			$_JCMS->message("Ошибка базы данных! [php/module.cabinet#".__LINE__."]", "MYSQL ERROR #".$_JCMS->db->errno." - ".$_JCMS->db->error, 'error');return $json;
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
	function getTable8DataAjax(){
		global $_JCMS;
		$resp = array();
		$resp['draw'] = strval($_POST['draw']);		
		$resp['error'] = NULL;
		// столбы таблицы в порядке следования в интерфейсе
		$columns = array('id', 'domain', 'addDate', 'b`.`client_login', 'b`.`client_email');

		$_POST['search']['value'] = trim(strval($_POST['search']['value']));
		if( !empty($_POST['search']['value']) ){ $search = $_JCMS->db->escape_string($_POST['search']['value']);; } else { $search = false; }
		
		$sql_search = $sql_order = "";
		foreach($columns as $key=>$val){
			if( $search ){
				// SQL фрагмент запроса на поиск
				if( $key != 5 ){ // не выполнять поиск в этих столбцах 
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
		if( !empty($sql_search) ){ $sql_where = !$sql_where?" WHERE ":''; $sql_where .= $sql_search.''; unset($sql_search); }
		$sql_limit = "LIMIT ".intval($_POST['start']).",".intval($_POST['length']);

		$sql_code = "  
		SELECT SQL_CALC_FOUND_ROWS `a`.*,
			`b`.`client_login`,
			`b`.`client_email`
		FROM `jcms2_blackListEmail` as `a`
			LEFT JOIN `jcms2_moduleClients` as `b` ON `b`.`client_id` = `a`.`client_id`
		{$sql_where}
		{$sql_order} {$sql_limit} 
				";

		if( !$res = $_JCMS->db->query($sql_code) ){ $_JCMS->message("Ошибка базы данных! [php/module.cabinet#".__LINE__."]", "MYSQL ERROR #".$_JCMS->db->errno." - ".$_JCMS->db->error, 'error'); return $json; }
		$table_data = array();
		if( $res->num_rows > 0 ){
			// table data
			while( $data = $res->fetch_assoc() ){

				$url = $_JCMS->getConfig('site_url').'/admin/module/cabinet/edit_client?id='.$data['client_id'];
				
				if( $data['client_id'] < 1 ) $client = '&mdash;'; else $client = '<a href="'.$url.'" onclick="window.open(\''.$url.'\'); return false;">[#'.$data['client_id'].']&nbsp;'.$data['client_login'].'</a>';

				$table_data[] = array($data['id'], $data['domain'], date("d.m.y H:i:s",strtotime($data['addDate'])), $client); 
			}
		}
		// всего записей с учетом фильтра, но без LIMIT
		$sql_code = "SELECT FOUND_ROWS() as total_filtered;";
		if( !$res = $_JCMS->db->query($sql_code) ){ $_JCMS->message("Ошибка базы данных! [php/module.cabinet#".__LINE__."]", "MYSQL ERROR #".$_JCMS->db->errno." - ".$_JCMS->db->error, 'error'); return $json;
		} else {
			$data = $res->fetch_assoc();
			$total_filtered = $data['total_filtered'];
		}
		
		// всего записей в таблице
		$sql_code = "SELECT COUNT(*) as total FROM `jcms2_blackListEmail`";
		if( !$res = $_JCMS->db->query($sql_code) ){
			$_JCMS->message("Ошибка базы данных! [php/module.cabinet#".__LINE__."]", "MYSQL ERROR #".$_JCMS->db->errno." - ".$_JCMS->db->error, 'error');return $json;
		} else {
			$data = $res->fetch_assoc();
			$total = $data['total'];
		}
		
		$resp['recordsTotal'] = $total;
		$resp['recordsFiltered'] = $total_filtered;		
		$resp['data'] = $table_data; unset($table_data);
		
		return $resp;
	}
	
	function getGeoIp($ip){ 
		if( !filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE|FILTER_FLAG_NO_RES_RANGE|FILTER_FLAG_IPV4) ) return array('title'=>'unknown', 'img'=>'unknown', 'code'=>'unknown');
		include_once("../include/geo_ip/geo_ip.php");
		$geoip = geo_ip::getInstance("../include/geo_ip/geo_ip.dat");
		$name = $geoip->lookupCountryName($ip);
		$img = str_replace(" ", "_", strtolower($name));
		
		return array('title'=>$name, 'img'=>$img, 'code'=>$geoip->lookupCountryCode($ip));
	}
	
	/* получает страницу конфигурации модуля */
	function getConfigPage(){
		global $_JCMS;
		
		$_JCMS->tpl->load('cabinet/cabinet.config.tpl');
		
		$conf = $_JCMS->getConfig('referals');
		 
		if( $conf['mode'] == 0 ){ $sel1=' selected'; } else
		if( $conf['mode'] == 1 ){ $sel2=' selected'; }
		
		$modes = "<option value=\"0\"{$sel1}>Единоразово</option>";
		$modes .= "<option value=\"1\"{$sel2}>Постоянно</option>";
		
		if( is_array($conf['rates']) ){
			ksort($conf['rates']);
			foreach($conf['rates'] as $summ=>$rate){
				$rates .= "referalsRatesBlockAdd('{$summ}','{$rate}');";
			}
		}


		$_JCMS->tpl->tag("{REFERALS_MODE}", $modes);


		$modes = '<option value="1"'.($_JCMS->getConfig('wm_mode')=='1'?' selected':'').'>Показать</option><option value="0"'.($_JCMS->getConfig('wm_mode')!='1'?' selected':'').'>Скрыть</option>';
		$_JCMS->tpl->tag("{WM_MODES}", $modes);		

		$_JCMS->tpl->tag("{REFERALS_RATES}", $rates);
		
		return $_JCMS->tpl->compile();
	}
	
	/* проверка конфигурации модуля(если нужно), перед сохранением */
	function checkConfig(&$errors){
		// $_POST['key'] = 'val'; // элемент конфигурации
		// $errors[] = 'текст ошибки'; // вернуть ошибку (конфигурация не будет сохранена, пока есть хотя бы одна ошибка)
		$_POST['referals']['mode'] = intval($_POST['referals']['mode']);
		if( !in_array($_POST['referals']['mode'], array('0','1')) ) $_POST['referals']['mode'] = 0;
		if( !is_array($_POST['referals']['rates']) ) $_POST['referals']['rates'] = array(0=>0); // значение по умолчанию 0%
		if( !empty($_POST['referals']['rates']) ){
			$tmp = $_POST['referals']['rates'];
			$_POST['referals']['rates'] = array();
			// валидация ставок
			foreach($tmp as $arr){
				if( !is_array($arr) || isset($_POST['referals']['rates'][$arr[0]]) || floatval($arr[0]) < 0 || floatval($arr[1]) < 0 ) continue; // удаляем дубли и пустые
				$_POST['referals']['rates'][$arr[0]] = strval($arr[1]);
			}
			ksort($_POST['referals']['rates']); // сортировка по возрастанию
		}
		if( empty($_POST['referals']['rates']) ) $_POST['referals']['rates'] = array(0=>0); // значение по умолчанию 0%
	}
}

// END.
?>