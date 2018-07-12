<?
require "../conexao.php";
require "../classes/empresa.class.php";

session_start();

// $dados = DBescape($_POST);
$dados = $_POST;

if ($dados['funcao']=="novo") {
    unset($dados['funcao']);
    // var_dump($dados); exit;
   $dados['id'] = DBcreate("mensal", $dados);
    
    echo json_encode(array('estado'=>1, 'mensagem'=> "Atividade mensal criada com sucesso!", 'mensal'=>$dados));
    exit;
} else if ($dados['funcao']=="editar") {
    unset($dados['funcao']);
    
    DBupdate("mensal", array('array'=>$dados['array']), "where id={$dados['id']}");
    
    echo json_encode(array('estado'=>1, 'mensagem'=> "Atualização salva!"));
    exit;
} else if ($dados['funcao']=="excluir") {
    // unset($dados['funcao']);
    
    // DBdelete("faturamento", "where id={$dados['id']}");
    // echo json_encode(array('estado'=>1, 'mensagem'=> "Faturamento removido com sucesso!"));
    // exit;
}

?>