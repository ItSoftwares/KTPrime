<?
date_default_timezone_set("America/Sao_Paulo");
require "../conexao.php";
require "../classes/funcionario.class.php";
require "../vendor/autoload.php";
session_start();
$dados = DBescape($_POST);

$funcionario = new Funcionario();

if ($dados['operacao']=="novo") {
    unset($dados['operacao']);
    
    $funcionario->fromArray($dados);
//    $result = array('estado'=>1, 'mensagem'=>"Funcionario cadastrado com sucesso, enviamos um email para ele com suas informações!");
    $result = $funcionario->cadastrar();

    if ($result['estado']!=2) {
        $nova = DBselect("funcionario", "order by id DESC limit 1");
        $nova = $nova[0];
        $funcionario = new Funcionario();
        $funcionario->fromArray($nova);
        $result['funcionario'] = $funcionario->toArray();    
    }
    
    echo json_encode($result);
    exit;
} 
else if ($dados['operacao']=="editar") {
    unset($dados['operacao']);
        
    $funcionario->fromArray($dados);
    $result = $funcionario->atualizar();
    
    $result['funcionario'] = $funcionario->toArray();
    echo json_encode($result);
    exit;
} 
else if ($dados['operacao']=="estado") {
    unset($dados['operacao']);
        
    $funcionario->fromArray($dados);
    $result = $funcionario->atualizar();
    
    $result['funcionario'] = $funcionario->toArray();
    echo json_encode($result);
    exit;
}
else if ($dados['operacao']=="excluir") {
    unset($dados['operacao']);
        
    $funcionario->fromArray($dados);
    $result = $funcionario->excluir();
    
    echo json_encode($result);
    exit;
}
?>