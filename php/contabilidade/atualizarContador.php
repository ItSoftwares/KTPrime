<?php
require '../conexao.php';
require '../classes/contador.class.php';

if(!isset($_SESSION)){session_start();}

$dados = DBescape($_POST);
$u = unserialize($_SESSION['contador']);
$usuario = new Contador();

if (array_key_exists("senha", $_POST)) {
    if ($dados['senha']=="") {
        unset($dados['senha']);
        unset($dados['repetir_senha']);
    } else {
        $hash = time();
        $dados['senha'] = md5($_POST['senha'].$hash);
        $dados['hash'] = $hash;
        $dados['senha_temporaria'] = "";
        $dados['trocar_senha'] = 0;
        unset($dados['repetir_senha']);
    }
}

foreach ($dados as $key=> $value) {
    if ($u->$key==$value) {
        unset($dados[$key]);
    }
}
//var_dump($dados);
//exit;

$usuario->fromArray($dados);
$usuario->id = $u->id;

$result = $usuario->atualizar();

$u->fromArray($usuario->toArray());
$u->valores_atualizar = array();

$_SESSION['contador'] = serialize($u);

echo json_encode(array('estado'=>1, 'mensagem'=>"Dados atualizados com sucesso!", 'contador'=>$u->toArray()));
exit;
?>