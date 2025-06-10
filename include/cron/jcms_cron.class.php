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

if( !defined("JENSENCMS2") ) { $_SERVER['REDIRECT_STATUS'] = 404; include('../../error.php'); exit(); }
 
class JCMS_CRON{
	var $db; // линк к БД
	var $conf; // конф JCMS
	private $log = 0; 
	
	function __construct(){

		if( !include(ROOT_PATH."/config/db.config.php") ){
			exit ("Ошибка загрузки конфигурации базы данных! [#".__LINE__."]");
		}
		if( !include(ROOT_PATH."/include/templates.class.php") ){
			exit ("Ошибка загрузки шаблонизатора! [#".__LINE__."]");
		}
		$this->tpl = new TPL(ROOT_PATH.'/modules/');

		$this->db = new mysqli($db_conf['hostname'], $db_conf['username'], $db_conf['password'], $db_conf['database']);
		unset($db_conf); 
		if( mysqli_connect_error() ){
			exit ("MYSQL ERROR #" . $this->db->connect_errno . ' - ' . $this->db->connect_error.' [#'.__LINE__.']');
		}
		if( !$this->db->set_charset("utf8") ){
			exit("Ошибка определения кодировки для работы с базой данных! [#".__LINE__."]");
		}
		unset($db_conf);
		
		if( !include(ROOT_PATH."/config/jcms.config.php") ){
			exit ("Ошибка загрузки конфигурации Jensen CMS! [#".__LINE__."]");
		}
		$this->conf = $conf;
	}

	// отправка сообщения по почте
	function send_email($to, $subject, $text){
		if( empty($to) || !filter_var($to, FILTER_VALIDATE_EMAIL) || empty($subject) || empty($text) ){
			$this->log("ERROR in send_email('$to', '$subject', '...') - неверный email адрес [#".__LINE__."]");
			return false;
		}
		        
		if( !class_exists('MultipleInterfaceMailer') ) include_once(ROOT_PATH.'/include/phpmailer/class.phpmailer.php');
		$mail = new PHPMailer();
		try {
		  $mail->Host = $this->conf['smtp_server'];
		  $mail->Port   = $this->conf['smtp_port'];
		  if( $mail->Port == 465 ) $mail->SMTPSecure = 'ssl';
		  $mail->Username   = $this->conf['smtp_username'];
		  $mail->Password   = $this->conf['smtp_password'];
		  $mail->SetFrom($this->conf['smtp_from_email'], $this->conf['smtp_from_name']);
		  $mail->AddAddress($to); 
		  $mail->Subject = iconv("utf-8",'cp1251',$subject);
		  $mail->MsgHTML(iconv("utf-8",'cp1251',$text)); 
		  $mail->SMTPDebug = 0; 
		  $mail->Debugoutput = 0;
		  
		  $mail->Send();
		  $this->log("Send_email('$to', '$subject', '...') - успешно отправлено [#".__LINE__."]");
		  return true;
		} catch (phpmailerException $e) {
			$this->log("ERROR in send_email('$to', '$subject', '...') - ".$e->errorMessage()." [#".__LINE__."]");
			return false;
		} catch (Exception $e) {
			$this->log("ERROR in send_email('$to', '$subject', '...') - ".$e->getMessage()." [#".__LINE__."]");
			return false;
		}  
		return false;
	}
	
	public function log($msg, $c=1){ 
		if( $c == 1 ){
			$this->log++;
			$msg = "<br/>\r\n[".$this->log."] >> ".$msg;
		} 
		$file = dirname(__FILE__).'/_cron.log.html'; // лог за этот день
		if( file_exists($file) && filemtime($file) < strtotime(date("d.m.Y 00:00:00")) ){
			$oldfile = dirname($file).'/_cron.old.log.html'; // лог за прошедший день
			if( file_exists($oldfile) ){
				unlink($oldfile);
			}
			rename($file, $oldfile);
		}
		echo $msg;
		$handle = fopen($file, "a+");
		flock ($handle, LOCK_EX);
		fwrite ($handle, $msg);
		flock ($handle, LOCK_UN);
		fclose($handle);
		return true;
	} 
	 
	function checkDB(){
		// удаляем старые неактуальные записи из таблицы фрод-контроля старше 14 дней
		$sql_code = "DELETE FROM `jcms2_frodControl` WHERE `date` < CURRENT_TIMESTAMP - INTERVAL 14 DAY";

		if( !$res = $this->db->query($sql_code) ){
			$this->log("checkDB() :: MYSQL ERROR #".$this->db->errno." - ".$this->db->error."[#".__LINE__."]"); 
			return false;
		}
		$this->log("checkDB() :: OK.");
	}

	function checkOrders(){
		// блокируем истекшие тестовые заказы в архив
		$sql_code = "UPDATE `jcms2_moduleClientsOrders` SET `order_status` = '3', `order_isLock` = '0' WHERE `order_isTest` = '1' AND `order_paidBefore` <= CURRENT_TIMESTAMP";

		if( !$res = $this->db->query($sql_code) ){
			$this->log("checkDB() :: MYSQL ERROR #".$this->db->errno." - ".$this->db->error."[#".__LINE__."]"); 
			return false;
		}
		$this->log("checkTestOrders() :: OK.");

		// помещает просроченные заказы в архив 
		$sql_code = "UPDATE `jcms2_moduleClientsOrders` SET `order_status` = '2', `order_isLock` = '0' WHERE `order_isTest` != '1' AND `order_paidBefore` <= CURRENT_TIMESTAMP";

		if( !$res = $this->db->query($sql_code) ){
			$this->log("checkDB() :: MYSQL ERROR #".$this->db->errno." - ".$this->db->error."[#".__LINE__."]"); 
			return false;
		}
		$this->log("checkOrders() :: OK.");
	}
	
	function sendEmailNotify(){
		// помещает просроченные заказы в архив  
		$sql_code = "SELECT *
		
		FROM `jcms2_moduleClientsOrders` as `a`
			LEFT JOIN `jcms2_moduleClients` as `b` ON `b`.`client_id` = `a`.`client_id`
		
		WHERE `order_notifyDate` < CURRENT_TIMESTAMP"; 

		if( !$res = $this->db->query($sql_code) ){
			$this->log("sendEmailNotify() :: MYSQL ERROR #".$this->db->errno." - ".$this->db->error."[#".__LINE__."]"); 
			return false;
		}
		while( $data = $res->fetch_assoc() ){
			$date1 = strtotime($data['order_paidBefore']); // оплачено до
			$date2 = strtotime($data['order_notifyDate']); // дата отравки уведомления
			$comp = $date1-$date2;
			if( $comp > 21600 ){ // 21600 сек = 6 часов
				// первое уведомление (больше 6 часов до конца)
				$this->tpl->load("cabinet/emails/notify1d.tpl");
				$t = 1;				
			} else {
				// второе уведомление (меньше 6 часов до конца)
				$this->tpl->load("cabinet/emails/notify6h.tpl");				
				$t = 2;
			}
			$this->tpl->tag("{TAR_TITLE}", $data['order_title']); 
			$tpl = $this->tpl->compile();
			
			$this->send_email($data['client_email'], "Уведомление / Notification | ".$_SERVER['HTTP_HOST'], $tpl);

			$this->log("sendEmailNotify() :: send notify to [#{$data['client_id']}] {$data['client_email']} on order #{$data['order_id']} (type={$t})");
			
			if( $t == 1 ){
				$sql_code = "UPDATE `jcms2_moduleClientsOrders` SET `order_notifyDate` = (`order_paidBefore` - INTERVAL 6 HOUR) WHERE `order_id` = '{$data['order_id']}' LIMIT 1";
			} else {
				$sql_code = "UPDATE `jcms2_moduleClientsOrders` SET `order_notifyDate` = NULL WHERE `order_id` = '{$data['order_id']}' LIMIT 1";
			}
			
			if( !$this->db->query($sql_code) ){
				$this->log("checkDB() :: MYSQL ERROR #".$this->db->errno." - ".$this->db->error."[#".__LINE__."]"); 
			}
		}
		
		$this->log("sendEmailNotify() :: OK.");
	}
	
	
	function genIpDB(){
		// удаляем старые неактуальные записи из таблицы фрод-контроля старше 14 дней
		// order_isLock = 1 - замороженные заказы
		$sql_code = "SELECT * FROM `jcms2_moduleClientsOrders` WHERE `order_paidBefore` > CURRENT_TIMESTAMP AND `order_status` IN ('0','1') AND `order_isLock` = '0'";

		if( !$res = $this->db->query($sql_code) ){
			$this->log("genIpDB() :: MYSQL ERROR #".$this->db->errno." - ".$this->db->error."[#".__LINE__."]"); 
			return false;
		}
		$file = dirname(__FILE__).'/ip_db.txt';
		$handle = fopen($file, "w+");
		flock ($handle, LOCK_EX);
		$ids = $ips = array();
		while( $data = $res->fetch_assoc() ){
			$data['order_data'] = json_decode($data['order_data'], 1);
			if( !empty($data['order_data']) ){
				$ids[] = $data['order_id'];
				if( !is_array($data['order_data']['ip']) || count($data['order_data']['ip']) == 0 ) continue;
				foreach($data['order_data']['ip'] as $ip){		
					if( in_array($ip, $ips) ) continue;
					$ips[] = $ip;
					fwrite($handle, $ip."\r\n");
				}
			}
		}
		flock ($handle, LOCK_UN);
		fclose($handle);
		
		if( !empty($ids) ){
			$sql_code = "UPDATE `jcms2_moduleClientsOrders` SET `order_status` = '1' WHERE `order_status` = '0' AND `order_id` IN ('".implode("', '",$ids)."')";

			if( !$res = $this->db->query($sql_code) ){
				$this->log("genIpDB() :: MYSQL ERROR #".$this->db->errno." - ".$this->db->error."[#".__LINE__."]"); 
				return false;
			}
		}

		$this->log("genIpDB() :: OK."); 

		ob_end_clean();
		// заставляем браузер показать окно сохранения файла
		header('Content-Description: File Transfer');
		header('Content-Type: application/octet-stream');
		header('Content-Disposition: attachment; filename='.basename($file));
		header('Content-Transfer-Encoding: binary');
		header('Expires: 0');
		header('Cache-Control: must-revalidate');
		header('Pragma: public');
		header('Content-Length: ' . filesize($file));

		readfile($file);
		exit;
	}
}

// END.
?>