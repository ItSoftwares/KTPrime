<?
require "../conexao.php";

$dados = $_POST;

$historico = $dados['historico'];

//$proximo = array('ano'=>0, 'mes'=> 0);

$teste = 1;
$mensagem = "Histórico do periodo foi apagado com sucesso, a página será atualizada!";

//if ($historico['mes']<12) {
//    $proximo['ano']=$historico['ano'];
//    $proximo['mes']=$historico['mes']+1;
//} else {
//    $proximo['ano']=$historico['ano']+1;
//    $proximo['mes']=1;
//}

$result = DBselect("historico_servicos", "where id_empresa={$historico['id_empresa']} and id>{$historico['id']}");

if (count($result)==0) {
    $teste = 0;
    $mensagem = "Para apagar um periodo a empresa deve ter um outro mais atual, espere até que ele seja criado no vencimento!";
}

if ($teste==1) {
    $result = DBselect("servico_prestado", "where id_historico={$historico['id']}");
    
    if (count($result)>0) {
        $teste = 0;
        $mensagem = "Serviços foram prestados nesse periodo, se quer apaga-lo, remova os serviços desse periodo!";
    }
}

if ($teste==1) {
    $result = DBselect("historico_pagamento", "where id_historico={$historico['id']}");

    if (count($result)>0) {
        $result = $result[0];
        $teste = 0;
        $mensagem = "Esse periodo recebeu um pagamento e por isso não pode ser apagado!";
    }
}

if ($teste==0) {
    echo json_encode(array('estado'=>2, 'mensagem'=>$mensagem));
} else {
    DBdelete("historico_servicos", "where id={$historico['id']}");
    echo json_encode(array('estado'=>1, 'mensagem'=>$mensagem));
}

exit;
?>