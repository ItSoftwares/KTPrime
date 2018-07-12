<?
require_once('config.php');
require_once('utils.php');
require_once('../conexao.php');
session_start();

$dados = DBescape($_POST);

$empresa = unserialize($_SESSION['empresa']);

$creditCardToken = htmlspecialchars($dados["token"]);
$senderHash = htmlspecialchars($dados["senderHash"]);

//$itemAmount = number_format($_POST["amount"], 2, '.', '');
//$shippingCoast = number_format($_POST["shippingCoast"], 2, '.', '');
$valor = number_format($dados["valor"], 2, '.', '');
$descricao = $dados['descricao'];

$params = array(
    'email'                     => $PAGSEGURO_EMAIL,  
    'token'                     => $PAGSEGURO_TOKEN,
    'creditCardToken'           => $creditCardToken,
    'senderHash'                => $senderHash,
    'receiverEmail'             => $PAGSEGURO_EMAIL,
    'paymentMode'               => 'default', 
    'paymentMethod'             => 'creditCard', 
    'currency'                  => 'BRL',
    // 'extraAmount'               => '1.00',
    'itemId1'                   => '0001',
    'itemDescription1'          => $dados['descricao'],  
    'itemAmount1'               => 1,  
    'itemQuantity1'             => 1,
    'reference'                 => $id_pagamento,
    'senderName'                => $empresa->razao_social,
    'senderCPF'                 => $empresa->cpf,
    'senderAreaCode'            => substr($empresa->telefone, 0, 2),
    'senderPhone'               => substr($empresa->telefone, 2),
    'senderEmail'               => $empresa->email,
    'shippingAddressStreet'     => $empresa->rua,
    'shippingAddressNumber'     => $empresa->numero,
    'shippingAddressDistrict'   => $empresa->bairro,
    'shippingAddressPostalCode' => $empresa->cep,
    'shippingAddressCity'       => $empresa->cidade,
    'shippingAddressState'      => $empresa->estado,
    'shippingAddressCountry'    => 'BRA',
    'installmentQuantity'       => 1,
    'installmentValue'          => $valor,
    'creditCardHolderName'      => $dados['titular'],
    'creditCardHolderCPF'       => $dados['cpf'],
    'creditCardHolderBirthDate' => $dados['aniversario'], // em formato 00/00/0000
    'creditCardHolderAreaCode'  => 83,
    'creditCardHolderPhone'     => substr($empresa->telefone, 0, 2),
    'billingAddressStreet'     => $empresa->rua,
    'billingAddressNumber'     => $empresa->numero,
    'billingAddressDistrict'   => $empresa->bairro,
    'billingAddressPostalCode' => $empresa->cep,
    'billingAddressCity'       => $empresa->cidade,
    'billingAddressState'      => $empresa->estado,
    'billingAddressCountry'    => 'BRA'
);

$header = array('Content-Type' => 'application/json; charset=UTF-8;');
$response = curlExec($PAGSEGURO_API_URL."/transactions", $params, $header);
$json = json_decode(json_encode(simplexml_load_string($response)));
?>
<!--
<h1>Pagseguro Test</h1>
<h3><?php echo 1 . ' x R$ ' .$valor;?></h3>
<h3>Code: <?php echo $json->code;?></h3>
<h3>Status: <?php echo $json->status;?></h3>
<p>Response: <?php print_r($json);  ?></p>-->
