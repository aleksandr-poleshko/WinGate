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
	var $shortLinks;
	var $conf;
	var $onpage = 900;
	var $ondate = NULL;
	var $tariff_periods = array(
		'ru'=> array(
			'1d' => array('title' => '1 день',	'interval' => '1 DAY'), 
			'7d' => array('title' => '7 дней',	'interval' => '7 DAY'),
			'1m' => array('title' => '1 месяц',	'interval' => '1 MONTH'),
			'3m' => array('title' => '3 месяца', 'interval'=> '3 MONTH'),
			'6m' => array('title' => '6 месяцев','interval'=> '6 MONTH'),
			'1y' => array('title' => '1 год', 	'interval' => '1 YEAR'),
		),
		'en'=> array(
			'1d' => array('title' => '1 day',	'interval' => '1 DAY'), 
			'7d' => array('title' => '7 days',	'interval' => '7 DAY'),
			'1m' => array('title' => '1 month',	'interval' => '1 MONTH'),
			'3m' => array('title' => '3 months', 'interval'=> '3 MONTH'),
			'6m' => array('title' => '6 months','interval'=> '6 MONTH'),
			'1y' => array('title' => '1 year', 	'interval' => '1 YEAR'),
		)			
	);

	var $ticket_status = array(
		'ru'=> array(
			'0' => "ожидает ответа",
			'1' => "отвечен",
			'2' => "закрыт",
			'3' => "заблокирован",
		),
		'en'=> array(
			'0' => "waiting for an answer",
			'1' => "answered",
			'2' => "closed",
			'3' => "blocked",
		)
	);
	
	/* воркер, обрабатывающий запрос к этому модулю */
	##
	## ОБРАБОТЧИКИ СТРАНИЦ ЛИЧНОГО КАБИНЕТА
	##
	function work(){
		global $_JCMS, $_META;
		$json = array();

		if( intval($_COOKIE['jcms2_uid']) < 1 ){
			$client_id = $profile['client_id']; 
			setcookie('jcms2_uid', $client_id); // сохраняем в куках ид юзера
		}
		
		if( $_GET['ref'] ){ 
			header("Location: ".$_JCMS->getConfig('site_url'));exit();
		}
		## ============== ##
		## ЛИЧНЫЙ КАБИНЕТ ##
		## ============== ##
		ob_clean();
		header("HTTP/1.1 200");
	
//		api.php?action=getFile&order={$data['order_id']}&key=".md5($data['order_id'].$data['order_addDate'])."\">Скачать</a>";
		if( $_JCMS->query[0] == 'api.php' && !$_JCMS->query[1] ){
			$order_id = $_JCMS->db->escape_string(intval($_GET['order']));
			$key = $_JCMS->db->escape_string(strval($_GET['key']));

			$sql_code = "SELECT * FROM `jcms2_moduleClientsOrders` WHERE `order_status` IN ('0','1') AND `order_id` = '{$order_id}' AND `order_paidBefore` > CURRENT_TIMESTAMP";
			if( !$res = $_JCMS->db->query($sql_code)){
				$_JCMS->message("Ошибка базы данных! [php/module.cabinet#".__LINE__."]", $_JCMS->show_php_errors?("MYSQL ERROR #".$_JCMS->db->errno." - ".$_JCMS->db->error):'', 'error');
			} elseif( $res->num_rows == 1 ){
				$data = $res->fetch_assoc();
				$valid_key = md5($data['order_id'].$data['order_addDate']);

				if( strtoupper($key) === strtoupper($valid_key) ){
					// хэш верный
					$file = ROOT_PATH.'/secure/list.txt';
					ob_end_clean();
					// заставляем браузер показать окно сохранения файла
					header("HTTP/1.1 200");
#					header('Content-Description: File Transfer');
#					header('Content-Type: application/octet-stream');
					header('Content-Type: text/plain');
#					header('Content-Disposition: attachment; filename='.$order_id.'.txt');
#					header('Content-Transfer-Encoding: binary');
#					header('Expires: 0');
#					header('Cache-Control: must-revalidate');
#					header('Pragma: public');
#					header('Content-Length: ' . filesize($file));
			
					readfile($file);
					exit();
				}
			}
			ob_end_clean();
			header("HTTP/1.1 403");
			$_SERVER['REDIRECT_STATUS'] = 403; include(ROOT_PATH.'/error.php');
			exit();
		}

		if( $_JCMS->query[0] == 'balance' && $_JCMS->query[1] == 'payment_freekassa' && !$_JCMS->query[2] ){
			 ob_end_clean(); $this->action_payment(); exit(); 
		} // оплата процессинг
		
		if( $_JCMS->query[0] == 'balance' && $_JCMS->query[1] == 'payment_cryptocloud' && !$_JCMS->query[2] ){
			 ob_end_clean(); $this->action_payment_cryptocloud(); exit(); 
		} // оплата процессинг

        if( $_JCMS->query[0] == 'balance' && $_JCMS->query[1] == 'payment_cryptocloud_webhook' && !$_JCMS->query[2] ){
            ob_end_clean(); $this->action_payment_cryptocloud_webhook(); exit();
        } // оплата процессинг

		if( $_JCMS->query[0] == 'balance' && $_JCMS->query[1] == 'payment_fail' && !$_JCMS->query[2] )
		{
			if( $_JCMS->lang == 'en' ){
				$_SESSION["JENSENCMS"]['client_msg'] = array("<b>Error occurred during the payment of order!</b>", 'Try to make a payment again, or our contact technical support.','error');
			} else {
				$_SESSION["JENSENCMS"]['client_msg'] = array("<b>Произошла ошибка при оплате заказа!</b>",'Попробуйте провести оплату еще раз или обратитесь в техническую поддержку.','error');
			}
			header("Location: ".$_JCMS->getConfig('site_url').'/balance');
			exit();
		}

		if( $_JCMS->query[0] == 'balance' && $_JCMS->query[1] == 'payment_success' && !$_JCMS->query[2] ){
			//ob_end_clean(); $this->action_payment();
			if( $_JCMS->lang == 'en' ){
				$_SESSION["JENSENCMS"]['client_msg'] = array("<b>Payment completed successfully!</b>", 'The payment will be credited within a few minutes... ','notice'); 
			} else {
				$_SESSION["JENSENCMS"]['client_msg'] = array("<b>Платёж успешно выполнен!</b>",'Оплата будет зачислена в течение нескольких минут...','notice'); 
			}
			header("Location: ".$_JCMS->getConfig('site_url').'/balance');
			exit();
		} 
				
		$noauth_pages = array('login', 'register', 'lost_password', 'confirm', 'confirmResend');

		if( $_JCMS->query[0] == 'login' && intval($_GET['god_mode'])>0 && $_SESSION["JENSENCMS"]['AUTH']['admin_id'] > 0 ){
			/* режим бога.)) Авторизация под любым акком в ЛК при наличии сессия в админпанели. */
			$adm_id = $_JCMS->db->escape_string(intval($_SESSION["JENSENCMS"]['AUTH']['admin_id']));
			$adm_tok = $_JCMS->db->escape_string($_SESSION["JENSENCMS"]['AUTH']['admin_auth_token']);
			$sql_code = "SELECT * FROM `jcms2_admins` WHERE `admin_id` = '{$adm_id}' AND `admin_auth_token` = '{$adm_tok}' AND `admin_status` = '1'";
			if( ($res = $_JCMS->db->query($sql_code)) && $res->num_rows === 1 ){
				$id = $_JCMS->db->escape_string(intval($_GET['god_mode']));
				$sql_code = "SELECT * FROM `jcms2_moduleClients` WHERE `client_id` = '{$id}' AND `client_status` = '1'";
				if( ($res = $_JCMS->db->query($sql_code)) && $res->num_rows === 1 ){
					$data = $res->fetch_assoc();
					$_SESSION["JENSENCMS"]['SITE_AUTH'] = $data;
					ob_end_clean();
					header("Location: ".PATH);
					exit();					
				}
			}
		}

		if( $this->checkAuth() && in_array($_JCMS->query[0], $noauth_pages)){
			// авторизованный юзер пытается получить доступ с траницам доступным только для неавторизованных. Сбрасываем сессию..
			header("Location: ".PATH);
			exit();
		}		 
		
		if( $_JCMS->query[0] != 'login' && !$this->checkAuth() && !in_array($_JCMS->query[0], $noauth_pages) ){
			header("Location: ".PATH."login");
			exit();
		}
		
		if( $this->checkAuth() && !$_JCMS->query[0] ){
			header("Location: ".PATH."myorders");
			exit();
		}
		
		if( $_JCMS->query[0] == 'confirm' ){

			if( $_JCMS->lang == 'en' ){
				$_META['title'][] = 'My Account';
				$_META['title'][] = 'Account activation';
			} else {
				$_META['title'][] = 'Личный кабинет';
				$_META['title'][] = 'Активация аккаунта';				
			}

			if( $_POST['client_form_submit'] == 1 ){
				if( $this->checkConfirmForm() === true ){
					header("Location: ".PATH."login");
					exit();					
				}
			}

			if( $_SESSION["JENSENCMS"]['client_msg'] ){
				$_JCMS->message($_SESSION["JENSENCMS"]['client_msg'][0], $_SESSION["JENSENCMS"]['client_msg'][1],$_SESSION["JENSENCMS"]['client_msg'][2]);
				unset($_SESSION["JENSENCMS"]['client_msg']);
			}
			
			$_JCMS->tpl->load("cabinet/confirm_{$_JCMS->lang}.tpl");
			echo $_JCMS->tpl->compile();
		}	
		
		if( $_JCMS->query[0] == 'confirmResend' ){
			if( $_JCMS->lang == 'en' ){
				$_META['title'][] = 'My Account';
				$_META['title'][] = 'Account activation';
			} else {
				$_META['title'][] = 'Личный кабинет';
				$_META['title'][] = 'Активация аккаунта';				
			}
			if( $_POST['client_form_submit'] == 1 ){
				if( $this->checkConfirmResendForm() === true ){
					header("Location: ".PATH."login");
					exit();					
				}
			}
			if( $_SESSION["JENSENCMS"]['client_msg'] ){
				$_JCMS->message($_SESSION["JENSENCMS"]['client_msg'][0], $_SESSION["JENSENCMS"]['client_msg'][1],$_SESSION["JENSENCMS"]['client_msg'][2]);
				unset($_SESSION["JENSENCMS"]['client_msg']);
			}
			
			$_JCMS->tpl->load("cabinet/confirmResend_{$_JCMS->lang}.tpl");
			echo $_JCMS->tpl->compile();
		}	
		
		if( $_JCMS->query[0] == 'login' ){
			if( $_JCMS->lang == 'en' ){
				$_META['title'][] = 'My Account';
				$_META['title'][] = 'Authentication';
			} else {
				$_META['title'][] = 'Личный кабинет';
				$_META['title'][] = 'Авторизация';
			}
			if( $_POST['client_form_submit'] == 1 ){
				if( $this->checkAuthForm() !== true ){ 
					header("Location: ".PATH."login");
					exit();
				} else {
					if( $_JCMS->lang == 'en' ){
						$_SESSION["JENSENCMS"]['client_msg'] = array("You have successfully logged in!",'','notice');
					} else {
						$_SESSION["JENSENCMS"]['client_msg'] = array("Вы успешно авторизованы!",'','notice');
					}
					if( $_JCMS->getConfig('news_mode') == '1' ){
						header("Location: ".PATH.'news');
					} else { 
						header("Location: ".PATH.'myorders');
					}
					exit();
				}
			}
			if( $_SESSION["JENSENCMS"]['client_msg'] ){
				$_JCMS->message($_SESSION["JENSENCMS"]['client_msg'][0], $_SESSION["JENSENCMS"]['client_msg'][1],$_SESSION["JENSENCMS"]['client_msg'][2]);
				unset($_SESSION["JENSENCMS"]['client_msg']);
			}
			
			$_SESSION["JENSENCMS"]['client_authToken'] = $auth_token =  md5(mt_rand().mt_rand().mt_rand().uniqid());
			$_JCMS->tpl->load("cabinet/login_{$_JCMS->lang}.tpl");
			$_JCMS->tpl->tag("{CABINET_LOGIN}", $_POST['jcms_clientLogin']?strval($_POST['jcms_clientLogin']):$_COOKIE['jcms2_cabinetLogin']);
			$_JCMS->tpl->tag("{CABINET_TOKEN}", $auth_token);
			echo $_JCMS->tpl->compile();
		}		

		if( $_JCMS->query[0] == 'register' ){
			if( $_JCMS->lang == 'en' ){
				$_META['title'][] = 'My Account';
				$_META['title'][] = 'Registration';
			} else {
				$_META['title'][] = 'Личный кабинет';
				$_META['title'][] = 'Регистрация';
			}
			
			if( $_POST['client_form_submit'] == 1 ){
				if( $this->checkRegisterForm() === true ){
					ob_end_clean();
					header("Location: ".PATH."login");
					exit();
				}
			}
			if( $_SESSION["JENSENCMS"]['client_msg'] ){
				$_JCMS->message($_SESSION["JENSENCMS"]['client_msg'][0], $_SESSION["JENSENCMS"]['client_msg'][1],$_SESSION["JENSENCMS"]['client_msg'][2]);
				unset($_SESSION["JENSENCMS"]['client_msg']);
			}
			
			$_SESSION["JENSENCMS"]['client_authToken'] = $auth_token =  md5(mt_rand().mt_rand().mt_rand().uniqid());
			$_JCMS->tpl->load("cabinet/register_{$_JCMS->lang}.tpl");
			foreach($_POST as $key=>$val){
				$_JCMS->tpl->tag("{".($key)."}", $val);
			}
			echo preg_replace("/\{[\w-]+\}/i", "", $_JCMS->tpl->compile());
		}		

		if( $_JCMS->query[0] == 'lost_password' ){
			if( $_JCMS->lang == 'en' ){
				$_META['title'][] = 'My Account';
				$_META['title'][] = 'Password recovery';
			} else {
				$_META['title'][] = 'Личный кабинет';
				$_META['title'][] = 'Восстановление пароля';
			}
			
			if( $_POST['client_form_submit'] == 1 ){
				if( $this->checkLostPasswordForm_sendEmail() === true ){
					ob_end_clean();
					header("Location: ".PATH."login");
					exit();
				}
			}
			if( $_POST['client_form_submit'] == 2 ){
				if( $this->checkLostPasswordForm_changePassword() === true ){
					ob_end_clean();
					header("Location: ".PATH."login");
					exit();
				}
			}
			if( $_SESSION["JENSENCMS"]['client_msg'] ){
				$_JCMS->message($_SESSION["JENSENCMS"]['client_msg'][0], $_SESSION["JENSENCMS"]['client_msg'][1],$_SESSION["JENSENCMS"]['client_msg'][2]);
				unset($_SESSION["JENSENCMS"]['client_msg']);
			}
			if( $_GET['token'] ){
				$_JCMS->tpl->load("cabinet/lost_password_confirm_{$_JCMS->lang}.tpl");				
			} else {
				$_JCMS->tpl->load("cabinet/lost_password_{$_JCMS->lang}.tpl");
			}
			foreach($_POST as $key=>$val){
				$_JCMS->tpl->tag("{".($key)."}", $val);
			}
			echo preg_replace("/\{[\w-]+\}/i", "", $_JCMS->tpl->compile());
		}
		
		if( $this->checkAuth() ){
			if( $_SESSION["JENSENCMS"]['client_msg'] ){
				$_JCMS->message($_SESSION["JENSENCMS"]['client_msg'][0], $_SESSION["JENSENCMS"]['client_msg'][1],$_SESSION["JENSENCMS"]['client_msg'][2]);
				unset($_SESSION["JENSENCMS"]['client_msg']);
			}


			##
			## ТЕСТОВЫЙ ДОСТУП
			##
			if( $_POST['action'] == 'buyTest' ){				
				$json = array();
				if( $_POST['type'] == 'check' ){
					$json = $this->action_buyTestCheck();
				}
				if( $_POST['type'] == 'buy' ){
					$json = $this->action_buyTestBuy();
				}
				
				echo json_encode($json);
				exit();
			}
			
			if( $_JCMS->query[0] == 'logout' ){
				$this->logout();
				header("Location: ".PATH);
				exit();
			}

			if( $_JCMS->query[0] == 'news' ){
				$json = array();
		
				$months = array('', 'января', 'февраля', 'марта', 'апреля', 'мая', 'июня', 'июля', 'августа', 'сентября', 'октября', 'ноября', 'декабря');
				
				/* вывод списка новостей */
				$_META['title'] = array("Новости"); 
		
				$data = $this->getAllNews(1,(($cur_page-1)*$this->onpage<0?0:($cur_page-1)*$this->onpage));
				if( empty($data) ){
					$_JCMS->message("Нет новостных страниц для отображения.", '', 'warning'); 
					return $json;
				}
				
				$tmp = '';
				foreach($data['news'] as $val){
					$d = strtotime($val['news_dateCreate']);
					$tpl = $_JCMS->tpl->load("news/short.tpl");
					$_JCMS->tpl->tag("{NEWS_TITLE}", $val['news_title']);			
					$_JCMS->tpl->tag("{NEWS_DATE}", date('d.m.Y', $d));//date("d ", $d).$months[date("n", $d)].' '.date(" Yг., H:i", $d));
					$_JCMS->tpl->tag("{NEWS_TEXT}", $val['news_shortText']);			
					$tmp .= $_JCMS->tpl->compile("SHORT_NEWS");
				}

				$_JCMS->tpl->load('pages/page.tpl');
				$_JCMS->tpl->tag("{PAGE_TITLE}", $_META['title'][0]);
				$_JCMS->tpl->tag("{PAGE_TEXT}", $tmp);
				$_JCMS->tpl->tag("{_PAGE_TEXT_}", '" style=display:none;""');
				$lk_body = $_JCMS->tpl->compile('NEWS') . $nav;

			}

			if( $_JCMS->query[0] == 'profile' ){				
				if( $_JCMS->lang == 'en' ){
					$lk_title = "Profile";
				} else {
					$lk_title = "Профиль";
				}
				if( $_POST['form_submit'] == 1 ){
					$this->checkForm_changeProfile();
					ob_end_clean();
					header("Location: ".PATH."profile");
					exit();
				} 
				$profile = $this->getProfile();
						
			
				// спсико категорий
				$_JCMS->tpl->load("cabinet/lk_profile_{$_JCMS->lang}.tpl");  
				$_JCMS->tpl->tag("{LK_LOGIN}", $profile['client_login']);
				$_JCMS->tpl->tag("{LK_EMAIL}", $profile['client_email']);
				$lk_body = $_JCMS->tpl->compile();
			}
			
			##
			## БАЛАНС 
			##
			if( $_JCMS->query[0] == 'balance' && !$_JCMS->query[1] ){				
				if( $_JCMS->lang == 'en' ){
					$lk_title = "My balance";
				} else {
					$lk_title = "Мой баланс";
				}

				if( $_POST['form_submit'] == 1 ){
					$this->checkForm_balancePayment();
					header("Location: ".PATH.'myorders');
					exit();
				}


				$table = '';
				$profile = $this->getProfile(); 
				if( !$res = $_JCMS->db->query("SELECT * FROM `jcms2_moduleClientsBalance` WHERE `client_id` = '{$profile[client_id]}' ORDER BY `date` DESC LIMIT 100")){
					$_JCMS->message("Ошибка базы данных! [php/module.cabinet#".__LINE__."]", $_JCMS->show_php_errors?("MYSQL ERROR #".$_JCMS->db->errno." - ".$_JCMS->db->error):'', 'error');
				} else {
					$table = ''; 
					while($data = $res->fetch_assoc() ){
						$table .= "<tr class=\"empty\"><td nowrap align=\"center\">".(date('d.m.Y H:i', strtotime($data['date'])))."</td><td nowrap align=\"center\"><span style=\"color:".($data['summ']>0?'green':'red')."\">".($data['summ']>0?'+':'&ndash;')."\$".abs($data['summ'])."</span></td><td>{$data[descr]}</td></tr>";
					}		
				}
				if( !$table ){
					if( $_JCMS->lang == 'en' ){
						$table = "<tr class=\"empty\" style=\"height:100px;\"><td colspan=\"8\" align=\"center\">no orders</td></tr>"; 
					} else {
						$table = "<tr class=\"empty\" style=\"height:100px;\"><td colspan=\"8\" align=\"center\">нет заказов</td></tr>"; 
					}
				} 
				$_JCMS->tpl->load("cabinet/lk_balance_{$_JCMS->lang}.tpl");  
				$_JCMS->tpl->tag("{WM_HIDE_MSG}", $_JCMS->getConfig('wm_mode')=='1'?"":"display:none;");
				$_JCMS->tpl->tag("{TBODY}", $table);
				$lk_body = $_JCMS->tpl->compile();
			}

			##
			## МОИ ЗАКАЗЫ
			##
			if( $_JCMS->query[0] == 'myorders' && !$_JCMS->query[1] ){				
				if( $_JCMS->lang == 'en' ){
					$lk_title = "My orders";
				} else {
					$lk_title = "Мои заказы";					
				}
				if( $_POST['form_submit'] == 1 && $_POST['action'] == 'getOrderSetting' ){
					$json = array('status'=>0);
					$order_id = $_JCMS->db->escape_string(intval($_POST['order']));
 					$profile = $this->getProfile();

					$sql_code = "SELECT * FROM `jcms2_moduleClientsOrders` WHERE `order_status` IN ('0','1') AND `order_id` = '{$order_id}' AND `client_id` = '{$profile[client_id]}'";
					if( !$res = $_JCMS->db->query($sql_code)){
						$_JCMS->message("Ошибка базы данных! [php/module.cabinet#".__LINE__."]", $_JCMS->show_php_errors?("MYSQL ERROR #".$_JCMS->db->errno." - ".$_JCMS->db->error):'', 'error');
					} elseif( $res->num_rows == 1 ){
						$data = $res->fetch_assoc();
						$json['status'] = 1; 
						$json['isTest'] = $data['order_isTest']; 
						$data['order_data'] = json_decode($data['order_data'], 1);
						$json['data']['ip'] = (array) $data['order_data']['ip'];
					}

					echo json_encode($json);
					exit();
				}

				if( $_POST['form_submit'] == 1 && $_POST['action'] == 'saveOrderSetting' ){
					$json = array('status'=>0);
					$order_id = $_JCMS->db->escape_string(intval($_POST['order']));
 					$profile = $this->getProfile();
					
					$sql_code = "SELECT * FROM `jcms2_moduleClientsOrders` WHERE `order_status` IN ('0','1') AND `order_id` = '{$order_id}' AND `client_id` = '{$profile[client_id]}'";
					if( !$res = $_JCMS->db->query($sql_code)){
						$_JCMS->message("Ошибка базы данных! [php/module.cabinet#".__LINE__."]", $_JCMS->show_php_errors?("MYSQL ERROR #".$_JCMS->db->errno." - ".$_JCMS->db->error):'', 'error');
					} elseif( $res->num_rows == 1 ){
						$data = $res->fetch_assoc();
						$data['order_data'] = json_decode($data['order_data'], 1);
						$data['order_data']['ip'] = array();
						foreach($_POST['ip'] as $val){							
							if( empty($val) ) continue;
							if( filter_var($val, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE|FILTER_FLAG_NO_RES_RANGE|FILTER_FLAG_IPV4) ){
								$data['order_data']['ip'][] = $val;
							} else {
								$json['error'] = '"'.$val.'" - Invalid IP!';
							}
							if( count($data['order_data']['ip']) >= 2 ) break;
						}
						if( $data['order_isTest'] == 1 ){
							// это тестовый заказ
							$data['order_data']['ip'] = array($data['order_data']['ip'][0]); // возможен только 1 IP
							$ip = $data['order_data']['ip'][0];
							// проверяем IP на наличие в черном списке
							$res2 = $_JCMS->db->query("SELECT * FROM `jcms2_blackListIP` WHERE `ip` = '{$ip}'");
							if( $res2 == false ){ return $json; }
							if($res2->num_rows != 0 ){
								// IP есть в базе, проверяем на принадлежность к текущему заказу
								$data2 = $res2->fetch_assoc();
								if( $data2['order_id'] != $order_id ){
									// IP использовался в другом тестовом заказе... выдаем ошибку и блокируем смену...
									if( $_JCMS->lang == 'en' ) $json['error'] = 'Test access to this IP is already issued!'; else $json['error'] = 'Тестовый доступ для этого IP уже выдавался!';
								}
							} else {
								// добавляем IP в базу...
								$sql_code = "INSERT INTO `jcms2_blackListIP`(`ip`, `order_id`, `client_id`) VALUES ('{$ip}', '{$order_id}', '{$profile[client_id]}')";
								if( !$res = $_JCMS->db->query($sql_code)){
									$_JCMS->message("Ошибка базы данных! [php/module.cabinet#".__LINE__."]", $_JCMS->show_php_errors?("MYSQL ERROR #".$_JCMS->db->errno." - ".$_JCMS->db->error):'', 'error');
								}								
							}
						}
						if( !$json['error'] ){
							$data['order_data'] = json_encode($data['order_data']);
							$sql_code = "UPDATE `jcms2_moduleClientsOrders` SET `order_data`= '{$data['order_data']}' WHERE `order_status` IN ('0','1') AND `order_id` = '{$order_id}' AND `client_id` = 	'{$profile[client_id]}'";
							if( !$res = $_JCMS->db->query($sql_code)){
								$_JCMS->message("Ошибка базы данных! [php/module.cabinet#".__LINE__."]", $_JCMS->show_php_errors?("MYSQL ERROR #".$_JCMS->db->errno." - ".$_JCMS->db->error):'', 'error');
							}
							$json['status'] = 1; 
						}
					}

					echo json_encode($json);
					exit();
				}

				if( $_POST['form_submit'] == 1 && $_POST['action'] == 'getOrderExtendData' ){
					$json = array('status'=>0);
					$order_id = $_JCMS->db->escape_string(intval($_POST['order']));
 					$profile = $this->getProfile();

					$sql_code = "SELECT *
					FROM `jcms2_moduleClientsOrders`
						LEFT JOIN `jcms2_moduleClientsTariffs` ON `jcms2_moduleClientsOrders`.`tariff_id` = `jcms2_moduleClientsTariffs`.`tariff_id`
					WHERE 
						`order_status` IN ('0','1')
						AND `order_id` = '{$order_id}'
						AND `client_id` = '{$profile[client_id]}'
						AND `order_isTest` = '0'
					";
					if( !$res = $_JCMS->db->query($sql_code)){
						$_JCMS->message("Ошибка базы данных! [php/module.cabinet#".__LINE__."]", $_JCMS->show_php_errors?("MYSQL ERROR #".$_JCMS->db->errno." - ".$_JCMS->db->error):'', 'error');
					} elseif( $res->num_rows == 1 ){
						$data = $res->fetch_assoc();
						$data['tariff_prices'] = json_decode($data['tariff_prices'], 1);
						$json['status'] = 1; 
						foreach($data['tariff_prices'] as $key=>$val){
							if( $_JCMS->lang == 'en' ){
								$json['form'] .= "<label><input name=\"period\" type=\"radio\" value=\"".$key."\"> for ".$this->tariff_periods[$_JCMS->lang][$key]['title'].", the price is $".$val."</label>";
							} else {
								$json['form'] .= "<label><input name=\"period\" type=\"radio\" value=\"".$key."\"> на ".$this->tariff_periods[$_JCMS->lang][$key]['title']." за $".$val."</label>";
							}
						} 
					}

					echo json_encode($json);
					exit();
				}
				
				// продление заказа
				if( $_POST['form_submit'] == 1 && $_POST['action'] == 'orderExtend' ){
					$this->checkForm_orderExtend();
					header("Location: ".PATH.'myorders');
					exit();
				}


				$table = '';
					$profile = $this->getProfile(); 
				if( !$res = $_JCMS->db->query("SELECT * FROM `jcms2_moduleClientsOrders` WHERE `client_id` = '{$profile[client_id]}' AND `order_status` IN ('0', '1', '2') ORDER BY `order_addDate` DESC, `order_status` DESC")){
					$_JCMS->message("Ошибка базы данных! [php/module.cabinet#".__LINE__."]", $_JCMS->show_php_errors?("MYSQL ERROR #".$_JCMS->db->errno." - ".$_JCMS->db->error):'', 'error');
				} else {
					$table = ''; 
					if( $_JCMS->lang == 'en' ) $rem_title = 'Left '; else $rem_title = 'Осталось ';
					$show_testOrderNotice = 0;
					while($data = $res->fetch_assoc() ){
						if( $data['order_isTest'] == 1 && $data['order_status'] == '2' ) $show_testOrderNotice = 1;
						if( !in_array($data['order_status'], array('0','1'))  ) continue;
						$table .= "<tr class=\"empty ".($data['order_isLock']?"noBottomBorder":"")."\"><td>{$data[order_id]}</td><td>{$data[order_title]}</td><td>{$data[order_descr]}</td><td align=\"center\">".(date('d.m.y H:i',strtotime($data['order_addDate'])).' &mdash; '.date('d.m.y H:i',strtotime($data['order_paidBefore'])))."<br><b>{$rem_title} ".$this->getCountDown($data['order_paidBefore'])."</b></td><td align=\"center\">";
						if( in_array($order_status, array(0,1)) ){
							$url = "{$_JCMS->getConfig('site_url')}/api.php?action=getFile&order={$data['order_id']}&key=".md5($data['order_id'].$data['order_addDate']);
//							$table .= "<a href=\"".$url."\">Скачать</a><br>";
							if( $_JCMS->lang == 'en' ){
								$table .= "<a href=\"#\" onClick=\"modal_alertCopy('".$url."');\">Copy</a>"; 
							} else {
								$table .= "<a href=\"#\" onClick=\"modal_alertCopy('".$url."');\">Скопировать</a>";
							}
						}
						$table .= "</td><td nowrap align=\"center\">";
						if( in_array($order_status, array(0,1)) ){
							if( $_JCMS->lang == 'en' ){
								$table .= "<button onclick=\"orderSetting('{$data[order_id]}');\" class=\"inline\" style=\"margin-bottom:5px;\"><b>Settings</b></button><br>";
								if( $data['order_isTest'] != 1 ) $table .= "<button onclick=\"orderExtend('{$data[order_id]}');\" class=\"inline\"><b>Extend</b></button>";
							} else {
								$table .= "<button onclick=\"orderSetting('{$data[order_id]}');\" class=\"inline\" style=\"margin-bottom:5px;\"><b>Настройки</b></button><br>";
								if( $data['order_isTest'] != 1 ) $table .= "<button onclick=\"orderExtend('{$data[order_id]}');\" class=\"inline\"><b>Продление</b></button>";								
							}
						}
						$table .= "</td></tr>";
						if( $data['order_isLock'] == 1 ){
							$table .= "<tr class=\"empty\" style=\"color:red\"><td colspan=\"6\"><b>Данный заказ заблокирован</b>, для выяснения обстоятельств напишите по контактам указанным ниже:<br>Telegram: @wingateproxy<br>Skype: socks5center<br>Jabber: socks5center@jabber.ru</td></tr>";
						}
					}		
				}
				if( !$table ){
					if( $_JCMS->lang == 'en' ){
						$table = "<tr class=\"empty\" style=\"height:100px;\"><td colspan=\"6\" align=\"center\">no orders</td></tr>"; 
					} else {
						$table = "<tr class=\"empty\" style=\"height:100px;\"><td colspan=\"6\" align=\"center\">нет заказов</td></tr>"; 
					}
				}
				if( $show_testOrderNotice == 1 && !$_SESSION["JENSENCMS"]['client_msg'] ){
					unset($_SESSION["JENSENCMS"]['client_msg']); 
					if( $_JCMS->lang == 'en' ){
						$_SESSION["JENSENCMS"]['client_msg'] = array("<b>Request for test access is sent to the administrator!</b>", 'To activate a test access, please provide your username on these contacts:<br><b>Telegram:</b> @wingateproxy <br><b> Skype:</b>socks5center<br><b>Jabber:</b> socks5center@jabber.ru','warning'); 
					} else {
						$_SESSION["JENSENCMS"]['client_msg'] = array("<b>Заявка на тестовый доступ отправлена администратору!</b>", 'Для активации тестового доступа сообщите ваш логин по ниже указанным контактам:<br><b>Telegram:</b> @wingateproxy<br><b>Skype:</b> socks5center<br><b>Jabber:</b> socks5center@jabber.ru','warning'); 
					}
				}
				$_JCMS->tpl->load("cabinet/lk_myorders_{$_JCMS->lang}.tpl");  
				$_JCMS->tpl->tag("{TBODY}", $table);
				$lk_body = $_JCMS->tpl->compile();
			}

			
			##
			## НОВЫЙ ЗАКАЗ
			##
			if( $_JCMS->query[0] == 'myorders' && $_JCMS->query[1] == 'buy' && !$_JCMS->query[2] ){
				if( $_JCMS->lang == 'en' ){
					$lk_title = "Buying tariff package";
				} else {
					$lk_title = "Покупка тарифного пакета";
				}
				
				if( $_POST['form_submit'] == 1 && $_POST['action'] == 'checkOrder' ){
					// формируем строку для подтверждения заказа
					$json = array('status'=>0);
					$tar_id = $_JCMS->db->escape_string(intval($_POST['tariff']));
					$period = $_JCMS->db->escape_string(strval($_POST['period']));

					$sql_code = "SELECT * FROM `jcms2_moduleClientsTariffs` WHERE `tariff_status` = '1' AND `tariff_id` = '{$tar_id}' ORDER BY `tariff_id` DESC";
					if( !$res = $_JCMS->db->query($sql_code)){
						$_JCMS->message("Ошибка базы данных! [php/module.cabinet#".__LINE__."]", $_JCMS->show_php_errors?("MYSQL ERROR #".$_JCMS->db->errno." - ".$_JCMS->db->error):'', 'error');
					} elseif( $res->num_rows == 1 ){
						$data = $res->fetch_assoc();
						$data['tariff_prices'] = json_decode($data['tariff_prices'],1);
						$json['status'] = 1; 
						if( $_JCMS->lang == 'en' ){
							$json['confirmStr'] = 'You confirm ordering "'.$data['tariff_title'].'" tariff for '.$this->tariff_periods[$_JCMS->lang][$period]['title'].', the price is $'.$data['tariff_prices'][$period].' ?';
						} else {
							$json['confirmStr'] = 'Вы подтверждаете заказ тарифа: "'.$data['tariff_title'].'" на '.$this->tariff_periods[$_JCMS->lang][$period]['title'].' за $'.$data['tariff_prices'][$period].' ?';
						}
						$json['orderForm'] = '<form method="post"><input name="form_submit" value="1" type="hidden"/><input name="action" value="buy" type="hidden"/><input name="tariff" value="'.$data['tariff_id'].'" type="hidden"/><input name="period" value="'.$period.'" type="hidden"/></form>';
					}

					echo json_encode($json);
					exit();
				}
				if( $_POST['form_submit'] == 1 && $_POST['action'] == 'buy' ){
					$this->checkForm_buyTariff();
					header("Location: ".PATH.'myorders');
					exit();
				}
				$table = '';
				$sql_code = "SELECT * FROM `jcms2_moduleClientsTariffs` WHERE `tariff_status` = '1' ORDER BY `tariff_id` DESC";
				if( !$res = $_JCMS->db->query($sql_code)){
					$_JCMS->message("Ошибка базы данных! [php/module.cabinet#".__LINE__."]", $_JCMS->show_php_errors?("MYSQL ERROR #".$_JCMS->db->errno." - ".$_JCMS->db->error):'', 'error');
				} else {
					$table = '';
					while($data = $res->fetch_assoc() ){
						$data['tariff_prices'] = json_decode($data['tariff_prices'],1);
						$n = 0;
						$max = count($data['tariff_prices']);
						foreach($data['tariff_prices'] as $key=>$val){ 
							$n++; 
							if( $n != $max ) $class = ""; else $class = "last";
							
								$table .= "<tr class=\"empty {$class}\">";
								if( $n == 1 ){
									$table.= "<td rowspan=\"{$max}\">{$data[tariff_title]}</td><td rowspan=\"{$max}\">{$data[tariff_descr]}</td>";
								}
								if( $_JCMS->lang == 'en' ){
									$table .= "<td>{$this->tariff_periods[$_JCMS->lang][$key]['title']}</td><td>\${$val}</td><td><button onclick=\"buy('{$data[tariff_id]}', '{$key}');\" class=\"inline\"><b>Purchase</b></button></td></tr>";
								} else {
									$table .= "<td>{$this->tariff_periods[$_JCMS->lang][$key]['title']}</td><td>\${$val}</td><td><button onclick=\"buy('{$data[tariff_id]}', '{$key}');\" class=\"inline\"><b>Купить</b></button></td></tr>";
								}							
						}
					}		
				}
				if( !$table ){
					if( $_JCMS->lang == 'en' ){
						$table = "<tr class=\"empty\" style=\"height:100px;\"><td colspan=\"5\">no tariffs available for ordering</td></tr>"; 
					} else {
						$table = "<tr class=\"empty\" style=\"height:100px;\"><td colspan=\"5\">нет доступных тарифов для заказа</td></tr>"; 
					}
				} 

				$_JCMS->tpl->load("cabinet/lk_myorders_buy_{$_JCMS->lang}.tpl");  
				$_JCMS->tpl->tag("{TBODY}", $table);
				$lk_body = $_JCMS->tpl->compile();
			}

			##
			## ИСТОРИЯ ЗАКАЗОВ
			##
			if( $_JCMS->query[0] == 'orderHistory' && !$_JCMS->query[1] ){		
				if( $_JCMS->lang == 'en' ){		
					$lk_title = "История заказов";
				} else {
					$lk_title = "Orders history";
				}

				$table = '';
					$profile = $this->getProfile(); 
				if( !$res = $_JCMS->db->query("SELECT * FROM `jcms2_moduleClientsOrders` WHERE `client_id` = '{$profile[client_id]}' AND `order_status` IN ('2','-1') ORDER BY `order_paidBefore` DESC, `order_status` DESC")){
					$_JCMS->message("Ошибка базы данных! [php/module.cabinet#".__LINE__."]", $_JCMS->show_php_errors?("MYSQL ERROR #".$_JCMS->db->errno." - ".$_JCMS->db->error):'', 'error');
				} else {
					$table = ''; 
					while($data = $res->fetch_assoc() ){
						$table .= "<tr class=\"empty\"><td>{$data[order_id]}</td><td>{$data[order_title]}</td><td>{$data[order_descr]}</td><td align=\"center\">".(date('d.m.y H:i',strtotime($data['order_addDate'])).' &mdash; '.date('d.m.y H:i',strtotime($data['order_paidBefore'])))."</td></tr>";
					}		
				}
				if( !$table ){
					$table = "<tr class=\"empty\" style=\"height:100px;\"><td colspan=\"4\" align=\"center\">no orders</td></tr>"; 
				} 
				$_JCMS->tpl->load("cabinet/lk_orderHistory_{$_JCMS->lang}.tpl");  
				$_JCMS->tpl->tag("{TBODY}", $table);
				$lk_body = $_JCMS->tpl->compile();
			}

			## 
			## ТИКЕТЫ
			##
			if( $_JCMS->query[0] == 'support' && !$_JCMS->query[1] ){				
				if( $_JCMS->lang == 'en' ){		
					$lk_title = "Technical support";
				} else {
					$lk_title = "Техническая поддержка";
				}
				if( $_POST['form_submit'] == 1 && $_POST['action'] == 'newTicket' ){
					$json = array('status'=>0);
					$title = $_JCMS->db->escape_string(trim(htmlentities(strip_tags($_POST['title']),ENT_QUOTES, 'utf-8')));
					$text = $_JCMS->db->escape_string(str_replace("\n", "<br>", trim(htmlentities(strip_tags($_POST['text']),ENT_QUOTES, 'utf-8'))));
					if( !empty($title) && !empty($text) ){
						$ticket_id = $_JCMS->db->escape_string($this->genTicketId());
						$profile = $this->getProfile();
						
						// создаем тикет...
						$sql_code = "INSERT INTO `jcms2_moduleClientsTickets`(`ticket_id`, `ticket_title`, `client_id`) VALUES ('{$ticket_id}', '{$title}', '{$profile[client_id]}')";
						if( !$res = $_JCMS->db->query($sql_code)){
							$_JCMS->message("Ошибка базы данных! [php/module.cabinet#".__LINE__."]", $_JCMS->show_php_errors?("MYSQL ERROR #".$_JCMS->db->errno." - ".$_JCMS->db->error):'', 'error');
						} else {
							// добавляем первое сообщение...
							$sql_code = "INSERT INTO `jcms2_moduleClientsTicketsMsgs`(`ticket_id`, `msg_text`, `client_id`) VALUES ('{$ticket_id}', '{$text}', '{$profile[client_id]}')";
							if( !$res = $_JCMS->db->query($sql_code)){
								$_JCMS->message("Ошибка базы данных! [php/module.cabinet#".__LINE__."]", $_JCMS->show_php_errors?("MYSQL ERROR #".$_JCMS->db->errno." - ".$_JCMS->db->error):'', 'error');
							} else {
								$json['status'] = 1; 
								$json['url'] = $_JCMS->getConfig('site_url').'/support/'.$ticket_id; 
							}						
						}
					}
					
					echo json_encode($json);
					exit();
				}

				$table = '';
				$profile = $this->getProfile(); 
				if( !$res = $_JCMS->db->query("SELECT * FROM `jcms2_moduleClientsTickets` WHERE `client_id` = '{$profile[client_id]}' ORDER BY `ticket_addDate` DESC, `ticket_status` DESC")){
					$_JCMS->message("Ошибка базы данных! [php/module.cabinet#".__LINE__."]", $_JCMS->show_php_errors?("MYSQL ERROR #".$_JCMS->db->errno." - ".$_JCMS->db->error):'', 'error');
				} else {
					$table = ''; 
					while($data = $res->fetch_assoc() ){
						$table .= "<tr><td align=\"center\">{$data[ticket_id]}</td><td>{$data[ticket_title]}</td><td align=\"center\">".date('d.m.y H:i',strtotime($data['ticket_addDate']))."</td><td align=\"center\">{$this->ticket_status[$_JCMS->lang][$data[ticket_status]]}</td></tr>";
					}		
				}
				if( !$table ){
					if( $_JCMS->lang == 'en' ){	
						$table = "<tr class=\"empty\" style=\"height:100px;\"><td colspan=\"4\" align=\"center\">no requests</td></tr>"; 
					} else {
						$table = "<tr class=\"empty\" style=\"height:100px;\"><td colspan=\"4\" align=\"center\">нет запросов</td></tr>"; 
					}
				} 
				
				$_JCMS->tpl->load("cabinet/lk_tickets_{$_JCMS->lang}.tpl");  
				$_JCMS->tpl->tag("{TBODY}", $table);
				$lk_body = $_JCMS->tpl->compile();
			}
			
			##
			## ТИКЕТЫ - ЧАТ
			##
			if( $_JCMS->query[0] == 'support' && ctype_digit($_JCMS->query[1]) && mb_strlen($_JCMS->query[1], 'utf-8') == 6 && !$_JCMS->query[2] ){
				$ticket_id = $_JCMS->db->escape_string(intval($_JCMS->query[1]));
				if( $_JCMS->lang == 'en' ){	
					$lk_title = "Request №{$ticket_id} / Technical support";
				} else {
					$lk_title = "Запрос №{$ticket_id} / Техническая поддержка";
				}
				$profile = $this->getProfile(); 
				if( !$res = $_JCMS->db->query("SELECT * FROM `jcms2_moduleClientsTickets` WHERE `client_id` = '{$profile[client_id]}' AND `ticket_id` = '{$ticket_id}'")){
					$_JCMS->message("Ошибка базы данных! [php/module.cabinet#".__LINE__."]", $_JCMS->show_php_errors?("MYSQL ERROR #".$_JCMS->db->errno." - ".$_JCMS->db->error):'', 'error');
				} elseif( $res->num_rows == 1 ){
					// AJAX CHAT
					if( $_POST['action'] == 'getChatLog' ){
						ob_end_clean();
						$json = array();
						$json['status'] = 0;
						$msg_id = $_JCMS->db->escape_string(intval($_POST['lastMsg']));
						if( !$res = $_JCMS->db->query("SELECT * FROM `jcms2_moduleClientsTicketsMsgs` WHERE `ticket_id` = '{$ticket_id}' AND `msg_id` > '{$msg_id}' AND `msg_adminOnly` = '0' ORDER BY `msg_date` ASC")){
							$_JCMS->message("Ошибка базы данных! [php/module.cabinet#".__LINE__."]", $_JCMS->show_php_errors?("MYSQL ERROR #".$_JCMS->db->errno." - ".$_JCMS->db->error):'', 'error');
						} else {
							$json['status'] = 1;	
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
							if( $data['ticket_status'] != 3 ){ 
								if( $data['ticket_status'] != 0 ){ 
									// меняем статус на "ожидает ответа админа"
									$sql_code = "UPDATE `jcms2_moduleClientsTickets` SET `ticket_status` = '0' WHERE `ticket_id` = '{$ticket_id}' AND `ticket_status` != '3'";
									if( !$res = $_JCMS->db->query($sql_code)){
										$_JCMS->message("Ошибка базы данных! [php/module.cabinet#".__LINE__."]", $_JCMS->show_php_errors?("MYSQL ERROR #".$_JCMS->db->errno." - ".$_JCMS->db->error):'', 'error');
										return;
									} 								
								}
								// добавляем сообщение...
								$sql_code = "INSERT INTO `jcms2_moduleClientsTicketsMsgs`(`ticket_id`, `msg_text`, `client_id`) VALUES ('{$ticket_id}', '{$text}', '{$profile[client_id]}')";
								if( !$res = $_JCMS->db->query($sql_code)){
									$_JCMS->message("Ошибка базы данных! [php/module.cabinet#".__LINE__."]", $_JCMS->show_php_errors?("MYSQL ERROR #".$_JCMS->db->errno." - ".$_JCMS->db->error):'', 'error');
								} else {
									$json['status'] = 1; 
								}		
							} else {
								if( $_JCMS->lang == 'en' ){
									$json['error'] = 'The ticket is blocked!';
								} else {
									$json['error'] = 'Тикет заблокирован!';
								}
							}
						}
						
						echo json_encode($json);
						exit();
					}
					$data = $res->fetch_assoc();
					$_JCMS->tpl->load("cabinet/lk_ticketChat_{$_JCMS->lang}.tpl");  
					$_JCMS->tpl->tag("{TICKET_TITLE}", $data['ticket_title']);
					$_JCMS->tpl->tag("{TICKET_ID}", $data['ticket_id']);
					$_JCMS->tpl->tag("{TICKET_STATUS}", $data['ticket_status']);
					$lk_body = $_JCMS->tpl->compile();
				}
			}

			##
			## РЕФ ПРОГРАММА
			##
			if( $_JCMS->query[0] == 'referals' && !$_JCMS->query[1] ){
				if( $_JCMS->lang == 'en' ){
					$lk_title = "Referral program";
				} else {
					$lk_title = "Реферальная программа";
				}
				$ticket_id = $_JCMS->db->escape_string(intval($_JCMS->query[1]));
				$profile = $this->getProfile();
				if( $profile['client_referalCode'] == NULL ){
					// у юзера еще нет реф. кода. Создаем...
					$ref_code = $this->genReferalCode();
					if( !$res = $_JCMS->db->query("UPDATE `jcms2_moduleClients` SET `client_referalCode` = '{$ref_code}' WHERE `client_id` = '{$profile['client_id']}'")){
						$_JCMS->message("Ошибка базы данных! [php/module.cabinet#".__LINE__."]", $_JCMS->show_php_errors?("MYSQL ERROR #".$_JCMS->db->errno." - ".$_JCMS->db->error):'', 'error');
					}
					$this->checkAuth(); // обновляем кэш профиля
					$profile = $this->getProfile();
				}
				if( !$res = $_JCMS->db->query("SELECT * FROM `jcms2_moduleClients` WHERE `client_ref_id` = '{$profile[client_id]}' AND `client_status` = '1' ORDER BY `client_regDate` DESC")){
					$_JCMS->message("Ошибка базы данных! [php/module.cabinet#".__LINE__."]", $_JCMS->show_php_errors?("MYSQL ERROR #".$_JCMS->db->errno." - ".$_JCMS->db->error):'', 'error');
				} else {
					$tbody = ''; 
					while($data = $res->fetch_assoc() ){
						if( !$res2 = $_JCMS->db->query("SELECT SUM(`summ`) as `ref_profit` FROM `jcms2_moduleClientsBalance` WHERE `ref_id` = '{$data[client_id]}'")){
							$_JCMS->message("Ошибка базы данных! [php/module.cabinet#".__LINE__."]", $_JCMS->show_php_errors?("MYSQL ERROR #".$_JCMS->db->errno." - ".$_JCMS->db->error):'', 'error');
						} else {
							$data2 = $res2->fetch_assoc();
						}
						$tbody .= "<tr><td align=\"center\">".date('d.m.Y H:i', strtotime($data['client_regDate']))."</td><td>".$this->hideStr($data['client_login'])."</td><td align=\"center\">$".floatval($data2['ref_profit'])."</td></tr>";
					}		
				}
				if( !$tbody ){
					$tbody = "<tr class=\"empty\" style=\"height:100px;\"><td colspan=\"3\" align=\"center\">no referrals</td></tr>"; 
				} 
				$_JCMS->tpl->load("cabinet/lk_referals_{$_JCMS->lang}.tpl");  
				$_JCMS->tpl->tag("{REFLINK}", $_JCMS->getConfig('site_url').'/?ref='.$profile['client_referalCode']);
				$_JCMS->tpl->tag("{TBODY}", $tbody);
				$_JCMS->tpl->tag("{TFOOT}", $tfoot);
				$lk_body = $_JCMS->tpl->compile();
			}
			
# # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # #
			if( $_SESSION["JENSENCMS"]['client_msg'] ){
				$_JCMS->message($_SESSION["JENSENCMS"]['client_msg'][0], $_SESSION["JENSENCMS"]['client_msg'][1],$_SESSION["JENSENCMS"]['client_msg'][2]);
				unset($_SESSION["JENSENCMS"]['client_msg']);
			}
			if( !empty($lk_body) ){ 
				if( $_JCMS->lang == 'en' ){
					$_META['title'][] = 'My account';			
				} else {
					$_META['title'][] = 'Личный кабинет';			
				}
				if( $lk_title) $_META['title'][] = $lk_title;			
				$_JCMS->tpl->load("cabinet/lk_page_{$_JCMS->lang}.tpl");
				$_JCMS->tpl->tag("{LK_PAGE_TITLE}", $lk_title?' / '.$lk_title:'');
				$_JCMS->tpl->tag("{LK_PAGE_TEXT}", $lk_body);
				$_JCMS->tpl->tag("{LK_BALANCE}", $this->getBalance());
				echo preg_replace("/\{[\w-]+\}/i", "", $_JCMS->tpl->compile());
			}
		}
	}  
 
 	function action_buyTestCheck(){
		global $_JCMS; 
		$json = array('status'=>0);
		$profile = $this->getProfile();
		$client_id = $profile['client_id']; 
		$old_id = $_JCMS->db->escape_string(intval($_COOKIE['jcms2_uid'])); // ид юзера который ране заказывал демо доступ (если есть)
		if( $old_id < 1 ) $old_id = 0;
		// тест же покупался...
		// проверяем есть ли реферал у этого юзера....
		$res = $_JCMS->db->query("SELECT * FROM `jcms2_moduleClientsOrders` WHERE `client_id` = '{$client_id}' AND `order_isTest` = '1'");
		if( $res == false ){ return $json; }
		if($res->num_rows == 0 ){ 
			// доступ еще не давался...
			$json['status'] = '1';			 
		} else {
			$data = $res->fetch_assoc(); 
			if( $data['order_status'] == 1 && strtotime($data['order_paidBefore']) > time() ){
				$json['status'] = '2';			
				$json['url'] = "{$_JCMS->getConfig('site_url')}/api.php?action=getFile&order={$data['order_id']}&key=".md5($data['order_id'].$data['order_addDate']);
				return $json;
			}
			// доступ уже давался... Возможно на другом акке (отслежено по кукам)
			if( $_JCMS->lang == 'en' ){
				$json['error'] = "Request for test access has already been sent to the administrator!\n\nTo activate a test access, please provide your username on these contacts:\n\nTelegram: @wingateproxy\nSkype: socks5center\nJabber: socks5center@jabber.ru";
			} else {
				$json['error'] = "Заявка на тестовый доступ ранее уже была отправлена администратору!\n\nДля активации тестового доступа сообщите ваш логин по ниже указанным контактам:\n\nTelegram: @wingateproxy\nSkype: socks5center\nJabber: socks5center@jabber.ru";				
			}
			return $json;
		}

		return $json;
	}  
	 
 	function action_buyTestBuy(){
		global $_JCMS; 
		$json = array('status'=>0);
		$ip = $_JCMS->db->escape_string($_POST['ip']); // ид юзера который ране заказывал демо доступ (если есть)
		if( intval($_POST['faqConfirm']) != '1' ){
			if( $_JCMS->lang == 'en' ) $json['error'] = 'Please, read the FAQ!'; else $json['error'] = 'Ознакомьтесь с FAQ!';
			return $json;
		}
		if( !filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE|FILTER_FLAG_NO_RES_RANGE|FILTER_FLAG_IPV4) ){
			if( $_JCMS->lang == 'en' ) $json['error'] = 'Invalid IP!'; else $json['error'] = 'Указан неверый IP!';
			return $json;
		}
		
		// проверяем IP на наличие в чернмо списке
		$res = $_JCMS->db->query("SELECT * FROM `jcms2_blackListIP` WHERE `ip` = '{$ip}'");
		if( $res == false ){ return $json; }
		if($res->num_rows != 0 ){ 
			if( $_JCMS->lang == 'en' ) $json['error'] = 'Test access to this IP is already issued!'; else $json['error'] = 'Тестовый доступ для этого IP уже выдавался!';
			return $json;
		}
		
		$old_id = $_JCMS->db->escape_string(intval($_COOKIE['jcms2_uid'])); // ид юзера который ране заказывал демо доступ (если есть)
		$profile = $this->getProfile();
		$client_id = $profile['client_id']; 
		if( $old_id < 1 ) $old_id = 0;
		$res = $_JCMS->db->query("SELECT * FROM `jcms2_moduleClientsOrders` WHERE `client_id` = '{$client_id}' AND `order_isTest` = '1'");
		if( $res == false ){ return $json; }
		if($res->num_rows == 0 ){ 
			// доступ еще не давался...

			// проверяем заявку на дубли
			$isFake = 0;
			if( $old_id > 0 ){
				$res = $_JCMS->db->query("SELECT * FROM `jcms2_moduleClientsOrders` WHERE `client_id` = '{$old_id}' AND `order_isTest` = '1'");
				if( $res == false ){ return $json; }
				if($res->num_rows != 0 ){
					$isFake = $old_id;
				}
			}
			if( $isFake > 0 ){
				$sql_isFake = '\'1\'';
			} else $sql_isFake = 'NULL';
			$order_id = $this->genOrderId();
			$profile =  $this->getProfile();
			// создаем заказ
			$order_data = json_encode(array('ip'=>array($ip)));
			if( $_JCMS->lang == 'en' ) $str = 'Request test access'; else $str = 'Запрос тестового доступа';
			$sql_code = "INSERT INTO `jcms2_moduleClientsOrders`(`order_id`, `order_title`, `order_descr`, `order_paidBefore`, `order_data`, `order_status`, `client_id`, `order_isTest`, `order_isTestFake`) VALUES ('{$order_id}', '{$str}', 'Mix all world 7000-15000 socks5 online', (CURRENT_TIMESTAMP + INTERVAL 1 HOUR), '{$order_data}', '2', '{$profile['client_id']}', '1', {$sql_isFake})";
			if( !$res = $_JCMS->db->query($sql_code)){
				$_SESSION["JENSENCMS"]['client_msg'] = array("Ошибка базы данных! [php/module.cabinet#".__LINE__."]", $_JCMS->show_php_errors?("MYSQL ERROR #".$_JCMS->db->errno." - ".$_JCMS->db->error):'', 'error');
				return;
			} 

			// добавляем IP в базу...
			$sql_code = "INSERT INTO `jcms2_blackListIP`(`ip`, `order_id`, `client_id`) VALUES ('{$ip}', '{$order_id}', '{$profile[client_id]}')";
			if( !$res = $_JCMS->db->query($sql_code)){
				$_JCMS->message("Ошибка базы данных! [php/module.cabinet#".__LINE__."]", $_JCMS->show_php_errors?("MYSQL ERROR #".$_JCMS->db->errno." - ".$_JCMS->db->error):'', 'error');
			}								

			$json['status'] = 1;
			return $json; 
			
		} else {
			// доступ уже давался... Возможно на другом акке (отслежено по кукам)
			if( $_JCMS->lang == 'en' ) $json['error'] = 'We have already provided you with a test access!'; else $json['error'] = 'Ранее Вам уже выдавался тестовый доступ!';
			return $json;
		}

		return $json;
	}  
	 
	function checkForm_buyTariff(){
		global $_JCMS;
		$tar_id = $_JCMS->db->escape_string(intval($_POST['tariff']));
		$period = $_JCMS->db->escape_string(strval($_POST['period']));
		if( !$this->tariff_periods[$_JCMS->lang][$period] ){ $_SESSION["JENSENCMS"]['client_msg'] = array("Error!",'','error'); return; }
		
		$sql_code = "SELECT * FROM `jcms2_moduleClientsTariffs` WHERE `tariff_status` = '1' AND `tariff_id` = '{$tar_id}'";
		if( !$res = $_JCMS->db->query($sql_code)){
			$_JCMS->message("Ошибка базы данных! [php/module.cabinet#".__LINE__."]", $_JCMS->show_php_errors?("MYSQL ERROR #".$_JCMS->db->errno." - ".$_JCMS->db->error):'', 'error');
		} elseif( $res->num_rows == 1 ){
			$data = $res->fetch_assoc();
			$data['tariff_prices'] = json_decode($data['tariff_prices'],1);
			$summ = floatval($data['tariff_prices'][$period]);
			if( $summ > 0 && $this->getBalance() >= $summ ){
				$order_id = $this->genOrderId();
				$profile =  $this->getProfile();
				// выполняем заказ
				// расход на баланс
				if( $_JCMS->lang == 'en' ) $str = "Payment order №{$order_id} on {$this->tariff_periods[$_JCMS->lang][$period]['title']}."; else $str = "Оплата заказа №{$order_id} на {$this->tariff_periods[$_JCMS->lang][$period]['title']}.";
				$sql_code = "INSERT INTO `jcms2_moduleClientsBalance`(`client_id`, `summ`, `descr`, `order_id`) VALUES ('{$profile['client_id']}', '-{$summ}', '{$str}', '{$order_id}');";
				if( !$res = $_JCMS->db->query($sql_code)){
					$_SESSION["JENSENCMS"]['client_msg'] = array("Ошибка базы данных! [php/module.cabinet#".__LINE__."]", $_JCMS->show_php_errors?("MYSQL ERROR #".$_JCMS->db->errno." - ".$_JCMS->db->error):'', 'error');
					return;
				}
				$bal_id = $_JCMS->db->insert_id;
				// создаем заказ 
				$sql_code = "INSERT INTO `jcms2_moduleClientsOrders`(`order_id`, `tariff_id`, `order_title`, `order_descr`, `order_paidBefore`, `order_status`, `client_id`, `order_notifyDate`) VALUES ('{$order_id}', '{$tar_id}', '{$data['tariff_title']}', '{$data['tariff_descr']}', (CURRENT_TIMESTAMP + INTERVAL {$this->tariff_periods[$_JCMS->lang][$period]['interval']}), '0', '{$profile['client_id']}', ((CURRENT_TIMESTAMP + INTERVAL {$this->tariff_periods[$_JCMS->lang][$period]['interval']}) - INTERVAL 1 DAY))";
				if( !$res = $_JCMS->db->query($sql_code)){
					$_SESSION["JENSENCMS"]['client_msg'] = array("Ошибка базы данных! [php/module.cabinet#".__LINE__."]", $_JCMS->show_php_errors?("MYSQL ERROR #".$_JCMS->db->errno." - ".$_JCMS->db->error):'', 'error');
					return;
				} 
				if( $_JCMS->lang == 'en' ){
					$_SESSION["JENSENCMS"]['client_msg'] = array("Your order №{$order_id} has been successfully created!", 'It will be activated within a few minutes...','notice');
				} else {
					$_SESSION["JENSENCMS"]['client_msg'] = array("Ваш заказ №{$order_id} успешно создан!", 'В течение нескольких минут заказ будет активирован...','notice');
				}
				return; 
			} elseif( $summ > $this->getBalance() ){
				if( $_JCMS->lang == 'en' ){
					$_SESSION["JENSENCMS"]['client_msg'] = array("There is not enough money on your balance to pay for the order!", 'You need $'.($summ-$this->getBalance()).' more to pay for it.','error');
				} else {
					$_SESSION["JENSENCMS"]['client_msg'] = array("На Вашем балансе недостаточно средств для оплаты заказа!",'Для оплаты нужно еще $'.($summ-$this->getBalance()).'.','error');
				}
				return; 
			}						
		}
	}

	function checkForm_orderExtend(){
		global $_JCMS;
		$order_id = $_JCMS->db->escape_string(intval($_POST['order']));
		$period = $_JCMS->db->escape_string(strval($_POST['period']));

		if( !$this->tariff_periods[$_JCMS->lang][$period] ){ $_SESSION["JENSENCMS"]['client_msg'] = array("Error!",'','error'); return; }
		$profile = $this->getProfile();

		$sql_code = "SELECT *
		FROM `jcms2_moduleClientsOrders`
			LEFT JOIN `jcms2_moduleClientsTariffs` ON `jcms2_moduleClientsOrders`.`tariff_id` = `jcms2_moduleClientsTariffs`.`tariff_id`
		WHERE 
			`order_status` IN ('0','1')
			AND `order_id` = '{$order_id}'
			AND `client_id` = '{$profile[client_id]}'
			AND `order_isTest` = '0'
		";
		if( !$res = $_JCMS->db->query($sql_code)){
			$_JCMS->message("Ошибка базы данных! [php/module.cabinet#".__LINE__."]", $_JCMS->show_php_errors?("MYSQL ERROR #".$_JCMS->db->errno." - ".$_JCMS->db->error):'', 'error');
		} elseif( $res->num_rows == 1 ){
			$data = $res->fetch_assoc();
			$data['tariff_prices'] = json_decode($data['tariff_prices'], 1);			
			$summ = floatval($data['tariff_prices'][$period]);
			if( $summ > 0 && $this->getBalance() >= $summ ){
				$profile =  $this->getProfile();
				// выполняем заказ
				// расход на баланс
				if( $_JCMS->lang == 'en' ) $str = "Extending the order №{$order_id} on {$this->tariff_periods[$_JCMS->lang][$period]['title']}."; else $str = "Продление заказа №{$order_id} на {$this->tariff_periods[$_JCMS->lang][$period]['title']}.";
				$sql_code = "INSERT INTO `jcms2_moduleClientsBalance`(`client_id`, `summ`, `descr`) VALUES ('{$profile['client_id']}', '-{$summ}', '{$str}');";
				if( !$res = $_JCMS->db->query($sql_code)){
					$_SESSION["JENSENCMS"]['client_msg'] = array("Ошибка базы данных! [php/module.cabinet#".__LINE__."]", $_JCMS->show_php_errors?("MYSQL ERROR #".$_JCMS->db->errno." - ".$_JCMS->db->error):'', 'error');
					return;
				}
				// продлеваем заказ 
				$sql_code = "UPDATE `jcms2_moduleClientsOrders` SET `order_paidBefore`=(`order_paidBefore` + INTERVAL {$this->tariff_periods[$_JCMS->lang][$period]['interval']}), `order_status` = '1', `order_notifyDate` = ((`order_paidBefore` + INTERVAL {$this->tariff_periods[$_JCMS->lang][$period]['interval']}) - INTERVAL 1 DAY) WHERE `order_id` = '{$order_id}'";
				if( !$res = $_JCMS->db->query($sql_code)){
					$_SESSION["JENSENCMS"]['client_msg'] = array("Ошибка базы данных! [php/module.cabinet#".__LINE__."]", $_JCMS->show_php_errors?("MYSQL ERROR #".$_JCMS->db->errno." - ".$_JCMS->db->error):'', 'error');
					return;
				}  
				if( $_JCMS->lang == 'en' ){
					$_SESSION["JENSENCMS"]['client_msg'] = array("Your order №{$order_id} has been successfully extended to {$this->tariff_periods[$_JCMS->lang][$period]['title']}!", '','notice');
				} else {
					$_SESSION["JENSENCMS"]['client_msg'] = array("Ваш заказ №{$order_id} успешно продлён на {$this->tariff_periods[$_JCMS->lang][$period]['title']}!", '','notice');
				}
				return; 
			} elseif( $summ > $this->getBalance() ){
				if( $_JCMS->lang == 'en' ){
					$_SESSION["JENSENCMS"]['client_msg'] = array("There is not enough money on your balance to extend the order!", 'You need  $'.($summ-$this->getBalance()).' more to pay for it.','notice');
				} else {
					$_SESSION["JENSENCMS"]['client_msg'] = array("На Вашем балансе недостаточно средств для продления заказа!",'Для продления нужно еще $'.($summ-$this->getBalance()).'.','error'); return; 
				}
			}						
		}
	}
	
	function checkRegisterForm(){
		global $_JCMS;
		if( $_POST['client_form_submit'] == 1 ){
			$_POST = $d = $_JCMS->escape($_POST,1);
			$d['jcms_clientLogin'] = $_JCMS->db->escape_string(preg_replace("/[^a-z0-9]/i", "", $d['jcms_clientLogin']));
			$d['jcms_clientEmail'] = $_JCMS->db->escape_string(preg_replace("/[^a-z0-9@.]/i", "", $d['jcms_clientEmail']));
			$d['jcms_clientPassw1'] = $_JCMS->db->escape_string(preg_replace("/[^a-z0-9]/i", "", $d['jcms_clientPassw1']));
			$d['jcms_clientPassw2'] = $_JCMS->db->escape_string(preg_replace("/[^a-z0-9]/i", "", $d['jcms_clientPassw2']));
			$_POST = $d;
			$_POST['jcms_clientCaptcha'] = strtolower($_POST['jcms_clientCaptcha']);
			if( empty($_POST['jcms_clientCaptcha']) || $_SESSION["JENSENCMS"]['captcha'] != $_POST['jcms_clientCaptcha'] ){
				unset($_SESSION["JENSENCMS"]['captcha']);
				if( $_JCMS->lang == 'en' ){
					$_SESSION["JENSENCMS"]['client_msg'] = array("Error: Invalid security code from the image!",'','error');
				} else {
					$_SESSION["JENSENCMS"]['client_msg'] = array("Ошибка: неверный защитный код с картинки!",'','error');
				}
				return;													
			}
			if( empty($d['jcms_clientLogin']) || empty($d['jcms_clientEmail']) || empty($d['jcms_clientPassw1']) || empty($d['jcms_clientPassw2']) ){
				if( $_JCMS->lang == 'en' ){
					$_SESSION["JENSENCMS"]['client_msg'] = array("Error: one or more fields of the form is not filled!", 'Fill in all fields and try again', 'error');
				} else {
					$_SESSION["JENSENCMS"]['client_msg'] = array("Ошибка: одно или несколько полей формы не заполнено!",'Заполните все поля и попробуйте снова','error');
				}
				return;									
			}

			if( empty($d['jcms_clientLogin']) || !preg_match("/[a-z0-9]/i", $d['jcms_clientLogin']) || mb_strlen($d['jcms_clientLogin']) < 6 ){
				if( $_JCMS->lang == 'en' ){
					$_SESSION["JENSENCMS"]['client_msg'] = array("Error: &laquo;Login&raquo; field is not filled or filled incorrectly!", "Only the Latin letters and number are allowed to use in this field. The minimum login length is 6 characters. <br> Be sure to fill it correctly and try again.", 'error');
				} else {
					$_SESSION["JENSENCMS"]['client_msg'] = array("Ошибка: поле &laquo;Логин&raquo; не заполнено или заполнено неправильно!", "В этом поле допустимо использовать только латинские буквы и цифры. Минимальная длина логина 6 символов.<br>Проверьте правильность заполнения поля и попробуйте снова.", 'error');
				}
				return $json;
			}

			if( empty($d['jcms_clientEmail']) || !filter_var($d['jcms_clientEmail'], FILTER_VALIDATE_EMAIL) ){
				if( $_JCMS->lang == 'en' ){
					$_SESSION["JENSENCMS"]['client_msg'] = array("Error: Field &laquo;E-mail address&raquo; is not filled or filled incorrectly!", "Check the correctness of the filled fields and try again.", 'error');
				} else {
					$_SESSION["JENSENCMS"]['client_msg'] = array("Ошибка: поле &laquo;Адрес эл. почты&raquo; не заполнено или заполнено неправильно!", "Проверьте правильность заполнения поля и попробуйте снова.", 'error');
				}
				return $json;
			}
			
			$email_domain = explode("@", $d['jcms_clientEmail']); $email_domain = $email_domain[1];
			$sql_code = "SELECT * FROM `jcms2_blackListEmail` WHERE `domain` = '{$email_domain}'";

			if( !$res = $_JCMS->db->query($sql_code) ){
				$_SESSION["JENSENCMS"]['client_msg'] = array("Ошибка базы данных! [php/module.cabinet#".__LINE__."]", $_JCMS->show_php_errors?("MYSQL ERROR #".$_JCMS->db->errno." - ".$_JCMS->db->error):'', 'error'); 
				return;
			}
			if( $res->num_rows == 1 ){
				if( $_JCMS->lang == 'en' ){
					$_SESSION["JENSENCMS"]['client_msg'] = array("Error: register with the specified email administrator is locked!", "", 'error');
				} else {
					$_SESSION["JENSENCMS"]['client_msg'] = array("Ошибка: регистрация с указанным email заблокирована администратором!", "", 'error');
				}
				return $json;
			}
			

			if( empty($d['jcms_clientPassw1']) || !preg_match("/[a-z0-9]/i", $d['jcms_clientPassw1']) || mb_strlen($d['jcms_clientPassw1']) < 8 ){
				if( $_JCMS->lang == 'en' ){
					$_SESSION["JENSENCMS"]['client_msg'] = array("Error: &laquo;Password&raquo; field is not filled or filled incorrectly!", "Only the Latin characters and numbers are allowed in this field. The minimum password length is 8 characters. <br/> Check the correctness of filled field and try again.", 'error');
				} else {
					$_SESSION["JENSENCMS"]['client_msg'] = array("Ошибка: поле &laquo;Пароль&raquo; не заполнено или заполнено неправильно!", "Пароль должен состоять из символов латинского алфавита и цифр. Минимальная длина пароля 8 символов.<br />Проверьте правильность заполнения поля и попробуйте снова.", 'error');
				}
				return $json;
			}

			if( empty($d['jcms_clientPassw1']) || empty($d['jcms_clientPassw2']) || $d['jcms_clientPassw1'] !== $d['jcms_clientPassw2'] ){
				if( $_JCMS->lang == 'en' ){
					$_SESSION["JENSENCMS"]['client_msg'] = array("Error: The entered passwords do not match!", "Check the correctness of filling the fields and try again.", 'error');
				} else {
					$_SESSION["JENSENCMS"]['client_msg'] = array("Ошибка: введенные пароли не совпадают!", "Проверьте правильность заполнения полей и попробуйте снова.", 'error');
				}
				return $json;
			} else { $d['jcms_clientPassw'] = md5($d['jcms_clientPassw1']); }

			$sql_code = "SELECT * FROM `jcms2_moduleClients` WHERE `client_email` = '{$d[jcms_clientEmail]}' || `client_login` = '{$d[jcms_clientLogin]}' LIMIT 1";

			if( !$res = $_JCMS->db->query($sql_code) ){
				$_SESSION["JENSENCMS"]['client_msg'] = array("Ошибка базы данных! [php/module.cabinet#".__LINE__."]", $_JCMS->show_php_errors?("MYSQL ERROR #".$_JCMS->db->errno." - ".$_JCMS->db->error):'', 'error'); 
				return;
			}
			if( $res->num_rows == 1 ){
				$data = $res->fetch_assoc();
				if( $data['client_login'] == $d['jcms_clientLogin'] ){
					if( $_JCMS->lang == 'en' ){
						$_SESSION["JENSENCMS"]['client_msg'] = array("Error: The user with this login is already registered!", "Specify a different username!", 'error');
					} else {
						$_SESSION["JENSENCMS"]['client_msg'] = array("Ошибка: пользователь с таким логином уже зарегистрирован!", "Укажите другой логин!", 'error');
					}
				}
				if( $data['client_email'] == $d['jcms_clientEmail'] ){
					if( $_JCMS->lang == 'en' ){
						$_SESSION["JENSENCMS"]['client_msg'] = array("Error: User with this email address is already registered!", "Specify a different email address!", 'error');
					} else {
						$_SESSION["JENSENCMS"]['client_msg'] = array("Ошибка: пользователь с таким адресом эл. почты уже зарегистрирован!", "Укажите другой адрес эл. почты!", 'error');
					}
				}
				return;
			}

			$d['client_token'] = md5(mt_rand().mt_rand().mt_rand().uniqid()); // токен

			$client_useragent = $_JCMS->db->escape_string($_SERVER['HTTP_USER_AGENT']);
			$client_regIP = $_JCMS->db->escape_string($this->getUserIP());
			
			$double_id = $_JCMS->db->escape_string(intval($_COOKIE['jcms2_uid'])<1?$double_id=0:intval($_COOKIE['jcms2_uid']));
			$sqldouble_id = $double_id > 0 ?'\''.$double_id.'\'':'NULL';
			
			$sql_code = "INSERT INTO `jcms2_moduleClients`(`client_email`, `client_password`, `client_login`, `client_regDate`, `client_regIP`, `client_lastUserAgent`, `client_status`, `client_token`, `client_double_id`) VALUES ('{$d[jcms_clientEmail]}', '{$d[jcms_clientPassw]}', '{$d[jcms_clientLogin]}', CURRENT_TIMESTAMP, '{$client_regIP}', '{$client_useragent}', '0', '{$d[client_token]}', {$sqldouble_id})";
			if( !$res = $_JCMS->db->query($sql_code) ){
				$_SESSION["JENSENCMS"]['client_msg'] = array("Ошибка базы данных! [php/module.cabinet#".__LINE__."]", $_JCMS->show_php_errors?("MYSQL ERROR #".$_JCMS->db->errno." - ".$_JCMS->db->error):'', 'error'); 
				return;
			} else {
				$client_id = $_JCMS->db->insert_id;
				if( intval($_COOKIE['jcms2_uid']) < 1 ){
					setcookie('jcms2_uid', $client_id); // сохраняем в куках ид зареганного юзера
				}
				if( $_COOKIE['JCMS2_LK_REFERAL'] ){
					// есть реферал...
					$client_referalCode = $_JCMS->db->escape_string($_COOKIE['JCMS2_LK_REFERAL']);
					$sql_code = "SELECT * FROM `jcms2_moduleClients` WHERE `client_referalCode` = '{$client_referalCode}'";
					if( ($res = $_JCMS->db->query($sql_code)) && $res->num_rows == 1 ){ 
						$data2 = $res->fetch_assoc();
						$sql_code = "UPDATE `jcms2_moduleClients` SET `client_ref_id` = '{$data2['client_id']}' WHERE `client_id` = '{$client_id}'";
						$_JCMS->db->query($sql_code);
					}
				}
				$url = $_JCMS->getConfig('site_url').'/confirm?token='.$d['client_token'];
				if( $_JCMS->lang == 'en' ){
					$_SESSION["JENSENCMS"]['client_msg'] = array('You have successfully logged in.', 'We sent a link for account activation to your email address.', 'warning');
					if( !$t = $_JCMS->send_email($d['jcms_clientEmail'], "Account activation | ".$_SERVER['HTTP_HOST'], "<b>You have successfully registered on the site ".$_SERVER['HTTP_HOST']." </b><br/><br/> To activate the account, click on the following link: <a href=\"{$url}\">{$url}</a>")){
						$_SESSION["JENSENCMS"]['client_msg'][2] .= " The error of letter sending. ".$t;
					} else {
						return true;					
					}
				} else {
					$_SESSION["JENSENCMS"]['client_msg'] = array('Вы успешно зарегистрированы.', 'На Ваш адрес эл. почты отправлено письмо со ссылкой для активации аккаунта.', 'warning');
					if( !$t = $_JCMS->send_email($d['jcms_clientEmail'], "Активация аккаунта | ".$_SERVER['HTTP_HOST'], "<b>Вы успешно зарегистрированы на сайте ".$_SERVER['HTTP_HOST']."!</b><br /><br />Для активации аккаунта перейдите по ссылке: <a href=\"{$url}\">{$url}</a>")){
						$_SESSION["JENSENCMS"]['client_msg'][2] .= " Ошибка отправки письма. ".$t;
					} else {
						return true;					
					}					
				}
			}
			
			
			
		}
	}
	
	function checkAuthForm(){
		global $_JCMS;
		if( $_POST['client_form_submit'] == 1 ){
			$client_authToken = $_SESSION["JENSENCMS"]['client_authToken'];

			if( empty($client_authToken) || $client_authToken !== $_POST['jcms_clientToken'] || $this->checkFrod($_POST['jcms_clientLogin']) > 4 ){
				$_SESSION["JENSENCMS"]['client_msg'] = array("Hacking attempt!",'','error');
				return;					
			}
			$_POST['jcms_clientCaptcha'] = strtolower($_POST['jcms_clientCaptcha']);
			if( empty($_POST['jcms_clientCaptcha']) || $_SESSION["JENSENCMS"]['captcha'] != $_POST['jcms_clientCaptcha'] ){
				unset($_SESSION["JENSENCMS"]['captcha']);
				if( $_JCMS->lang == 'en' ){
					$_SESSION["JENSENCMS"]['client_msg'] = array("Error: Invalid security code from the image!",'','error');
				} else {
					$_SESSION["JENSENCMS"]['client_msg'] = array("Ошибка: неверный защитный код с картинки!",'','error');
				}
				return;													
			}
			
			$d['useremail'] = $_JCMS->db->escape_string($_POST['jcms_clientLogin']);
			setcookie('jcms2_cabinetLogin', $d['useremail']);
			$d['password'] = $_JCMS->db->escape_string(md5($_POST['jcms_clientPassword'])); // md5 хэш пароля
			if( empty($d['useremail']) || empty($d['password']) ){
				if( $_JCMS->lang == 'en' ){
					$_SESSION["JENSENCMS"]['client_msg'] = array("Error: Invalid username or password",'','error');
				} else {
					$_SESSION["JENSENCMS"]['client_msg'] = array("Ошибка: неверный логин или пароль",'','error');
				}
				return;									
			}
			
			$sql_code = "SELECT * FROM `jcms2_moduleClients` WHERE (`client_email` = '{$d[useremail]}' OR `client_login` = '{$d[useremail]}') AND `client_password` = '{$d[password]}'";
			if( !$res = $_JCMS->db->query($sql_code) ){
				$_SESSION["JENSENCMS"]['client_msg'] = array("Ошибка базы данных! [php/module.cabinet#".__LINE__."]", $_JCMS->show_php_errors?("MYSQL ERROR #".$_JCMS->db->errno." - ".$_JCMS->db->error):'', 'error'); 
				return;
			}
			if( $res->num_rows !== 1 ){
				$this->frodAdd($d['useremail']);
				if( $_JCMS->lang == 'en' ){
					$_SESSION["JENSENCMS"]['client_msg'] = array("Error: User with these data is not found!", "Check the data entered is correct and try again!",'error');
				} else {
					$_SESSION["JENSENCMS"]['client_msg'] = array("Ошибка: пользователь с указанным данными не найден!", "Проверьте правильность ввода данных и попробуйте снова!", 'error');
				}
				return;
			}
			unset($_SESSION["JENSENCMS"]['client_authToken']);
			$data = $res->fetch_assoc();
			
			// проверяем статус аккаунта
			if( $data['client_status'] != 1 ){
				if( $data['client_status'] == 2 ){
					if( $_JCMS->lang == 'en' ){
						$_SESSION["JENSENCMS"]['client_msg'] = array("Your account has been blocked!", "Refer to the site administrator to unlock it!",'error');
					} else {
						$_SESSION["JENSENCMS"]['client_msg'] = array("Ваш аккаунт заблокирован!", "Обратитесь к администратору сайта для разблокировки!", 'error');
					}
					return;
				}
				if( $data['client_status'] == 0 ){
					if( $_JCMS->lang == 'en' ){
						$_SESSION["JENSENCMS"]['client_msg'] = array("Your account is not activated!", "A letter has been sent to your email. Follow the link in that letter to activate your account.",'error');
					} else {
						$_SESSION["JENSENCMS"]['client_msg'] = array("Ваш аккаунт не активирован!", "На Ваш email было отправлено письмо. Перейдите по ссылке в письме для активации аккаунта.", 'warning');
					}
					$data['client_token'] = md5(mt_rand().mt_rand().mt_rand().uniqid()); // токен
					$sql_code = "UPDATE `jcms2_moduleClients` SET `client_token` = '{$data[client_token]}' WHERE (`client_email` = '{$d[useremail]}' OR `client_login` = '{$d[useremail]}') AND `client_password` = '{$data[client_password]}'";
					if( !$res = $_JCMS->db->query($sql_code) ){ return $_JCMS->message("Ошибка базы данных! [php/module.cabinet#".__LINE__."]", $_JCMS->show_php_errors?("MYSQL ERROR #".$_JCMS->db->errno." - ".$_JCMS->db->error):'', 'error'); }
					$url = $_JCMS->getConfig('site_url').'/confirm?token='.$data['client_token'];
					if( $_JCMS->lang == 'en' ){
						if( !$t = $_JCMS->send_email($data['client_email'], "Account activation | ".$_SERVER['HTTP_HOST'], "<b>You have successfully registered on the site ".$_SERVER['HTTP_HOST']."!</b> <br/><br/> To activate the account, click on the following link: <a href=\"{$url}\">{$url}<a/>") ){
							$_SESSION["JENSENCMS"]['client_msg'][2] .= " The error of letter sending. ".$t;
						}
					} else {
						if( !$t = $_JCMS->send_email($data['client_email'], "Активация аккаунта | ".$_SERVER['HTTP_HOST'], "<b>Вы успешно зарегистрированы на сайте ".$_SERVER['HTTP_HOST']."!</b><br /><br />Для активации аккаунта перейдите по ссылке: <a href=\"{$url}\">{$url}<a/>")){
							$_SESSION["JENSENCMS"]['client_msg'][2] .= " Ошибка отправки письма. ".$t;
						}
					}
					return;
				}				
			}			
			$data['client_token'] = md5(mt_rand().mt_rand().mt_rand().uniqid()); // токен
			$_SESSION["JENSENCMS"]['SITE_AUTH'] = $data;

			$client_lastUserAgent = $_JCMS->db->escape_string($_SERVER['HTTP_USER_AGENT']);
			$client_lastIP = $_JCMS->db->escape_string($this->getUserIP());
			
			// обновляем токен авторизации в базе
			$sql_code = "UPDATE `jcms2_moduleClients` SET `client_token` = '{$data[client_token]}', `client_lastIP` = '{$client_lastIP}', `client_lastUserAgent` = '$client_lastUserAgent', `client_lastAuthDate` = CURRENT_TIMESTAMP WHERE (`client_email` = '{$data[client_email]}') AND `client_password` = '{$data[client_password]}'";
			if( !$res = $_JCMS->db->query($sql_code) ){ return $_JCMS->message("Ошибка базы данных! [php/module.cabinet#".__LINE__."]", $_JCMS->show_php_errors?("MYSQL ERROR #".$_JCMS->db->errno." - ".$_JCMS->db->error):'', 'error'); }
			
			return $this->checkAuth();
		}
	}

	function checkConfirmForm(){
		global $_JCMS;
		if( $_POST['client_form_submit'] == 1 ){
			$_POST['jcms_clientCaptcha'] = strtolower($_POST['jcms_clientCaptcha']);
			if( empty($_POST['jcms_clientCaptcha']) || $_SESSION["JENSENCMS"]['captcha'] != $_POST['jcms_clientCaptcha'] ){
				unset($_SESSION["JENSENCMS"]['captcha']);
				if( $_JCMS->lang == 'en' ){
					$_SESSION["JENSENCMS"]['client_msg'] = array("Error: Invalid security code from the image!",'','error');
				} else {
					$_SESSION["JENSENCMS"]['client_msg'] = array("Ошибка: неверный защитный код с картинки!",'','error');
				}
				return;													
			}
			$d['token'] = $_JCMS->db->escape_string($_GET['token']);
			$d['password'] = $_JCMS->db->escape_string(md5($_POST['jcms_clientPassword'])); // md5 хэш пароля
			if( empty($d['token']) || empty($d['password']) ){
				if( $_JCMS->lang == 'en' ){
					$_SESSION["JENSENCMS"]['client_msg'] = array("Error: invalid token or password",'','error');
				} else {
					$_SESSION["JENSENCMS"]['client_msg'] = array("Ошибка: неверный токен или пароль",'','error');
				}
				return;									
			}
			$sql_code = "SELECT * FROM `jcms2_moduleClients` WHERE `client_token` = '{$d[token]}' AND `client_password` = '{$d[password]}' AND `client_status` = '0'";

			if( !$res = $_JCMS->db->query($sql_code) ){
				$_SESSION["JENSENCMS"]['client_msg'] = array("Ошибка базы данных! [php/module.cabinet#".__LINE__."]", $_JCMS->show_php_errors?("MYSQL ERROR #".$_JCMS->db->errno." - ".$_JCMS->db->error):'', 'error'); 
				return;
			}
			if( $res->num_rows !== 1 ){
				if( $_JCMS->lang == 'en' ){
					$_SESSION["JENSENCMS"]['client_msg'] = array("Error: invalid token or password",'','error');
				} else {
					$_SESSION["JENSENCMS"]['client_msg'] = array("Ошибка: неверный токен или пароль", "", 'error');
				}
				return;
			}
			$data = $res->fetch_assoc();
			
			$sql_code = "UPDATE `jcms2_moduleClients` SET `client_token` = '', `client_status` = '1' WHERE `client_token` = '{$d[token]}' AND `client_password` = '{$d[password]}' AND `client_status` = '0'";
			if( !$res = $_JCMS->db->query($sql_code) ){ return $_JCMS->message("Ошибка базы данных! [php/module.cabinet#".__LINE__."]", $_JCMS->show_php_errors?("MYSQL ERROR #".$_JCMS->db->errno." - ".$_JCMS->db->error):'', 'error'); }
			if( $_JCMS->lang == 'en' ){
				$_SESSION["JENSENCMS"]['client_msg'] = array("Account has been successfully activated!",'','error');
			} else {
				$_SESSION["JENSENCMS"]['client_msg'] = array('Аккаунт успешно активирован!', '', 'notice');
			}
			return true;					
		}
	}

	function checkConfirmResendForm(){
		global $_JCMS;
		if( $_POST['client_form_submit'] == 1 ){
			$_POST['jcms_clientCaptcha'] = strtolower($_POST['jcms_clientCaptcha']);
			if( empty($_POST['jcms_clientCaptcha']) || $_SESSION["JENSENCMS"]['captcha'] != $_POST['jcms_clientCaptcha'] ){
				unset($_SESSION["JENSENCMS"]['captcha']);
				if( $_JCMS->lang == 'en' ){
					$_SESSION["JENSENCMS"]['client_msg'] = array("Error: Invalid security code from the image!",'','error');
				} else {
					$_SESSION["JENSENCMS"]['client_msg'] = array("Ошибка: неверный защитный код с картинки!",'','error');
				}
				return;													
			}

			if( empty($_POST['jcms_clientEmail']) || !filter_var($_POST['jcms_clientEmail'], FILTER_VALIDATE_EMAIL) ){
				if( $_JCMS->lang == 'en' ){
					$_SESSION["JENSENCMS"]['client_msg'] = array("Error: Field &laquo;E-mail address&raquo; is not filled or filled incorrectly!", "Check the correctness of the filled fields and try again.", 'error');
				} else {
					$_SESSION["JENSENCMS"]['client_msg'] = array("Ошибка: поле &laquo;Адрес эл. почты&raquo; не заполнено или заполнено неправильно!", "Проверьте правильность заполнения поля и попробуйте снова.", 'error');
				}
				return $json;
			}
			
			$d['email'] = $_JCMS->db->escape_string($_POST['jcms_clientEmail']);
			
			$sql_code = "SELECT * FROM `jcms2_moduleClients` WHERE `client_email` = '{$d[email]}' AND `client_status` = '0'";

			if( !$res = $_JCMS->db->query($sql_code) ){
				$_SESSION["JENSENCMS"]['client_msg'] = array("Ошибка базы данных! [php/module.cabinet#".__LINE__."]", $_JCMS->show_php_errors?("MYSQL ERROR #".$_JCMS->db->errno." - ".$_JCMS->db->error):'', 'error'); 
				return;
			}
			if( $res->num_rows !== 1 ){
				if( $_JCMS->lang == 'en' ){
					$_SESSION["JENSENCMS"]['client_msg'] = array("Error: Invalid email or account is already activated",'','error');
				} else {
					$_SESSION["JENSENCMS"]['client_msg'] = array("Ошибка: неверный email или аккаунт уже активирован", "", 'error');
				}
				return;
			}
			$data = $res->fetch_assoc(); 
			if( strtotime($data['client_lastSendEmail']) > 0 && strtotime($data['client_lastSendEmail']) > time()-60*60 ){
				if( $_JCMS->lang == 'en' ){
					$_SESSION["JENSENCMS"]['client_msg'] = array("You have recently resubmitted a letter. You can try again a little later ...",'','error');
				} else {
					$_SESSION["JENSENCMS"]['client_msg'] = array("Недавно вам уже было повторно отправлено письмо.  Повторить попытку можно немного позже...", "", 'error');
				}
				return;
			}

			$d['client_token'] = md5(mt_rand().mt_rand().mt_rand().uniqid()); // токен
			
			$sql_code = "UPDATE `jcms2_moduleClients` SET `client_token` = '{$d['client_token']}', `client_lastSendEmail` = CURRENT_TIMESTAMP WHERE `client_email` = '{$d[email]}'  AND `client_status` = '0'";
			if( !$res = $_JCMS->db->query($sql_code) ){ return $_JCMS->message("Ошибка базы данных! [php/module.cabinet#".__LINE__."]", $_JCMS->show_php_errors?("MYSQL ERROR #".$_JCMS->db->errno." - ".$_JCMS->db->error):'', 'error'); }
			
			$url = $_JCMS->getConfig('site_url').'/confirm?token='.$d['client_token'];
			if( $_JCMS->lang == 'en' ){
				$_SESSION["JENSENCMS"]['client_msg'] = array('You have successfully logged in.', 'We sent a link for account activation to your email address.', 'warning');
				if( !$t = $_JCMS->send_email($d['email'], "Account activation | ".$_SERVER['HTTP_HOST'], "<b>You have successfully registered on the site ".$_SERVER['HTTP_HOST']." </b><br/><br/> To activate the account, click on the following link: <a href=\"{$url}\">{$url}</a>")){
					$_SESSION["JENSENCMS"]['client_msg'][2] .= " The error of letter sending. ".$t;
				} else {
					return true;					 
				} 
			} else {
				$_SESSION["JENSENCMS"]['client_msg'] = array('Вы успешно зарегистрированы.', 'На Ваш адрес эл. почты отправлено письмо со ссылкой для активации аккаунта.', 'warning');
				if( !$t = $_JCMS->send_email($d['email'], "Активация аккаунта | ".$_SERVER['HTTP_HOST'], "<b>Вы успешно зарегистрированы на сайте ".$_SERVER['HTTP_HOST']."!</b><br /><br />Для активации аккаунта перейдите по ссылке: <a href=\"{$url}\">{$url}</a>")){
					$_SESSION["JENSENCMS"]['client_msg'][2] .= " Ошибка отправки письма. ".$t;
					return false;					
				} else {
					return true;					
				}					
			}

			return true;					
		}
	}

	function checkLostPasswordForm_sendEmail(){
		global $_JCMS;
		if( $_POST['client_form_submit'] == 1 ){
			$_POST['jcms_clientCaptcha'] = strtolower($_POST['jcms_clientCaptcha']);
			if( empty($_POST['jcms_clientCaptcha']) || $_SESSION["JENSENCMS"]['captcha'] != $_POST['jcms_clientCaptcha'] ){
				unset($_SESSION["JENSENCMS"]['captcha']);
				if( $_JCMS->lang == 'en' ){
					$_SESSION["JENSENCMS"]['client_msg'] = array("Error: Invalid security code from the image!",'','error');
				} else {
					$_SESSION["JENSENCMS"]['client_msg'] = array("Ошибка: неверный защитный код с картинки!",'','error');
				}
				return;													
			}
			$d['jcms_clientName'] = $_JCMS->db->escape_string($_POST['jcms_clientName']);
			if( empty($d['jcms_clientName']) ){
				if( $_JCMS->lang == 'en' ){
					$_SESSION["JENSENCMS"]['client_msg'] = array("Error: invalid email",'','error');
				} else {
					$_SESSION["JENSENCMS"]['client_msg'] = array("Ошибка: неверный email",'','error');
				}
				return;									
			}
			$sql_code = "SELECT * FROM `jcms2_moduleClients` WHERE `client_email` = '{$d[jcms_clientName]}'";

			if( !$res = $_JCMS->db->query($sql_code) ){
				$_SESSION["JENSENCMS"]['client_msg'] = array("Ошибка базы данных! [php/module.cabinet#".__LINE__."]", $_JCMS->show_php_errors?("MYSQL ERROR #".$_JCMS->db->errno." - ".$_JCMS->db->error):'', 'error'); 
				return;
			}
			if( $res->num_rows !== 1 ){
				if( $_JCMS->lang == 'en' ){
					$_SESSION["JENSENCMS"]['client_msg'] = array("Error: invalid email",'','error');
				} else {
					$_SESSION["JENSENCMS"]['client_msg'] = array("Ошибка: неверный email",'','error');
				}
				return;
			}
			$data = $res->fetch_assoc();
			
			$data['client_token'] = md5(mt_rand().mt_rand().mt_rand().uniqid()); // токен
			$sql_code = "UPDATE `jcms2_moduleClients` SET `client_token` = '{$data[client_token]}' WHERE `client_id` = '{$data[client_id]}'";
			if( !$res = $_JCMS->db->query($sql_code) ){ return $_JCMS->message("Ошибка базы данных! [php/module.cabinet#".__LINE__."]", $_JCMS->show_php_errors?("MYSQL ERROR #".$_JCMS->db->errno." - ".$_JCMS->db->error):'', 'error'); }
			$url = $_JCMS->getConfig('site_url').'/lost_password?token='.$data['client_token'];
			if( $_JCMS->lang == 'en' ){
				$_SESSION["JENSENCMS"]['client_msg'] = array("A letter has been sent to your email", "Click on the link in the letter to reset the account password.", 'warning');
				if( !$t = $_JCMS->send_email($data['client_email'], "Password recovery | ".$_SERVER['HTTP_HOST'], "<b>To reset the account password, please follow a link:</b> <a href=\"{$url}\">{$url}<a/>")){
					$_SESSION["JENSENCMS"]['client_msg'][2] .= " The error of letter sending.".$t;	
				}
			} else {
				$_SESSION["JENSENCMS"]['client_msg'] = array("На Ваш email было отправлено письмо.","Перейдите по ссылке в письме для сброса пароля аккаунта.", 'warning');
				if( !$t = $_JCMS->send_email($data['client_email'], "Восстановление пароля | ".$_SERVER['HTTP_HOST'], "<b>Для сброса пароля аккаунта перейдите по ссылке:</b> <a href=\"{$url}\">{$url}<a/>")){
					$_SESSION["JENSENCMS"]['client_msg'][2] .= " Ошибка отправки письма.".$t;
	
				}
			}
			return true;
		}
	}

	function checkLostPasswordForm_changePassword(){
		global $_JCMS;
		if( $_POST['client_form_submit'] == 2 ){
			$d['client_token'] = $_JCMS->db->escape_string($_GET['token']);
			$d['jcms_clientPassw1'] = $_JCMS->db->escape_string($_POST['jcms_clientPassw1']);
			$d['jcms_clientPassw2'] = $_JCMS->db->escape_string($_POST['jcms_clientPassw2']);
			$_POST['jcms_clientCaptcha'] = strtolower($_POST['jcms_clientCaptcha']);
			if( empty($_POST['jcms_clientCaptcha']) || $_SESSION["JENSENCMS"]['captcha'] != $_POST['jcms_clientCaptcha'] ){
				unset($_SESSION["JENSENCMS"]['captcha']);
				if( $_JCMS->lang == 'en' ){
					$_SESSION["JENSENCMS"]['client_msg'] = array("Error: Invalid security code from the image!",'','error');
				} else {
					$_SESSION["JENSENCMS"]['client_msg'] = array("Ошибка: неверный защитный код с картинки!",'','error');
				}
				return;													
			}
			if( empty($d['client_token']) ){
				if( $_JCMS->lang == 'en' ){
					$_SESSION["JENSENCMS"]['client_msg'] = array("Error: invalid token!",'','error');
				} else {
					$_SESSION["JENSENCMS"]['client_msg'] = array("Ошибка: неверный токен!", "", 'error');
				}
				return $json;
			}
			
			if( empty($d['jcms_clientPassw1']) || !preg_match("/[a-z0-9]/i", $d['jcms_clientPassw1']) || mb_strlen($d['jcms_clientPassw1']) < 8 ){
				if( $_JCMS->lang == 'en' ){
					$_SESSION["JENSENCMS"]['client_msg'] = array("Error: &laquo;Password&raquo; field is not filled or filled incorrectly!", "Only the Latin characters and numbers are allowed in this field. The minimum password length is 8 characters.<br/>Check the correctness of filling the field and try again.", 'error');
				} else {
					$_SESSION["JENSENCMS"]['client_msg'] = array("Ошибка: поле &laquo;Пароль&raquo; не заполнено или заполнено неправильно!", "Пароль должен состоять из символов латинского алфавита и цифр. Минимальная длина пароля 8 символов.<br />Проверьте правильность заполнения поля и попробуйте снова.", 'error');
				}
				return $json;
			}

			if( empty($d['jcms_clientPassw1']) || empty($d['jcms_clientPassw2']) || $d['jcms_clientPassw1'] !== $d['jcms_clientPassw2'] ){
				if( $_JCMS->lang == 'en' ){
					$_SESSION["JENSENCMS"]['client_msg'] = array("Error: The entered passwords do not match!", "Check the correctness of filled fields and try again.", 'error');
				} else {
					$_SESSION["JENSENCMS"]['client_msg'] = array("Ошибка: введенные пароли не совпадают!", "Проверьте правильность заполнения полей и попробуйте снова.", 'error');
				}
				return $json;
			} else { $d['jcms_clientPassw'] = md5($d['jcms_clientPassw1']); }


			$sql_code = "SELECT * FROM `jcms2_moduleClients` WHERE `client_token` = '{$d[client_token]}'";

			if( !$res = $_JCMS->db->query($sql_code) ){
				$_SESSION["JENSENCMS"]['client_msg'] = array("Ошибка базы данных! [php/module.cabinet#".__LINE__."]", $_JCMS->show_php_errors?("MYSQL ERROR #".$_JCMS->db->errno." - ".$_JCMS->db->error):'', 'error'); 
				return;
			}
			if( $res->num_rows !== 1 ){
				if( $_JCMS->lang == 'en' ){
					$_SESSION["JENSENCMS"]['client_msg'] = array("Error: invalid token!",'','error');
				} else {
					$_SESSION["JENSENCMS"]['client_msg'] = array("Ошибка: неверный токен!", "", 'error');
				}
				return $json;
			}
			$data = $res->fetch_assoc();
			
			$sql_code = "UPDATE `jcms2_moduleClients` SET `client_token` = '', `client_password` = '{$d[jcms_clientPassw]}' WHERE `client_id` = '{$data[client_id]}'";
			if( !$res = $_JCMS->db->query($sql_code) ){ return $_JCMS->message("Ошибка базы данных! [php/module.cabinet#".__LINE__."]", $_JCMS->show_php_errors?("MYSQL ERROR #".$_JCMS->db->errno." - ".$_JCMS->db->error):'', 'error'); }

			if( $_JCMS->lang == 'en' ){
				$_SESSION["JENSENCMS"]['client_msg'] = array("Changing the password is successful!", "Now you can log in with your new password.", 'warning');
				if( !$t = $_JCMS->send_email($data['client_email'], "Account password change | ". $_SERVER['HTTP_HOST'], "<b> Password changed successfully!<b><br/><br/><p> To authenticate, use the following data:! </p><b> login:</b>{$client_email}<br/><b>password".($client_login).": </b>{$jcms_clientPassw}")){
					$_SESSION["JENSENCMS"]['client_msg'][2] .= " The error of letter sending.".$t;
	
				}
			} else {
				$_SESSION["JENSENCMS"]['client_msg'] = array("Изменение пароля успешно выполнено!","Теперь вы можете авторизоваться с новым паролем.", 'warning');
				if( !$t = $_JCMS->send_email($data['client_email'], "Изменение пароля аккаунта | ".$_SERVER['HTTP_HOST'], "<b>Изменение пароля успешно выполнено!</b><br /><br /><p>Для авторизации используйте следующие данные:</p><b>логин:</b> {$data[client_email]}".($profile['client_login']?" или {$profile[client_email]}":"")."<br /><b>пароль:</b> {$d[jcms_clientPassw1]}")){
					$_SESSION["JENSENCMS"]['client_msg'][2] .= " Ошибка отправки письма.".$t;
	
				}
			}
			
			return true;
		}
	} 

	function checkForm_changeProfile(){
		global $_JCMS;
		if( $_POST['form_submit'] == 1 ){
			$d['jcms2_lk_passw1'] = $_JCMS->db->escape_string($_POST['jcms2_lk_passw1']);
			$d['jcms2_lk_passw2'] = $_JCMS->db->escape_string($_POST['jcms2_lk_passw2']);
					
			if( !empty($d['jcms2_lk_passw1']) && !empty($d['jcms2_lk_passw2']) ){
				if( empty($d['jcms2_lk_passw1']) || !preg_match("/[a-z0-9]/i", $d['jcms2_lk_passw1']) || mb_strlen($d['jcms2_lk_passw1']) < 8 ){
					if( $_JCMS->lang == 'en' ){
						$_SESSION["JENSENCMS"]['client_msg'] = array("Error: &laquo;Password&raquo; field is not filled or filled incorrectly!", "Only the Latin characters and numbers are allowed in this field. The minimum password length is 8 characters.<br/>Check the correctness of filling the field and try again.", 'error');
					} else {
						$_SESSION["JENSENCMS"]['client_msg'] = array("Ошибка: поле &laquo;Пароль&raquo; не заполнено или заполнено неправильно!", "Пароль должен состоять из символов латинского алфавита и цифр. Минимальная длина пароля 8 символов.<br />Проверьте правильность заполнения поля и попробуйте снова.", 'error');
					}
				
					return $json;
				}
		
				if( empty($d['jcms2_lk_passw1']) || empty($d['jcms2_lk_passw2']) || $d['jcms2_lk_passw1'] !== $d['jcms2_lk_passw2'] ){
					if( $_JCMS->lang == 'en' ){
						$_SESSION["JENSENCMS"]['client_msg'] = array("Error: The entered passwords do not match!", "Check the correctness of filled fields and try again.", 'error');
					} else {
						$_SESSION["JENSENCMS"]['client_msg'] = array("Ошибка: введенные пароли не совпадают!", "Проверьте правильность заполнения полей и попробуйте снова.", 'error');
					}
				} else { $d['jcms_clientPassw'] = md5($d['jcms2_lk_passw1']); }
			} else return;// пароль не указан, игнориуем...
			
			$profile = $this->getProfile();
			$sql_code = "UPDATE `jcms2_moduleClients` SET `client_password` = '{$d[jcms_clientPassw]}' WHERE `client_id` = '{$profile[client_id]}'";
			
			if( !$res = $_JCMS->db->query($sql_code) ){ return $_JCMS->message("Ошибка базы данных! [php/module.cabinet#".__LINE__."]", $_JCMS->show_php_errors?("MYSQL ERROR #".$_JCMS->db->errno." - ".$_JCMS->db->error):'', 'error'); }
			
			if( $d['jcms_clientPassw'] ){
				$this->logout();
				if( $_JCMS->lang == 'en' ){
					$_SESSION["JENSENCMS"]['client_msg'] = array("Changing the password is successful!", "Now you can log in with your new password.", 'warning');
					if( !$t = $_JCMS->send_email($data['client_email'], "Account password change | ". $_SERVER['HTTP_HOST'], "<b> Password changed successfully!<b><br/><br/><p> To authenticate, use the following data:! </p><b> login:</b>{$profile[client_email]}".($profile['client_login']?" or {$profile[client_email]}":"")."<br/><b>password: </b>{$jcms2_lk_passw1}")){
						$_SESSION["JENSENCMS"]['client_msg'][2] .= " The error of letter sending.".$t;
		
					}
				} else {
					$_SESSION["JENSENCMS"]['client_msg'] = array("Изменение профиля успешно выполнено!","Необходимо авторизоваться с новым паролем.", 'warning');
					if( !$t = $_JCMS->send_email($profile['client_email'], "Изменение пароля аккаунта | ".$_SERVER['HTTP_HOST'], "<b>Изменение пароля успешно выполнено!</b><br /><br /><p>Для авторизации используйте следующие данные:</p><b>логин:</b> {$profile[client_email]}".($profile['client_login']?" или {$profile[client_email]}":"")."<br /><b>пароль:</b> {$d[jcms2_lk_passw1]}")){
						$_SESSION["JENSENCMS"]['client_msg'][2] .= " Ошибка отправки письма.".$t;
		
					}				
				}
			}
			
			return true;
		}
	}
	
	// пополнение баланса
	function checkForm_balancePayment(){
		global $_JCMS;
 
/*
			if( $_JCMS->lang == 'en' ){
				$_SESSION["JENSENCMS"]['client_msg'] = array("Payment via the website temporarily disabled!", 'At this point, you can top up your balance by the operator on the below mentioned contacts:<br>Telegram: @wingateproxy<br>Skype: socks5center<br>Jabber: socks5center@jabber.ru ','warning'); 
			} else {
				$_SESSION["JENSENCMS"]['client_msg'] = array("Оплата через сайт временно отключена!", 'На данный момент пополнить баланс можно через оператора по ниже указанным контактам:<br>Telegram: @wingateproxy<br>Skype: socks5center<br>Jabber: socks5center@jabber.ru ','warning'); 
				}
			header("Location: ".$_JCMS->getConfig('site_url').'/balance');
			exit();
*/

		$p_type = $_POST['type'];
		
		$profile = $this->getProfile();
		$client_id = $profile['client_id'];
		$invoice_id=rand(111111,999999);
		$out_summ = floatval($_POST['summ']);
		
		if( $_JCMS->lang == 'en' ){
			$inv_desc = "Refill the balance on the WinGate service. id='".$client_id."', login='".$profile['client_login']."'";
		} else {
			$inv_desc = "Пополнение баланса на сервисе WinGate. id='".$client_id."', логин='".$profile['client_login']."'";		
		}
		
		if ($p_type == 2) {
            $order_id = $invoice_id . '_' . $client_id;

            /*if ($out_summ > 25) {
                $out_summ = 25;
            }*/

            $amount = number_format($out_summ, 2, '.', '');

            $url = "https://cryptoscan.one/api/v1/invoice/widget";

            $curl = curl_init($url);
            curl_setopt($curl, CURLOPT_URL, $url);
            curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);

            $headers = array(
                "private-key: private_Uuu4haL0q4OBgS9FfFXdxIvYn-xXl_2j1Z6X4aEJ_lbLQeGVKEv222s_5lwzNuaj6BEN6R09T25aZK24VaiSRpFJJW0-iOleQgQbM9ZbIiw4ZIRtziHmrhdp",
                "public-key: public_1BZ03U4qfvBhS3zZQDMeq5Y8XXYd-a61_bPUUeDDp76kEhyAscG2ir_9g",
                "Accept: application/json",
                "Content-Type:application/json"
            );
            curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
            curl_setopt($curl, CURLOPT_HEADER, false);
            curl_setopt($curl, CURLOPT_POST, 1);

            $data = array(
                'amount'  => $amount,
                'client_reference_id' => $order_id,
                'metadata' => $profile['client_login'],
            );

            curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($data));

            $response = curl_exec($curl);
            curl_close($curl);
            $response = json_decode($response, 1);

            if ($response['success'] == 1) {
                header("Location: " . $response['data']['widget_url']);
                exit();
            }
            else {
                header("Location: http://cabinet.wingate.me/balance");
                exit();
            }
		}

        if ($p_type == 3) {
            $order_id = $invoice_id . '_' . $client_id;

            $merchant_id = 'Qccg7DlWJxCo3CAY';
            $apikey = 'eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJ1dWlkIjoiTVRZNE56VT0iLCJ0eXBlIjoicHJvamVjdCIsInYiOiI2YTlhYmNjN2Y0NzQ2YjQ2YmU0YmZlZDJlN2NmODA4OTE5ZGVlZDlmNzAwZWFkNWQ3OWVhMzZmYWE5YWI4N2NlIiwiZXhwIjo4ODA5OTc4MzU5NH0.ycBUD4l78C40IWHEJW-SSl-ogXP0CVRAMwy1mn1MeQg';
            $amount = number_format($out_summ, 2, '.', '');
            $desc = $inv_desc;

            $data_request = array(
                'shop_id'	=> $merchant_id,
                'amount'	=> $amount,
                'currency'	=> 'USD',
                'order_id'	=> $order_id
            );

            $curl = curl_init();

            curl_setopt($curl, CURLOPT_POST, 1);
            curl_setopt($curl, CURLOPT_POSTFIELDS, $data_request);

            $headers = array();
            $headers[] = "Authorization: Token " . $apikey;

            curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
            curl_setopt($curl, CURLOPT_URL, 'https://api.cryptocloud.plus/v1/invoice/create');
            curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);

            $result = curl_exec($curl);

            curl_close($curl);

            $json_data = json_decode($result, true);

            $this->data['url'] = $json_data['pay_url'];

            header("Location: " . $json_data['pay_url']);
            exit();
        }

        if ($p_type == 4) {
            $api_key = 'HQx7SC8X0O6JeGs7ROZaqvfgJzDNTZ6nzzpWN8ju2lSC90xnez4rfGfkvMMMqOZDEdPpzHWbYRnFnYoszt8AMwiU7rRz4nS4Zy9sCHWFd7A1cTAGxQ53ePIpbXzUJwbS';
            $merchant = 'aeb449bb-192c-4b44-a104-6b6218908323';

            $order_id = $invoice_id . '_' . $client_id;

            $amount = number_format($out_summ, 2, '.', '');

            $data['amount'] = $amount;
            $data['subtract'] = 100;
            $data['currency'] = 'USD';
            $data['order_id'] = $order_id;
            $data['url_callback'] = 'https://cabinet.wingate.me/payment_cryptomus.php';

            $sign = md5(base64_encode(json_encode($data)) . $api_key);

            $url = "https://api.cryptomus.com/v1/payment";

            $curl = curl_init($url);
            curl_setopt($curl, CURLOPT_URL, $url);
            curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);

            $headers = array(
                "merchant: " . $merchant,
                "sign: " . $sign,
                "Content-Type:application/json"
            );
            curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
            curl_setopt($curl, CURLOPT_POST, 1);
            curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($data));

            $response = curl_exec($curl);
            curl_close($curl);
            $response = json_decode($response, 1);

            if (isset($response['result'])) {
                header("Location: " . $response['result']['url']);
                exit();
            }
            else {
                header("Location: http://cabinet.wingate.me/balance");
                exit();
            }
        }
 		
		$mrh_login = "29622";
		$MERCHANT_ID = '29622';
		$mrh_pass1 = "wkkqw52w";
		//$SECRET_WORD   = 'iSu5qVMs9jh5Wsotvuilj48I2FyzIncX';
		$SECRET_WORD   = '214be1e2b297e72c2ec99c38f7b2c60af09d35c6';
		

		if( $out_summ < 1 ) $out_summ = 1;
		
		//$crc = md5("$mrh_login:$out_summ:$invoice_id:$mrh_pass1:Shp_client_id=$client_id");
		$crc = md5($mrh_login.':'.$out_summ.':'.$SECRET_WORD.':'.$invoice_id);

		//$url = 'https://www.free-kassa.ru/merchant/cash.php';
		$url = 'https://enot.io/pay';
		//$url = $url."?MrchLogin=$mrh_login&OutSum=$out_summ&InvId=$invoice_id&SignatureValue=$crc&Shp_client_id=$client_id&Desc=$inv_desc";
		$sign = md5($MERCHANT_ID.':'.$out_summ.':'.$SECRET_WORD.':'.$invoice_id);

		$cf = ['client'=>$client_id,'pay_type'=>$inv_desc];

		$url = $url."?m=$MERCHANT_ID&oa=$out_summ&o=$invoice_id&s=$sign&cr=USD&c=$inv_desc&cf=$client_id&success_url=http://cabinet.wingate.me/balance/payment_success&fail_url=http://cabinet.wingate.me/balance/payment_fail";
		
		/*header("Location: {$url}");
		exit($crc);*/

        //код для обновленного платежа
        $api_url = 'https://api.enot.io/invoice/create';

        $client_data = ['cliend_id' => $client_id];

        $args = array(
            'shop_id' => '0d6f292b-8d9b-47e9-bfd5-4c8798184361',
            'order_id' => '"' . $invoice_id . '_' . $client_id . '"',
            'amount' => $out_summ,
            'hook_url' => 'http://cabinet.wingate.me/balance/payment_freekassa',
            'custom_fields' => json_encode($client_data),
        );

        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $api_url);
        curl_setopt($curl, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($curl, CURLOPT_POST, true);
        curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($args));
        curl_setopt($curl, CURLOPT_HTTPHEADER, array(
            'Content-Type: application/json', 'x-api-key:' . $SECRET_WORD
        ));

        $out = curl_exec($curl);

        $json = json_decode($out, 1);

        curl_close($curl);

        header("Location: " . $json['data']['url']);
        exit();
	}

    function action_payment_cryptocloud_webhook() {
        global $_JCMS;

        $request_body = file_get_contents('php://input');

        /*if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            file_put_contents(dirname(__FILE__) . '/payments.log.txt', date('Y-m-d H:i:s') . ' - callback # crypto: Invalid method.' . "\r\n", FILE_APPEND);
            http_response_code(405); // Method Not Allowed
            exit;
        }*/

        file_put_contents(dirname(__FILE__). '/payments.log.txt', date('Y-m-d H:i:s') . " - callback # crypto: " . $request_body . "\r\n", FILE_APPEND);

        $params = [];
        parse_str($request_body, $params);

        //file_put_contents(dirname(__FILE__). '/payments.log.txt', date('Y-m-d H:i:s') . " - callback # crypto: " . var_dump($params) . "\r\n", FILE_APPEND);

        //$order_id = $request_body['order_id'];

        $out_summ = $params['amount_crypto'];
        $invoice_id = $params['order_id'];
        $c_invoice_id = $params['invoice_id'];
        $tmp = explode('_', $invoice_id);
        $order_id = $tmp[0];
        $order_id = str_replace('"', '', $order_id);
        $client_id = $tmp[1];
        $client_id = str_replace('"', '', $client_id);

        file_put_contents(dirname(__FILE__). '/payments.log.txt', date('Y-m-d H:i:s') . ' - invoice_id crypto - ' . $invoice_id . "\r\n", FILE_APPEND);
        file_put_contents(dirname(__FILE__). '/payments.log.txt', date('Y-m-d H:i:s') . ' - sum crypto - ' . $out_summ . "\r\n", FILE_APPEND);
        file_put_contents(dirname(__FILE__). '/payments.log.txt', date('Y-m-d H:i:s') . ' - c_invoice_id crypto - ' . $c_invoice_id . "\r\n", FILE_APPEND);

        if (!$order_id) {
            file_put_contents(dirname(__FILE__). '/payments.log.txt', date('Y-m-d H:i:s') . ' - callback # crypto: not order_id.' . "\r\n", FILE_APPEND);
            http_response_code(406);
            exit;
        }

        $status = $params['status'];

        if ($status != 'success') {
            file_put_contents(dirname(__FILE__). '/payments.log.txt', date('Y-m-d H:i:s') . "callback # crypto: unsuccessful status for order $order_id - $status \r\n", FILE_APPEND);
            exit;
        }

        file_put_contents(dirname(__FILE__). '/payments.log.txt', date('Y-m-d H:i:s') . ' - clien_id crypto - ' . $order_id . ' - ' . $client_id . "\r\n", FILE_APPEND);

        //$client_id = $_JCMS->db->escape_string($client_id);
        $res = $_JCMS->db->query("SELECT * FROM `jcms2_moduleClients` WHERE `client_id` = '{$client_id}'");

        //file_put_contents(dirname(__FILE__). '/payments.log.txt', date('Y-m-d H:i:s') . " - callback # enot: " . "SELECT * FROM `jcms2_moduleClients` WHERE `client_id` = '{$client_id}'" . "\r\n", FILE_APPEND);

        if( $res == false ){ echo "database err1\n"; return; }
        if($res->num_rows == 1 ){
            $data = $res->fetch_assoc();
            $out_summ = floatval($out_summ);
            $sql_code = "INSERT INTO `jcms2_moduleClientsBalance`(`client_id`, `summ`, `descr`) VALUES ('{$client_id}', '{$out_summ}', 'Пополнение через Crypto <i>(транзакция №{$c_invoice_id})</i>')";
            $res = $_JCMS->db->query($sql_code);
            if( $res == false ){ echo "database err3\n"; return; }

            /* --- НАЧИСЛЕНИЕ ПО РЕФ ПРОГРАММЕ --- */
            // проверяем есть ли реферал у этого юзера....
            $res = $_JCMS->db->query("SELECT * FROM `jcms2_moduleClients` WHERE `client_id` = '{$client_id}' AND `client_ref_id` IS NOT NULL");
            if( $res == false ){ echo "database err4\n"; return; }
            if($res->num_rows == 1 ){
                // есть реферал...
                $data = $res->fetch_assoc();
                $referals = $_JCMS->getConfig('referals');
                $rate = 0; // ставка по умолчанию...

                // определяем текущую партнерскую ставку...
                $res = $_JCMS->db->query("SELECT * FROM `jcms2_moduleClients` WHERE `client_id` = '{$data['client_ref_id']}' AND `client_ref_rate` IS NOT NULL");
                if( $res == false ){ echo "database err5\n"; return; }
                if($res->num_rows == 1 ){
                    // у клиента специальная партнерская ставка
                    $data2 = $res->fetch_assoc();
                    $rate = $data2['client_ref_rate'];
                } else {
                    // подбираем подходящую ставку...
                    if( is_array($referals['rates']) ){
                        ksort($referals['rates']); // сортиурем по возрастанию...
                        // определяем подходящую сумме ставку...
                        foreach($referals['rates'] as $summ=>$_rate){
                            if( $out_summ >= $summ ) $rate = $_rate;
                        }
                    }
                }
                $ref_summ = floatval($out_summ*$rate/100); // партнерское вознаграждение
                if( $referals['mode'] == 0 ){
                    // выплаты разово
                    // проверяем были ли уже выплаты по этмоу рефералу?
                    $res = $_JCMS->db->query("SELECT * FROM `jcms2_moduleClientsBalance` WHERE `client_id` = '{$data['client_ref_id']}' AND `ref_id` = '{$client_id}'");
                    if( $res == false ){ echo "database err6\n"; return; }
                    if($res->num_rows == 0 ){
                        // выплат еще не было...
                        $referals['mode'] = 1; // разрешаем проведение выплаты
                    }
                }
                if( $referals['mode'] == 1 ){
                    // выплаты постоянно
                    // делаем выплату
                    $sql_code = "INSERT INTO `jcms2_moduleClientsBalance`(`client_id`, `summ`, `descr`, `ref_id`) VALUES ('{$data['client_ref_id']}', '{$ref_summ}', 'Партнёрское вознаграждение за приведённого пользователя <i>{$this->hideStr($data['client_login'])}</i> ({$rate}%)', '{$client_id}')";
                    $res = $_JCMS->db->query($sql_code);
                    if( $res == false ){ echo "database err7\n"; return; }
                }
            }
            /* ---------------------------------------- */

            echo "OK";
            return;
        } else {
            echo "client_not_found";
            return;
        }
    }

	function action_payment_cryptocloud() {
		global $_JCMS;

        $request_body = file_get_contents('php://input');

        /*if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            file_put_contents(dirname(__FILE__) . '/payments.log.txt', date('Y-m-d H:i:s') . ' - callback # crypto: Invalid method.' . "\r\n", FILE_APPEND);
            http_response_code(405); // Method Not Allowed
            exit;
        }*/
		
		//file_put_contents(dirname(__FILE__). '/payments.log.txt', date('Y-m-d H:i:s') . " - callback # crypto: " . $request_body . "\r\n", FILE_APPEND);
        file_put_contents(dirname(__FILE__). '/payments.log.txt', date('Y-m-d H:i:s') . " - callback # crypto: INPUT\r\n", FILE_APPEND);

        $params = [];
        $params = json_decode($request_body, 1);

		//file_put_contents(dirname(__FILE__). '/payments.log.txt', date('Y-m-d H:i:s') . " - callback # crypto: " . var_dump($params) . "\r\n", FILE_APPEND);

        //$order_id = $request_body['order_id'];

        $out_summ = $params['data']['requested_amount'];
        $invoice_id = $params['data']['client_reference_id'];
		$c_invoice_id = $params['data']['transaction_id'];
        $tmp = explode('_', $invoice_id);
        $order_id = $tmp[0];
        $order_id = str_replace('"', '', $order_id);
        $client_id = $tmp[1];
        $client_id = str_replace('"', '', $client_id);

        file_put_contents(dirname(__FILE__). '/payments.log.txt', date('Y-m-d H:i:s') . ' - invoice_id crypto - ' . $invoice_id . "\r\n", FILE_APPEND);
        file_put_contents(dirname(__FILE__). '/payments.log.txt', date('Y-m-d H:i:s') . ' - sum crypto - ' . $out_summ . "\r\n", FILE_APPEND);
        file_put_contents(dirname(__FILE__). '/payments.log.txt', date('Y-m-d H:i:s') . ' - c_invoice_id crypto - ' . $c_invoice_id . "\r\n", FILE_APPEND);
		
        if (!$order_id) {
            file_put_contents(dirname(__FILE__). '/payments.log.txt', date('Y-m-d H:i:s') . ' - callback # crypto: not order_id.' . "\r\n", FILE_APPEND);
            http_response_code(406);
            exit;
        }

        $status = $params['status'];

        if ($status != 'success') {
            file_put_contents(dirname(__FILE__). '/payments.log.txt', date('Y-m-d H:i:s') . "callback # crypto: unsuccessful status for order $order_id - $status \r\n", FILE_APPEND);
            exit;
        }

        file_put_contents(dirname(__FILE__). '/payments.log.txt', date('Y-m-d H:i:s') . ' - clien_id crypto - ' . $order_id . ' - ' . $client_id . "\r\n", FILE_APPEND);

        //$client_id = $_JCMS->db->escape_string($client_id);
        $res = $_JCMS->db->query("SELECT * FROM `jcms2_moduleClients` WHERE `client_id` = '{$client_id}'");

        //file_put_contents(dirname(__FILE__). '/payments.log.txt', date('Y-m-d H:i:s') . " - callback # enot: " . "SELECT * FROM `jcms2_moduleClients` WHERE `client_id` = '{$client_id}'" . "\r\n", FILE_APPEND);

        if( $res == false ){ echo "database err1\n"; return; }
        if($res->num_rows == 1 ){
            $data = $res->fetch_assoc();
            $out_summ = floatval($out_summ);
            $sql_code = "INSERT INTO `jcms2_moduleClientsBalance`(`client_id`, `summ`, `descr`) VALUES ('{$client_id}', '{$out_summ}', 'Пополнение через Crypto <i>(транзакция №{$c_invoice_id})</i>')";
            $res = $_JCMS->db->query($sql_code);
            if( $res == false ){ echo "database err3\n"; return; }

            /* --- НАЧИСЛЕНИЕ ПО РЕФ ПРОГРАММЕ --- */
            // проверяем есть ли реферал у этого юзера....
            $res = $_JCMS->db->query("SELECT * FROM `jcms2_moduleClients` WHERE `client_id` = '{$client_id}' AND `client_ref_id` IS NOT NULL");
            if( $res == false ){ echo "database err4\n"; return; }
            if($res->num_rows == 1 ){
                // есть реферал...
                $data = $res->fetch_assoc();
                $referals = $_JCMS->getConfig('referals');
                $rate = 0; // ставка по умолчанию...

                // определяем текущую партнерскую ставку...
                $res = $_JCMS->db->query("SELECT * FROM `jcms2_moduleClients` WHERE `client_id` = '{$data['client_ref_id']}' AND `client_ref_rate` IS NOT NULL");
                if( $res == false ){ echo "database err5\n"; return; }
                if($res->num_rows == 1 ){
                    // у клиента специальная партнерская ставка
                    $data2 = $res->fetch_assoc();
                    $rate = $data2['client_ref_rate'];
                } else {
                    // подбираем подходящую ставку...
                    if( is_array($referals['rates']) ){
                        ksort($referals['rates']); // сортиурем по возрастанию...
                        // определяем подходящую сумме ставку...
                        foreach($referals['rates'] as $summ=>$_rate){
                            if( $out_summ >= $summ ) $rate = $_rate;
                        }
                    }
                }
                $ref_summ = floatval($out_summ*$rate/100); // партнерское вознаграждение
                if( $referals['mode'] == 0 ){
                    // выплаты разово
                    // проверяем были ли уже выплаты по этмоу рефералу?
                    $res = $_JCMS->db->query("SELECT * FROM `jcms2_moduleClientsBalance` WHERE `client_id` = '{$data['client_ref_id']}' AND `ref_id` = '{$client_id}'");
                    if( $res == false ){ echo "database err6\n"; return; }
                    if($res->num_rows == 0 ){
                        // выплат еще не было...
                        $referals['mode'] = 1; // разрешаем проведение выплаты
                    }
                }
                if( $referals['mode'] == 1 ){
                    // выплаты постоянно
                    // делаем выплату
                    $sql_code = "INSERT INTO `jcms2_moduleClientsBalance`(`client_id`, `summ`, `descr`, `ref_id`) VALUES ('{$data['client_ref_id']}', '{$ref_summ}', 'Партнёрское вознаграждение за приведённого пользователя <i>{$this->hideStr($data['client_login'])}</i> ({$rate}%)', '{$client_id}')";
                    $res = $_JCMS->db->query($sql_code);
                    if( $res == false ){ echo "database err7\n"; return; }
                }
            }
            /* ---------------------------------------- */

            echo "OK";
            return;
        } else {
            echo "client_not_found";
            return;
        }
	}
	
	function action_payment(){
		global $_JCMS;

        $sign = $request_body = file_get_contents('php://input');

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            file_put_contents(dirname(__FILE__) . '/payments.log.txt', date('Y-m-d H:i:s') . ' - callback # enot: Invalid method.' . "\r\n", FILE_APPEND);
            http_response_code(405); // Method Not Allowed
            exit;
        }

        /*$signature_header = $_SERVER['HTTP_X_API_SHA256_SIGNATURE'];

        if (!$signature_header) {
            file_put_contents(dirname(__FILE__). '/payments.log.txt', date('Y-m-d H:i:s') . ' - callback # enot: not signature.' . "\r\n", FILE_APPEND);
            exit;
        }

        $sign = json_decode($sign, true);
        ksort($sign);
        $sign = json_encode($sign);
        $secret_key = '7f452eb68d950a2f2e99088071e235c3e7c68ad4';
        $calculated_signature = hash_hmac('sha256', $sign, $secret_key);

        // Compare the calculated signature with the signature in the header
        if (!hash_equals($signature_header, $calculated_signature)) {
            file_put_contents(dirname(__FILE__). '/payments.log.txt', date('Y-m-d H:i:s') . ' - callback # enot:Invalid signature.' . "\r\n", FILE_APPEND);
            http_response_code(401); // Unauthorized
            exit;
        }*/

        $request_body = json_decode($request_body, 1);

        $order_id = $request_body['order_id'];

        $out_summ = $request_body['amount'];
        $invoice_id = $request_body['invoice_id'];
        $tmp = explode('_', $order_id);
        $order_id = $tmp[0];
        $order_id = str_replace('"', '', $order_id);
        $client_id = $tmp[1];
        $client_id = str_replace('"', '', $client_id);

        file_put_contents(dirname(__FILE__). '/payments.log.txt', date('Y-m-d H:i:s') . ' - invoice_id - ' . $invoice_id . "\r\n", FILE_APPEND);

        //file_put_contents(dirname(__FILE__). '/payments.log.txt', date('Y-m-d H:i:s') . " - callback # enot: " . var_dump($request_body) . "\r\n", FILE_APPEND);

        if (!$order_id) {
            file_put_contents(dirname(__FILE__). '/payments.log.txt', date('Y-m-d H:i:s') . ' - callback # enot: not order_id.' . "\r\n", FILE_APPEND);
            http_response_code(406);
            exit;
        }

        $status = $request_body['status'];

        if ($status != 'success') {
            file_put_contents(dirname(__FILE__). '/payments.log.txt', date('Y-m-d H:i:s') . "callback # enot: unsuccessful status for order $order_id - $status \r\n", FILE_APPEND);
            exit;
        }

        file_put_contents(dirname(__FILE__). '/payments.log.txt', date('Y-m-d H:i:s') . ' - clien_id - ' . $order_id . ' - ' . $client_id . "\r\n", FILE_APPEND);

        //$client_id = $_JCMS->db->escape_string($client_id);
        $res = $_JCMS->db->query("SELECT * FROM `jcms2_moduleClients` WHERE `client_id` = '{$client_id}'");

        //file_put_contents(dirname(__FILE__). '/payments.log.txt', date('Y-m-d H:i:s') . " - callback # enot: " . "SELECT * FROM `jcms2_moduleClients` WHERE `client_id` = '{$client_id}'" . "\r\n", FILE_APPEND);

        if( $res == false ){ echo "database err1\n"; return; }
        if($res->num_rows == 1 ){
            $data = $res->fetch_assoc();
            $out_summ = floatval($out_summ);
            $sql_code = "INSERT INTO `jcms2_moduleClientsBalance`(`client_id`, `summ`, `descr`) VALUES ('{$client_id}', '{$out_summ}', 'Пополнение через ENOT.IO <i>(транзакция №{$invoice_id})</i>')";
            $res = $_JCMS->db->query($sql_code);
            if( $res == false ){ echo "database err3\n"; return; }

            /* --- НАЧИСЛЕНИЕ ПО РЕФ ПРОГРАММЕ --- */
            // проверяем есть ли реферал у этого юзера....
            $res = $_JCMS->db->query("SELECT * FROM `jcms2_moduleClients` WHERE `client_id` = '{$client_id}' AND `client_ref_id` IS NOT NULL");
            if( $res == false ){ echo "database err4\n"; return; }
            if($res->num_rows == 1 ){
                // есть реферал...
                $data = $res->fetch_assoc();
                $referals = $_JCMS->getConfig('referals');
                $rate = 0; // ставка по умолчанию...

                // определяем текущую партнерскую ставку...
                $res = $_JCMS->db->query("SELECT * FROM `jcms2_moduleClients` WHERE `client_id` = '{$data['client_ref_id']}' AND `client_ref_rate` IS NOT NULL");
                if( $res == false ){ echo "database err5\n"; return; }
                if($res->num_rows == 1 ){
                    // у клиента специальная партнерская ставка
                    $data2 = $res->fetch_assoc();
                    $rate = $data2['client_ref_rate'];
                } else {
                    // подбираем подходящую ставку...
                    if( is_array($referals['rates']) ){
                        ksort($referals['rates']); // сортиурем по возрастанию...
                        // определяем подходящую сумме ставку...
                        foreach($referals['rates'] as $summ=>$_rate){
                            if( $out_summ >= $summ ) $rate = $_rate;
                        }
                    }
                }
                $ref_summ = floatval($out_summ*$rate/100); // партнерское вознаграждение
                if( $referals['mode'] == 0 ){
                    // выплаты разово
                    // проверяем были ли уже выплаты по этмоу рефералу?
                    $res = $_JCMS->db->query("SELECT * FROM `jcms2_moduleClientsBalance` WHERE `client_id` = '{$data['client_ref_id']}' AND `ref_id` = '{$client_id}'");
                    if( $res == false ){ echo "database err6\n"; return; }
                    if($res->num_rows == 0 ){
                        // выплат еще не было...
                        $referals['mode'] = 1; // разрешаем проведение выплаты
                    }
                }
                if( $referals['mode'] == 1 ){
                    // выплаты постоянно
                    // делаем выплату
                    $sql_code = "INSERT INTO `jcms2_moduleClientsBalance`(`client_id`, `summ`, `descr`, `ref_id`) VALUES ('{$data['client_ref_id']}', '{$ref_summ}', 'Партнёрское вознаграждение за приведённого пользователя <i>{$this->hideStr($data['client_login'])}</i> ({$rate}%)', '{$client_id}')";
                    $res = $_JCMS->db->query($sql_code);
                    if( $res == false ){ echo "database err7\n"; return; }
                }
            }
            /* ---------------------------------------- */

            echo "OK$client_id\n";
            return;
        } else {
            echo "client_not_found";
            return;
        }

        die;

		$post = file_get_contents('php://input');
		
		//mail('fxbyden@gmail.com','ssss', json_encode($post));

		// Логирование запросов в файл
		file_put_contents(dirname(__FILE__).'/payments.log.txt', "---\n\n".date('d.m.Y H:i:s')."\n\$_POST = ".var_export($_POST,1).";\n\n",FILE_APPEND);
		$mrh_pass2 = "i6el2zem";
		$out_summ = $_REQUEST['amount'];
		$invoice_id = $_POST["intid"];
		$client_id = $_POST["custom_field"];
		$crc = $_POST["SignatureValue"];
		$crc = strtoupper($crc);
		$merchant = '29622';
		
		//$secret_word2 = 'xaMSP0hl1azTwIAC-ixtQfloDnQVF6jw';
		$secret_word2 = '7f452eb68d950a2f2e99088071e235c3e7c68ad4';

		$sign = md5($merchant.':'.$_REQUEST['amount'].':'.$secret_word2.':'.$_REQUEST['merchant_id']);

		if ($sign == $_REQUEST['sign_2'])
		{
			$client_id = $_JCMS->db->escape_string($client_id);
			$res = $_JCMS->db->query("SELECT * FROM `jcms2_moduleClients` WHERE `client_id` = '{$client_id}'");
			if( $res == false ){ echo "database err1\n"; return; }
			if($res->num_rows == 1 ){
				$data = $res->fetch_assoc();
				$out_summ = floatval($out_summ); 
				$sql_code = "INSERT INTO `jcms2_moduleClientsBalance`(`client_id`, `summ`, `descr`) VALUES ('{$client_id}', '{$out_summ}', 'Пополнение через ENOT.IO <i>(транзакция №{$invoice_id})</i>')";
				$res = $_JCMS->db->query($sql_code);
				if( $res == false ){ echo "database err3\n"; return; }

				/* --- НАЧИСЛЕНИЕ ПО РЕФ ПРОГРАММЕ --- */
				// проверяем есть ли реферал у этого юзера....
				$res = $_JCMS->db->query("SELECT * FROM `jcms2_moduleClients` WHERE `client_id` = '{$client_id}' AND `client_ref_id` IS NOT NULL");
				if( $res == false ){ echo "database err4\n"; return; }
				if($res->num_rows == 1 ){
					// есть реферал...
					$data = $res->fetch_assoc();
					$referals = $_JCMS->getConfig('referals');
					$rate = 0; // ставка по умолчанию...
					
					// определяем текущую партнерскую ставку...
					$res = $_JCMS->db->query("SELECT * FROM `jcms2_moduleClients` WHERE `client_id` = '{$data['client_ref_id']}' AND `client_ref_rate` IS NOT NULL");
					if( $res == false ){ echo "database err5\n"; return; }
					if($res->num_rows == 1 ){
						// у клиента специальная партнерская ставка
						$data2 = $res->fetch_assoc();
						$rate = $data2['client_ref_rate'];
					} else {
						// подбираем подходящую ставку...
						if( is_array($referals['rates']) ){
							ksort($referals['rates']); // сортиурем по возрастанию...
							// определяем подходящую сумме ставку...
							foreach($referals['rates'] as $summ=>$_rate){
								if( $out_summ >= $summ ) $rate = $_rate;
							}
						}
					}
					$ref_summ = floatval($out_summ*$rate/100); // партнерское вознаграждение
					if( $referals['mode'] == 0 ){
						// выплаты разово
						// проверяем были ли уже выплаты по этмоу рефералу?
						$res = $_JCMS->db->query("SELECT * FROM `jcms2_moduleClientsBalance` WHERE `client_id` = '{$data['client_ref_id']}' AND `ref_id` = '{$client_id}'");
						if( $res == false ){ echo "database err6\n"; return; }
						if($res->num_rows == 0 ){
							// выплат еще не было...
							$referals['mode'] = 1; // разрешаем проведение выплаты
						}
					}
					if( $referals['mode'] == 1 ){
						// выплаты постоянно
						// делаем выплату
						$sql_code = "INSERT INTO `jcms2_moduleClientsBalance`(`client_id`, `summ`, `descr`, `ref_id`) VALUES ('{$data['client_ref_id']}', '{$ref_summ}', 'Партнёрское вознаграждение за приведённого пользователя <i>{$this->hideStr($data['client_login'])}</i> ({$rate}%)', '{$client_id}')";
						$res = $_JCMS->db->query($sql_code);
						if( $res == false ){ echo "database err7\n"; return; }
					}
				}
				/* ---------------------------------------- */		

				echo "OK$client_id\n";
				return;		
			} else {
				echo "client_not_found";
				return;
			}
		} else {

		  	echo "bad_sign\n";
		  	return;		
		}
		echo "unknown_error\n";
		return;
	}  
	
	function isAuth(){
		return $this->checkAuth(1);
	}
   
	function logout(){
		if( $_JCMS->lang == 'en' ){
			$_SESSION["JENSENCMS"]['client_msg'] = array('Вы вышли!', '', 'notice');
		} else {
			$_SESSION["JENSENCMS"]['client_msg'] = array('Вы вышли!', '', 'notice');
		}
		unset($_SESSION["JENSENCMS"]['SITE_AUTH']);
		return true;
	} 
	
	function checkAuth($cache=false){
		global $_JCMS, $_SESSION, $_JSON;
		if( !$_SESSION["JENSENCMS"]['SITE_AUTH'] ){ return false; } // нет сессии авторизации
		if( $cache ){ return true; }
		// проверяем валидность аккаунта
		$profile = $this->getProfile();
		$sql_code = " 
SELECT * FROM 

`jcms2_moduleClients` 

WHERE  `jcms2_moduleClients`.`client_id` = '{$profile[client_id]}' AND `client_status` = '1'
		"; 

		if( !$res = $_JCMS->db->query($sql_code) ){ 
			$this->logout();
			$_SESSION["JENSENCMS"]['client_msg'] = array("Ошибка базы данных! [php/module.cabinet#".__LINE__."]", $_JCMS->show_php_errors?("MYSQL ERROR #".$_JCMS->db->errno." - ".$_JCMS->db->error):'', 'error'); 
			return false;
		}
		if( $res->num_rows !== 1 ){
			$this->logout();
			if( $_JCMS->lang == 'en' ){
				$_SESSION["JENSENCMS"]['client_msg'] = array("Error: The account credentials have been changed!", 'Re-enter your account','error');
			} else {
				$_SESSION["JENSENCMS"]['client_msg'] = array("Ошибка: учетные данные аккаунта были изменены!",'Необходим повторный вход','error');
			}
			return false;
		}
		$data = $res->fetch_assoc();
		if( $profile['client_token'] !== $data['client_token'] ){
			$this->logout();
			if( $_JCMS->lang == 'en' ){
				$_SESSION["JENSENCMS"]['client_msg'] = array("Error: The account credentials have been changed!", 'Re-enter your account','error');
			} else {
				$_SESSION["JENSENCMS"]['client_msg'] = array("Ошибка: учетные данные аккаунта были изменены!",'Необходим повторный вход','error');
			}
			return false;
		}
		$_SESSION["JENSENCMS"]['SITE_AUTH'] = $data;
		return true;
	}
	
	function getProfile(){
		return $_SESSION["JENSENCMS"]['SITE_AUTH'];
	}

	function genOrderId(){
		global $_JCMS;  
		$symbols = array('1','2','3','4','5','6','7','8','9');
		$uniqid = "";
		for($i = 0; $i < 6; $i++){
			$index = rand(0, count($symbols) - 1);
			$uniqid .= $symbols[$index];
		}
		$uniqid = intval($uniqid);
		$sql = $_JCMS->db->query("SELECT * FROM `jcms2_moduleClientsOrders` WHERE `order_id` = '{$uniqid}'");
		if( $sql->num_rows != 0 ){ $uniqid = $this->genOrderId(); }
		return $uniqid;
		 
	}
	
	function genTicketId(){
		global $_JCMS;  
		$symbols = array('1','2','3','4','5','6','7','8','9');
		$uniqid = "";
		for($i = 0; $i < 6; $i++){
			$index = rand(0, count($symbols) - 1);
			$uniqid .= $symbols[$index];
		}
		$uniqid = intval($uniqid);
		$sql = $_JCMS->db->query("SELECT * FROM `jcms2_moduleClientsTickets` WHERE `ticket_id` = '{$uniqid}'");
		if( $sql->num_rows != 0 ){ $uniqid = $this->genTicketId(); }
		return $uniqid;
		 
	}
	
	function checkFrod($login=''){
		global $_JCMS;
		$login = $_JCMS->db->escape_string($login);
		$ip = $_JCMS->db->escape_string($this->getUserIP());
		$sql_code = "SELECT * FROM `jcms2_frodControl` WHERE (".(!empty($login)?"`login` = '{$login}' OR ":"")."`ip` = '{$ip}') AND `date` >= CURRENT_TIMESTAMP-INTERVAL 30 MINUTE";

		if( !$res = $_JCMS->db->query($sql_code) ){
			$_SESSION["JENSENCMS"]['client_msg'] = array("Ошибка базы данных! [php/module.cabinet#".__LINE__."]", $_JCMS->show_php_errors?("MYSQL ERROR #".$_JCMS->db->errno." - ".$_JCMS->db->error):'', 'error'); 
			return false;
		} else
		if( $res->num_rows > 0 ){
			return $res->num_rows; // счетчик фрода
		}
		
		return false;
	}
	
	function frodAdd($login=''){
		global $_JCMS;
		$login = $_JCMS->db->escape_string($login);
		$ip = $_JCMS->db->escape_string($this->getUserIP());
		$sql_code = "INSERT INTO `jcms2_frodControl`(`login`, `ip`) VALUES ('{$login}', '{$ip}')";
		if( !$res = $_JCMS->db->query($sql_code) ){
			$_SESSION["JENSENCMS"]['client_msg'] = array("Ошибка базы данных! [php/module.cabinet#".__LINE__."]", $_JCMS->show_php_errors?("MYSQL ERROR #".$_JCMS->db->errno." - ".$_JCMS->db->error):'', 'error'); 
			return false;
		}
		
		return true;
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
	
	function validate_ip($ip) {
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
	
	function getBalance($client_id=false){
		global $_JCMS;
		if( $client_id < 1 ){ $client_id = $this->getProfile(); $client_id = $client_id['client_id']; }
		$client_id = $_JCMS->db->escape_string($client_id);
		$sql_code = "SELECT SUM(`summ`) as `balance` FROM `jcms2_moduleClientsBalance` WHERE `client_id` = '{$client_id}'";
		if( !$res = $_JCMS->db->query($sql_code)){
			$_JCMS->message("Ошибка базы данных! [php/module.cabinet#".__LINE__."]", $_JCMS->show_php_errors?("MYSQL ERROR #".$_JCMS->db->errno." - ".$_JCMS->db->error):'', 'error');
		} else {
			$data = $res->fetch_assoc();
			return round($data['balance']<1?0:$data['balance'],2);
		}
	}
	
	function getCountDown($dateBefore){
		global $_JCMS;
		//вычисление оставшихся дней, часов и минут
		$now_date = time();
		$future_date = strtotime($dateBefore);
		$days = 0; $min = 0; $hours = 0;
		if( $future_date > $now_date ){
			$difference_days = $future_date - $now_date;
			$days = floor($difference_days/86400);
			$difference_hours = $difference_days % 86400;
			$hours = floor($difference_hours/3600);
			$difference_min = $difference_hours % 3600;
			$min = floor($difference_min/60);
		}
		if( $_JCMS->lang == 'en' ){
			return $days.'d. '.$hours.'h. '.$min.'min.';	
		} else {
			return $days.'дн. '.$hours.'ч. '.$min.'мин.';	
		}
	}

	function genReferalCode(){
		global $_JCMS;
		$symbols = array('a','b','c','d','e','f','g','h','i','j','k','l','m','n','o','p','r','s','t','u','v','x','y','z','1','2','3','4','5','6','7','8','9','0');
		$uniqid = "";
		for($i = 0; $i < 6; $i++){
			$index = rand(0, count($symbols) - 1);
			$uniqid .= $symbols[$index];
		}
		$res = $_JCMS->db->query("SELECT * FROM `jcms2_moduleClients` WHERE `client_referalCode` = '{$uniqid}'"); 
		if( $res->num_rows == 1 ){ $uniqid = $this->genReferalCode(); }
		return $uniqid;
		
	}
	
	function hideStr($str){
		for ($i=3; $i<mb_strlen($str, 'utf-8')-1; $i++){
			$str[$i] = '*'; 
		}
		return $str;
	}

	function getNews($id,$public_only=true){
		global $_JCMS;
		$id = $_JCMS->db->escape_string($id);
		if( $public_only ){
			$public_only = "AND `news_status` = '1'";
		}
		$sql_code = "SELECT `jcms2_moduleNews`.*, `jcms2_admins`.`admin_name` FROM `jcms2_moduleNews`, `jcms2_admins` WHERE `jcms2_admins`.`admin_id` = `jcms2_moduleNews`.`admin_id` AND (`news_id` = '{$id}' OR `news_sysname` = '{$id}') {$public_only} LIMIT 1";

		if( !$res = $_JCMS->db->query($sql_code) ){
			return $_JCMS->message("Ошибка базы данных! [php/news#".__LINE__."]", $_JCMS->show_php_errors?"MYSQL ERROR #".$_JCMS->db->errno." - ".$_JCMS->db->error:'', 'error');
		} 
		
		if( $res->num_rows < 1 ) return array(); // нет новостей

		return $res->fetch_assoc();
	}

	function getAllNews($public_only=true, $start=0){
		global $_JCMS;
		$onpage = $this->onpage;
		if( $public_only ){
			$public_only = "AND `news_status` = '1'";
		}
		if( $this->ondate ){
			$sql_date = " AND `news_dateCreate` LIKE '%".date('Y-m-d', $this->ondate)."%'";
		}
		$sql_code = "SELECT SQL_CALC_FOUND_ROWS `jcms2_moduleNews`.*, `jcms2_moduleNews`.*, `jcms2_admins`.`admin_name` FROM `jcms2_moduleNews`, `jcms2_admins` WHERE `jcms2_admins`.`admin_id` = `jcms2_moduleNews`.`admin_id` {$public_only} {$sql_date} ORDER BY `news_dateCreate` DESC LIMIT {$start},{$onpage}";

		if( !$res = $_JCMS->db->query($sql_code) ){
			return $_JCMS->message("Ошибка базы данных! [php/news#".__LINE__."]", $_JCMS->show_php_errors?"MYSQL ERROR #".$_JCMS->db->errno." - ".$_JCMS->db->error:'', 'error');
		} 
		if( $res->num_rows < 1 ) return array(); // нет новостей

		$sql_code = "SELECT FOUND_ROWS() as total;";		
		if( !$res2 = $_JCMS->db->query($sql_code) ){
			return $_JCMS->message("Ошибка базы данных! [php/news#".__LINE__."]", $_JCMS->show_php_errors?"MYSQL ERROR #".$_JCMS->db->errno." - ".$_JCMS->db->error:'', 'error');
		} 
		$data2 = $res2->fetch_assoc();

		$news = array();
		while( $data = $res->fetch_assoc() ){
			$news[] = $data;
		}
		
		return array('news'=>$news, 'total'=> $data2['total']);
	}
	
}

// END.
?>