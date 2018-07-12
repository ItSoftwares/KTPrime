<?
header("access-control-allow-origin: https://sandbox.pagseguro.uol.com.br");
header("access-control-allow-origin: https://pagseguro.uol.com.br");
require("config_pag.php");
require("utils.php");
require("../conexao.php");

//var_dump($_POST);

$notificationCode = $_POST['notificationCode'];
$url = $PAGSEGURO_API_URL."/transactions/notifications/".$notificationCode."?email=".$PAGSEGURO_EMAIL."&token=".$PAGSEGURO_TOKEN;
 
$curl = curl_init($url);
curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
$response = curl_exec($curl);
$http = curl_getinfo($curl);

if($response == 'Unauthorized'){
    print_r($response);
    exit;
}
curl_close($curl);
$response= json_decode(json_encode(simplexml_load_string($response)));

if(count($response->error) > 0){
    print_r($response);
    exit;
}

DBupdate("pagamento", array('estado'=>$response->status),"where id={$response->reference}");

//if ($response->status==3 or $response->status==4) {
//    DBupdate("servico_prestado", array('pago'=>1),"where id_pagamento={$response->reference}");
//}

//var_dump($response);
//$today = date("Y_m_d");
//$file = fopen("LogPagSeguro.$today.txt", "ab");
//$hour = date("H:i:s T");
//echo($file,"Log de Notificações e consulta\\\\r\\\\n");
//echo($file,"Hora da consulta: $hour \\\\r\\\\n");
//echo($file,"HTTP: ".$http['http_code']." \\\\r\\\\n");
//echo($file,"Código de Notificação:".$notificationCode." \\\\r\\\\n");
//echo($file, "Código da transação:".$response->code."\\\\r\\\\n");
//echo($file, "Status da transação:".$response->status."\\\\r\\\\n");
//echo($file,"____________________________________ \\\\r\\\\n");
//fclose($file);
?>