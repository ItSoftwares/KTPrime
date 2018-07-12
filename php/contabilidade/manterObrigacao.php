<?
date_default_timezone_set("America/Sao_Paulo");
require "../conexao.php";
require "../classes/obrigacao.class.php";
require "../classes/empresa.class.php";
session_start();

if (isset($_POST['obrigacao'])) {
    $temp = $_POST['obrigacao'];
    unset($_POST['obrigacao']);
    $dados = DBescape($_POST);
} else {
    $dados = DBescape($_POST);
}

$empresa = unserialize($_SESSION['empresa']);

$obrigacao = new Obrigacao();

if ($dados['funcao']=="excluir") {
    error_reporting(E_ERROR | E_PARSE);
    unset($dados['funcao']);
    $obrigacao->fromArray($dados);
    $result = $obrigacao->excluir();
    if ($temp['tem_anexo']==1) {
        $dirname = realpath("../..".DIRECTORY_SEPARATOR."servidor".DIRECTORY_SEPARATOR."empresa".DIRECTORY_SEPARATOR.$empresa->id.DIRECTORY_SEPARATOR."anexos".DIRECTORY_SEPARATOR).DIRECTORY_SEPARATOR.date("Y", $temp['data_realizar']).DIRECTORY_SEPARATOR.$dados['id'].".".$temp['extensao'];
        unlink($dirname);
    }
    echo json_encode($result);
    exit;
}
else if ($dados['funcao']=="novo") {
    unset($dados['funcao']);
    $data = str_replace("/","-",$dados['data_realizar']);
    $data = strtotime($data);
    $dados['data_realizar'] = intval($data)+3600;
    $ano = date("Y");
    $dados['time'] = time();
    $dados['id_empresa'] = $empresa->id;
    $dirname="";
    $target_file="";
    $imageFileType="";
    // var_dump($_FILES); exit;
    if (strlen($_FILES["image-upload"]["name"])>0) {
        $dirname = realpath("../..".DIRECTORY_SEPARATOR."servidor".DIRECTORY_SEPARATOR."empresa".DIRECTORY_SEPARATOR.$empresa->id.DIRECTORY_SEPARATOR."anexos".DIRECTORY_SEPARATOR).DIRECTORY_SEPARATOR.$ano.DIRECTORY_SEPARATOR;
        $target_file = basename($_FILES["image-upload"]["name"]);
        $imageFileType = pathinfo($target_file, PATHINFO_EXTENSION);
        $dados['extensao'] = $imageFileType;
        $dados["tem_anexo"] = 1;
    }
    $obrigacao->fromArray($dados);
    $result = $obrigacao->novo();
    $dados['id'] = $obrigacao->id;

    if (isset($dados["tem_anexo"])) {
        $uploadOk = 1;
        $target_file = $dirname . $dados['id'] .".". $imageFileType;
        if (!file_exists($dirname)) {
            if (!mkdir($dirname, 0755, true)) {
                echo "error";
            }
        }
        // VERIFICAR TAMANHO DA IMAGEM
        if ($_FILES["image-upload"]["size"] > 5000000) {
            $mensagem = "Tamanho máximo permitido é de 5Mb";
            $uploadOk = 0;
        }
        // FINALIZAR UPLOAD
        if ($uploadOk == 0) {
            $result = array('estado'=>2, 'mensagem'=> $mensagem);
            $obrigacao->excluir();
            echo json_encode($result);
            exit;
        // SE ESTIVER TUDO CERTO FAZ O UPLOAD
        } else {
            if (move_uploaded_file($_FILES["image-upload"]["tmp_name"], $target_file)) {
                // TUDO OK
            } else {
                $mensagem =  "Desculpe, ocorreu um erro ao enviar imagem!";
                $result = array('estado'=>2, 'mensagem'=> $mensagem);
                $obrigacao->excluir();
                echo json_encode($result);
                exit;
            }
        }    
    }

    $nova = DBselect("obrigacao", "where id_empresa = {$empresa->id} order by id DESC limit 1");
    $nova = $nova[0];
    $result['obrigacao'] = $nova;
    echo json_encode($result);
    exit;
} 
else if ($dados['funcao']=="editar") {
    unset($dados['funcao']);
    if (array_key_exists("anexo", $dados)) unset($dados['anexo']);
    if (array_key_exists("data_realizar", $dados)) {
        $data = str_replace("/","-",$dados['data_realizar']);
        $data = strtotime($data);
        $dados['data_realizar'] = intval($data)+3600;
    }
    $ano = date("Y", $dados['time']);
    $dirname="";
    $target_file="";
    $imageFileType="";
    if (count($_FILES)>0) {
        $dirname = realpath("../..".DIRECTORY_SEPARATOR."servidor".DIRECTORY_SEPARATOR."empresa".DIRECTORY_SEPARATOR.$empresa->id.DIRECTORY_SEPARATOR."anexos".DIRECTORY_SEPARATOR).DIRECTORY_SEPARATOR.$ano.DIRECTORY_SEPARATOR;
        $target_file = basename($_FILES["image-upload"]["name"]);
        $imageFileType = pathinfo($target_file, PATHINFO_EXTENSION);
        $dados['extensao'] = $imageFileType;
        $dados["tem_anexo"] = 1;
    }
    $obrigacao->fromArray($dados);
    $result = $obrigacao->atualizar();
    if (isset($dados["tem_anexo"])) {
        $uploadOk = 1;
        $target_file = $dirname . $dados['id'] .".". $imageFileType;
        if (!file_exists($dirname)) {
            if (!mkdir($dirname, 0755, true)) {
                echo "error";
            }
        }
        // VERIFICAR TAMANHO DA IMAGEM
        if ($_FILES["image-upload"]["size"] > 5000000) {
            $mensagem = "Tamanho máximo permitido é de 5Mb";
            $uploadOk = 0;
        }
        // FINALIZAR UPLOAD
        if ($uploadOk == 0) {
            return array('estado'=>2, 'mensagem'=> $mensagem);
        // SE ESTIVER TUDO CERTO FAZ O UPLOAD
        } else {
            if (move_uploaded_file($_FILES["image-upload"]["tmp_name"], $target_file)) {
                // TUDO OK
            } else {
                $mensagem =  "Desculpe, ocorreu um erro ao enviar imagem!";
                return array('estado'=>2, 'mensagem'=> $mensagem);
            }
        }    
    }
    $new = DBselect("obrigacao", "where id={$dados['id']}");
    $result['obrigacao'] = $new[0];
    echo json_encode($result);
    exit;
}
else if ($dados['funcao']=="finalizar") {
    unset($dados['funcao']);
    $obrigacao->fromArray($dados);

    $result = $obrigacao->atualizar();
    echo json_encode($result);
    exit;
}
?>