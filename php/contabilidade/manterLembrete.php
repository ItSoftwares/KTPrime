<?
date_default_timezone_set("America/Sao_Paulo");
require "../conexao.php";
require "../classes/lembrete.class.php";
session_start();
$dados = DBescape($_POST);

$lembrete = new Lembrete();

if ($dados['funcao']=="excluir") {
    unset($dados['funcao']);
    $index = $dados['id_excluir'];
    unset($dados['id_excluir']);
    
    $lembrete->fromArray($dados);
    $result = $lembrete->excluir();
    
    unset($_SESSION['lembretes'][$index]);
    
    echo json_encode($result);
    exit;
}
else if ($dados['funcao']=="realizar") {
    unset($dados['funcao']);
    $index = $dados['id_editar'];
    unset($dados['id_editar']);
    
    $lembrete->fromArray($dados);
    $result = $lembrete->atualizar();
    
    $result['lembrete'] = $lembrete->toArray();
    $_SESSION['lembretes'][$index]['realizado'] = $lembrete->realizado;
    echo json_encode($result);
    exit;
}

$data = str_replace("/","-",$dados['data_inicio']);
$data = strtotime($data);
$dados['data_inicio'] = intval($data)+3600;

if ($dados['tipo']==1) {
    $data = str_replace("/","-",$dados['data_validade']);
    $data = strtotime($data);
    $dados['data_validade'] = $data;

    if ($dados['data_validade']<$dados['data_inicio']) {
        echo json_encode(array('estado'=>2,'mensagem'=>"Escolha uma data de validade maior do que a data de inicio!"));
        exit;
    }
} 
else {
    unset($dados['data_validade']);
    unset($dados['id_empresa']);
}

if ($dados['funcao']=="novo") {
    unset($dados['funcao']);
    
    $lembrete->fromArray($dados);
    $result = $lembrete->novo();
    
    $id = DBselect("lembrete", "order by id DESC limit 1","id");
    $id = $id[0]['id'];
    $lembrete->id = $id;
    $result['lembrete'] = $lembrete->toArray();
    array_push($_SESSION['lembretes'], $lembrete->toArray());
    echo json_encode($result);
    exit;
} 
else if ($dados['funcao']=="editar") {
    unset($dados['funcao']);
    $index = $dados['id_editar'];
    unset($dados['id_editar']);
    
    $lembrete->fromArray($dados);
    $result = $lembrete->atualizar();
    
    $result['lembrete'] = $lembrete->toArray();
    $_SESSION['lembretes'][$index] = $lembrete->toArray();
    echo json_encode($result);
    exit;
}
?>