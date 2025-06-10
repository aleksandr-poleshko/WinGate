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

header("HTTP/1.0 200");
ob_start();
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);
header('Content-type: text/html; charset=utf-8');
set_time_limit(5*60); // макс лимит времени выполнения (5 мин)

$con = mysqli_connect('localhost', 'wingate', 'd8F6E5Fy', 'wingate');
if (!$con) {
    die('Не удалось соединиться : ' . mysqli_error());
}

mysqli_set_charset($con, 'utf8');

mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

$request_body = file_get_contents('php://input');
$params = [];
$params = json_decode($request_body, 1);

//file_put_contents(dirname(__FILE__). '/payments.log.txt', date('Y-m-d H:i:s') . " - callback # crypto: " . $request_body . "\r\n", FILE_APPEND);

$out_summ = $params['amount'];
$invoice_id = $params['order_id'];
$c_invoice_id = $params['uuid'];
$tmp = explode('_', $invoice_id);
$order_id = $tmp[0];
$order_id = str_replace('"', '', $order_id);
$client_id = $tmp[1];
$client_id = str_replace('"', '', $client_id);
$status = $params['status'];

if (($status == 'paid') || ($status == 'paid_over')) {
    $res = mysqli_query($con, "SELECT * FROM `jcms2_moduleClients` WHERE `client_id` = '{$client_id}'");

    if( $res == false ){ echo "database err1\n"; return; }
    if($res->num_rows == 1 ){
        $data = mysqli_fetch_assoc($res);
        $out_summ = floatval($out_summ);
        $sql_code = "INSERT INTO `jcms2_moduleClientsBalance`(`client_id`, `summ`, `descr`) VALUES ('{$client_id}', '{$out_summ}', 'Пополнение через Cryptomus.com <i>(транзакция №{$c_invoice_id})</i>')";
        $res = mysqli_query($con, $sql_code);
        if( $res == false ){ echo "database err3\n"; return; }

        echo "OK";
        return;
    } else {
        echo "client_not_found";
        return;
    }
}
else {
    echo "status error";
    return;
}

?>