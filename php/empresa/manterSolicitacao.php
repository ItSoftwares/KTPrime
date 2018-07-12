<?
date_default_timezone_set("America/Sao_Paulo");
require "../conexao.php";
require "../classes/solicitacao.class.php";
require "../classes/empresa.class.php";

session_start();
$dados = DBescape($_POST);
if ($dados['funcao']!="responder") 
    $usuario = unserialize($_SESSION['empresa']);
$solicitacao = new solicitacao();

if ($dados['funcao']=="excluir") {
    unset($dados['funcao']);
    $index = $dados['id_excluir'];
    unset($dados['id_excluir']);
    
    $solicitacao->fromArray($dados);
    $result = $solicitacao->excluir();
    
    unset($_SESSION['solicitacoes'][$index]);
    
    echo json_encode($result);
    exit;
} 
else if ($dados['funcao']=="novo") {
    unset($dados['funcao']);
    unset($dados['anexo']);
    
    $ano = date("Y");
    
    $dirname="";
    $target_file="";
    $imageFileType="";
    if (count($_FILES)>0) {
        $dirname = realpath("../..".DIRECTORY_SEPARATOR."servidor".DIRECTORY_SEPARATOR."empresa".DIRECTORY_SEPARATOR.$usuario->id.DIRECTORY_SEPARATOR."solicitacoes".DIRECTORY_SEPARATOR).DIRECTORY_SEPARATOR.$ano.DIRECTORY_SEPARATOR;
        
        $target_file = basename($_FILES["anexo"]["name"]);
        $imageFileType = pathinfo($target_file, PATHINFO_EXTENSION);
        $dados['extensao'] = $imageFileType;
        $dados["tem_anexo"] = 1;
    }
    
    $dados['id_empresa'] = $usuario->id;
    $dados['data'] = time();
    $solicitacao->fromArray($dados);
    $result = $solicitacao->novo();
    
    $id = DBselect("solicitacao", "where id_empresa={$usuario->id} order by id DESC limit 1");
    $solicitacao->fromArray($id[0]);
    $result['solicitacao'] = $solicitacao->toArray();
    array_push($_SESSION['solicitacoes'], $solicitacao->toArray());
    
    if (isset($dados["tem_anexo"])) {
        $uploadOk = 1;
        $target_file = $dirname . $solicitacao->id .".". $imageFileType;
        
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
else if ($dados['funcao']=="responder") {
    unset($dados['funcao']);
    
    $solicitacao->fromArray($dados);
    
    $result = $solicitacao->atualizar();
    
    echo json_encode($result);
    exit;
}