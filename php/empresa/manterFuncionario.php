<?
require "../conexao.php";
require "../classes/empresa.class.php";

session_start();

$dados = DBescape($_POST);
$empresa = unserialize($_SESSION['empresa']);

if ($dados['operacao']=="novo") {
    unset($dados['operacao']);
    
    $dados['id_empresa'] = $empresa->id;
    
    DBcreate("funcionario_empresa", $dados);
    
    $funcionarios = DBselect("funcionario_empresa", "where id_empresa={$empresa->id} order by id DESC");
    
    echo json_encode(array('estado'=>1, 'mensagem'=> "Funcionario criado com sucesso!", 'funcionarios'=>$funcionarios));
    exit;
} else if ($dados['operacao']=="editar") {
    unset($dados['operacao']);
    
    $dados['id_empresa'] = $empresa->id;
    
    DBupdate("funcionario_empresa", $dados, "where id_empresa={$empresa->id} and id={$dados['id']}");
    
    $funcionarios = DBselect("funcionario_empresa", "where id_empresa={$empresa->id} order by id DESC");
    
    echo json_encode(array('estado'=>1, 'mensagem'=> "Funcionario atualizado com sucesso!", 'funcionarios'=>$funcionarios));
    exit;
} else if ($dados['operacao']=="excluir") {
    unset($dados['operacao']);
    
    DBdelete("funcionario_empresa", "where id={$dados['id']}");
    echo json_encode(array('estado'=>1, 'mensagem'=> "Funcionario removido com sucesso!"));
    exit;
}

?>