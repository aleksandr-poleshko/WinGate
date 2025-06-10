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

$request_body = file_get_contents('php://input');

$tmp_data = explode('&', $request_body);
$params = [];
foreach ($tmp_data as $t) {
    $z = explode('=', $t);
    $x = [];
    $params[$z[0]] = $z[1];
}

file_put_contents('payments.log.txt', date('Y-m-d H:i:s') . " - callback # crypto: " . $request_body . "\r\n", FILE_APPEND);
file_put_contents('payments.log.txt', date('Y-m-d H:i:s') . " - callback # crypto TEST: " . $params['order_id'] . "\r\n", FILE_APPEND);

$con = mysqli_connect('localhost', 'wingate', 'd8F6E5Fy', 'wingate');
if (!$con) {
    die('Не удалось соединиться : ' . mysqli_error());
}

mysqli_set_charset($con, 'utf8');

mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

/*$params = [];
$params = json_decode($request_body, 1);*/

$out_summ = $params['amount_crypto'];

if ($out_summ <= 0) {
    echo "invalid_amount";
    return;
}

$invoice_id = $params['order_id'];
$c_invoice_id = $params['invoice_id'];
$tmp = explode('_', $invoice_id);

if (count($tmp) < 2) {
    echo "invalid_invoice_format";
    return;
}

$order_id = $tmp[0];
$order_id = str_replace('"', '', $order_id);
$client_id = $tmp[1];
$client_id = str_replace('"', '', $client_id);
$status = $params['status'];

$valid_statuses = ['paid', 'overpaid', 'success'];
if (!in_array($status, $valid_statuses)) {
    echo "payment_not_completed";
    return;
}

$res = mysqli_query($con, "SELECT * FROM `jcms2_moduleClients` WHERE `client_id` = '{$client_id}'");

if( $res == false ){ echo "database err1\n"; return; }
if($res->num_rows == 1 ){
    $data = mysqli_fetch_assoc($res);
    $out_summ = floatval($out_summ);
    $sql_code = "INSERT INTO `jcms2_moduleClientsBalance`(`client_id`, `summ`, `descr`) VALUES ('{$client_id}', '{$out_summ}', 'Пополнение через CryptoCloud <i>(транзакция №{$c_invoice_id})</i>')";
    $res = mysqli_query($con, $sql_code);
    if( $res == false ){ echo "database err3\n"; return; }

    echo "OK";
    return;
} else {
    echo "client_not_found";
    return;
}

?>