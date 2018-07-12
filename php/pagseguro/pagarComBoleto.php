<?
require_once('config_pag.php');
require_once('utils.php');
require_once('../conexao.php');
require_once('../classes/empresa.class.php');
session_start();

$dados = DBescape($_POST);
$dados['ids'] = explode(",", $dados['ids']);
//var_dump($dados);
//exit;

$empresa = unserialize($_SESSION['empresa']);

$senderHash = htmlspecialchars($dados["senderHash"]);

$valor = number_format($dados["valor"], 2, '.', '');
$descricao = $dados['descricao'];

$pagamento = array(
    'id_empresa'=> $empresa->id,
    'tipo'=> 2,
    'valor'=> $valor,
    'descricao'=> $dados['descricao'],
    'time'=> time()
);

$id = DBcreate("pagamento", $pagamento);
$pagamento['id'] = $id;
//$pagamento = DBselect("pagamento", "where id_empresa={$empresa->id} order by id DESC limit 1");
//$pagamento = $pagamento[0];

$params = array(
    'email'                     => $PAGSEGURO_EMAIL,  
    'token'                     => $PAGSEGURO_TOKEN,
    'senderHash'                => $senderHash,
    'receiverEmail'             => $PAGSEGURO_EMAIL,
    'paymentMode'               => 'default',
    'paymentMethod'             => 'boleto', 
    'currency'                  => 'BRL',
    'itemId1'                   => '0001',
    'itemDescription1'          => $dados['descricao'],  
    'itemAmount1'               => $valor,  
    'itemQuantity1'             => 1,
//    'reference'                 => $id_pagamento,
    'reference'                 => $pagamento['id'],
    'senderName'                => "Empresa ".trim($empresa->razao_social),
    'senderCPF'                 => $empresa->cpf,
    'senderAreaCode'            => substr($empresa->telefone, 0, 2),
    'senderPhone'               => substr($empresa->telefone, 2),
    'senderEmail'               => $empresa->email,
//    'senderEmail'               => $SANDBOX_ENVIRONMENT?"c76311835793865313846@sandbox.pagseguro.com.br":"karina@ktprime.com.br",
    'shippingAddressStreet'     => $empresa->rua,
    'shippingAddressNumber'     => $empresa->numero,
    'shippingAddressDistrict'   => $empresa->bairro,
    'shippingAddressPostalCode' => $empresa->cep,
    'shippingAddressCity'       => $empresa->cidade,
    'shippingAddressState'      => $empresa->estado,
    'shippingAddressCountry'    => 'BRA',
    'notificationURL'          => "https://www.cnpj.net.br/php/pagseguro/notificacao.php"
);

$header = array('Content-Type' => 'application/json; charset=UTF-8;');
$response = curlExec($PAGSEGURO_API_URL."/transactions", $params, $header);
$json = json_decode(json_encode(simplexml_load_string($response)));

//$condicao = $dados['condicao'];
//$condicao2 = $dados['condicao2'];
//$condicao3 = $dados['condicao3'];

$criar = array();
foreach($dados['ids'] as $i) {
    array_push($criar, array('id_pagamento'=>$pagamento['id'], 'id_historico'=>$i, 'time'=>time()));
}

DBcreateVarios("historico_pagamento", $criar);
DBupdate("pagamento", array('codigo'=>$json->code, 'link_boleto'=>$json->paymentLink), "where id={$pagamento['id']}");
//DBupdate("historico_servicos", array('id_pagamento'=> $pagamento['id']), $condicao);
//DBupdate("servico_prestado", array('id_pagamento'=> $pagamento['id']), $condicao2);
//if ($condicao3!="where") DBupdate("pagamento", array('id_proximo'=>$pagamento['id']), $condicao3);

echo json_encode($json);
exit;
?>