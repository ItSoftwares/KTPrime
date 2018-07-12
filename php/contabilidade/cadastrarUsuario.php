<?php

require 'conexao.php';
require 'classes/empresa.class.php';
require_once('vendor/autoload.php');

if (isset($_POST)) {
    $_POST = DBescape($_POST);
    
    $usuario = new Empresa();
    $usuario->fromArray($_POST); 
    
    $result = $usuario->cadastrar();
    
//    var_dump($usuario->toArray());
//    echo 1;
    echo json_encode($result);
    exit;
}

?>