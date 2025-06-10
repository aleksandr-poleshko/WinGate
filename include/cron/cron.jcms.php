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

ob_start();
session_start();
error_reporting(E_ALL & ~E_NOTICE|E_DEPRECATED);
ini_set('display_errors', 0); 
if( php_sapi_name() !== 'cli' ){
	header('Content-type: text/html; charset=utf-8');
	$req = strval($_SERVER['QUERY_STRING']);
} else {
	$req = $_SERVER['argv'][1];

}
$req = explode("&", $req);
set_time_limit(0);
ini_set('max_execution_time', 0); 


define("JENSENCMS2", TRUE);
define("ROOT_PATH", realpath(dirname(__FILE__).'/../../'));
 
include("../functions.php");
include("./jcms_cron.class.php");

$cron = new JCMS_CRON();

$date  = "<br/>\r\n---------------------<br/>\r\n";
$date .= ' '.date("d-m-Y H:i:s");
$date .= "<br/>\r\n---------------------";
$cron->log($date,0);

$date = time();

foreach($req as $val){
	$val = $cron->db->escape_string($val);
	if( method_exists($cron, $val) ){
		ob_end_flush(); 
		$cron->$val();	
	}
}
$o  = "<br/>\r\n---------------------<br/>\r\n";
$o .= "Время работы: ".((time()-$date))."сек.";
$cron->log($o,0);
// END.
?>