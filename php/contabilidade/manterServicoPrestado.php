<?
date_default_timezone_set("America/Sao_Paulo");
require "../conexao.php";
require "../classes/servico_prestado.class.php";
require "../classes/empresa.class.php";
session_start();

$temp = "";
$dados = "";

if (isset($_POST['servicoPrestado'])) {
    $temp = $_POST['servicoPrestado'];
    unset($_POST['servicoPrestado']);
    $dados = DBescape($_POST);
} else {
    $dados = DBescape($_POST);
}

$empresa = unserialize($_SESSION['empresa']);
$servicoPrestado = new servicoPrestado();

if ($dados['funcao']=="excluir") {
    unset($dados['funcao']);
    
    $servicoPrestado->fromArray($dados);
    $result = $servicoPrestado->excluir();
    
    DBdelete("servico_mensal", "where id_empresa={$temp['id_empresa']} and id_servico={$temp['id_servico']}");
    
    if ($temp['tem_anexo']==1) {
        $dirname = realpath("../..".DIRECTORY_SEPARATOR."servidor".DIRECTORY_SEPARATOR."empresa".DIRECTORY_SEPARATOR.$empresa->id.DIRECTORY_SEPARATOR."anexos".DIRECTORY_SEPARATOR).DIRECTORY_SEPARATOR.date("Y", $temp['data']).DIRECTORY_SEPARATOR.$dados['id'].".".$temp['extensao'];
    
        unlink($dirname);
    }
    
    echo json_encode($result);
    exit;
}
else if ($dados['funcao']=="novo") {
    unset($dados['funcao']);
    unset($dados['anexo']);
    
    $ano = date("Y");
    
    $data = str_replace("/","-",$dados['data']);
    $data = strtotime($data);
    $dados['data'] = $data;
    $dados['data'] += 1000;
    $mes = date("n", $data);
    $ano = date("Y", $data); 
    
    $dirname="";
    $target_file="";
    $imageFileType="";
    if (count($_FILES)>0) {
        $dirname = realpath("../..".DIRECTORY_SEPARATOR."servidor".DIRECTORY_SEPARATOR."empresa".DIRECTORY_SEPARATOR.$empresa->id.DIRECTORY_SEPARATOR."anexos".DIRECTORY_SEPARATOR).DIRECTORY_SEPARATOR.$ano.DIRECTORY_SEPARATOR;
        
        $target_file = basename($_FILES["anexo"]["name"]);
        $imageFileType = pathinfo($target_file, PATHINFO_EXTENSION);
        $dados['extensao'] = $imageFileType;
        $dados["tem_anexo"] = 1;
    }
    
    $historico = DBselect("historico_servicos", "where id_empresa = {$empresa->id} and mes={$mes} and ano={$ano}")[0];
    
    if (count($historico)==0) {
        $historico = DBselect("historico_servicos", "where id_empresa = {$empresa->id} order by id DESC limit 1")[0];
    }
    $historico2 = DBselect("historico_servicos", "where id_empresa = {$empresa->id} order by id DESC limit 1")[0];
    
    $pagamento = DBselect("pagamento", "where id in (select id_pagamento from historico_pagamento where id_historico={$historico['id']})", "SUM(valor) as valor_total")[0];
    $servico = DBselect("servico_prestado", "where id_historico={$historico['id']}", "SUM(valor) as valor_total")[0];

    $restante = $historico['mensalidade']-$historico['desconto']-$pagamento['valor_total']+$servico['valor_total'];  
    
//     echo $restante; exit;
    
    if ($restante==0 and $pagamento['valor_total']!=0) $historico = $historico2;
    
//    $dados['data'] = time();
    $dados['id_empresa'] = $empresa->id;
    $dados['id_historico'] = $historico['id'];
    
    $servicoPrestado->fromArray($dados);
    $result = $servicoPrestado->novo();
    $nova = DBselect("servico_prestado", "where id_empresa = {$empresa->id} order by id DESC limit 1");
    $nova = $nova[0];
    $result['servicoPrestado'] = $nova;
    
    if (isset($dados["tem_anexo"])) {
        $uploadOk = 1;
        $target_file = $dirname . $nova['id'] .".". $imageFileType;
        
        if (!file_exists($dirname)) {
            if (!mkdir($dirname, 0755, true)) {
                echo "error";
            }
        }

        // VERIFICAR TAMANHO DA IMAGEM
        if ($_FILES["anexo"]["size"] > 5000000) {
            $mensagem = "Tamanho máximo permitido é de 5Mb";
            $uploadOk = 0;
        }

        // FINALIZAR UPLOAD
        if ($uploadOk == 0) {
            return array('estado'=>2, 'mensagem'=> $mensagem);
        // SE ESTIVER TUDO CERTO FAZ O UPLOAD
        } else {
            if (move_uploaded_file($_FILES["anexo"]["tmp_name"], $target_file)) {
                // TUDO OK
                
            } else {
                $mensagem =  "Desculpe, ocorreu um erro ao enviar imagem!";
                return array('estado'=>2, 'mensagem'=> $mensagem);
            }
        }    
    }
    
    echo json_encode($result);
    exit;
} 
else if ($dados['funcao']=="editar") {
    unset($dados['funcao']);
    
    $servicoPrestado->fromArray($dados);
    $result = $servicoPrestado->atualizar();
    
    echo json_encode($result);
    exit;
}
else if ($dados['funcao']=="adicionar") {
    unset($dados['funcao']);

    $novo = array('id_servico'=>$temp['id'], 'id_empresa'=>$temp['id_empresa']);
    DBcreate("servico_mensal", $novo);
    
    echo json_encode(array('estado'=>1, 'mensagem'=> "Este serviço será setado mensalmente automaticamente.", 'novo'=> $novo));
    exit;
}
else if ($dados['funcao']=="remover") {
    unset($dados['funcao']);
    
    DBdelete('servico_mensal', "where id_servico={$temp['id']} and id_empresa={$temp['id_empresa']}");
        
    echo json_encode(array('estado'=>1, 'mensagem'=> "Este serviço não será mais setado mensalmente."));
    exit;
}
?>