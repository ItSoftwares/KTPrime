<?
date_default_timezone_set("America/Sao_Paulo");
require "../conexao.php";
require "../classes/servico.class.php";
session_start();
$dados = DBescape($_POST);

$servico = new Servico();

if ($dados['funcao']=="excluir") {
    unset($dados['funcao']);
    $index = $dados['id_excluir'];
    unset($dados['id_excluir']);
    
    $servico->fromArray($dados);
    $result = $servico->excluir();
    
    unset($_SESSION['servicos'][$index]);
    
    echo json_encode($result);
    exit;
} 
else if ($dados['funcao']=="novo") {
    unset($dados['funcao']);
    
    $servico->fromArray($dados);
    $result = $servico->novo();
    
    $id = DBselect("servico", "order by id DESC limit 1","id");
    $id = $id[0]['id'];
    $servico->id = $id;
    $result['servico'] = $servico->toArray();
    array_push($_SESSION['servicos'], $servico->toArray());
    echo json_encode($result);
    exit;
} 
else if ($dados['funcao']=="editar") {
    unset($dados['funcao']);
    $index = $dados['id_editar'];
    unset($dados['id_editar']);
    
    $servico->fromArray($dados);
    $result = $servico->atualizar();
    
    DBupdate("servico_prestado", array("valor"=>$dados['valor'], "nome_servico"=>$dados['nome']), "where id_servico={$dados['id']}");
    
    $result['servico'] = $servico->toArray();
    $_SESSION['servicos'][$index] = $servico->toArray();
    echo json_encode($result);
    exit;
}
?>