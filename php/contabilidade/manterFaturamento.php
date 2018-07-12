<?
require "../conexao.php";
require "../classes/empresa.class.php";

session_start();

$dados = DBescape($_POST);
$empresa = unserialize($_SESSION['empresa']);

if ($dados['funcao']=="novo") {
    unset($dados['funcao']);
    
    $dados['id_empresa'] = $empresa->id;
    
    DBcreate("faturamento", $dados);
    
    $faturamentos = DBselect("faturamento", "where id_empresa={$empresa->id} order by ano, mes DESC");
    
    echo json_encode(array('estado'=>1, 'mensagem'=> "Faturamento criado com sucesso!", 'faturamentos'=>$faturamentos));
    exit;
} else if ($dados['funcao']=="editar") {
    unset($dados['funcao']);
    
    $dados['id_empresa'] = $empresa->id;
    
    DBupdate("faturamento", $dados, "where id_empresa={$empresa->id} and id={$dados['id']}");
    
    $faturamentos = DBselect("faturamento", "where id_empresa={$empresa->id} order by ano, mes DESC");
    
    echo json_encode(array('estado'=>1, 'mensagem'=> "Faturamento atualizado com sucesso!", 'faturamentos'=>$faturamentos));
    exit;
} else if ($dados['funcao']=="excluir") {
    unset($dados['funcao']);
    
    DBdelete("faturamento", "where id={$dados['id']}");
    echo json_encode(array('estado'=>1, 'mensagem'=> "Faturamento removido com sucesso!"));
    exit;
}

?>