<?
date_default_timezone_set("America/Sao_Paulo");
require "../conexao.php";
require "../listarArquivos.php";
require "../classes/empresa.class.php";
session_start();

$empresa = unserialize($_SESSION['empresa']);

$dados = $_POST;
$extra="";

if ($dados['funcao']=="excluir") {
    unset($dados['funcao']);
    
    $dirname = realpath("../..".DIRECTORY_SEPARATOR."servidor".DIRECTORY_SEPARATOR."empresa".DIRECTORY_SEPARATOR.$empresa->id.DIRECTORY_SEPARATOR).$dados['caminho'];
    
    if (is_dir($dirname)) {
        rmdir_recursive($dirname);
    } else {
        unlink($dirname);
    }
    if (isset($dados['extra'])) $extra = DIRECTORY_SEPARATOR.$dados['extra'];
    $lista = listar(realpath("../..".DIRECTORY_SEPARATOR."servidor".DIRECTORY_SEPARATOR."empresa".DIRECTORY_SEPARATOR.$empresa->id.$extra));
    $lista = utf8ize($lista);
    
    echo json_encode(array('estado'=>1, 'mensagem'=>"Exclusão realizada com sucesso!", 'lista'=>$lista));
    exit;
}
else if ($dados['funcao']=="criar_pasta") {
    unset($dados['funcao']);
    
    $uploadOk = 1;
    
    if ($dados['nome']=="anexos") {
        $mensagem = "Por favor escolha outro nome!";
        $uploadOk = 0;
    }
    
    $dirname="";
    
    $dirname = realpath("../..".DIRECTORY_SEPARATOR."servidor".DIRECTORY_SEPARATOR."empresa".DIRECTORY_SEPARATOR.$empresa->id.DIRECTORY_SEPARATOR).$dados['caminho'].DIRECTORY_SEPARATOR.$dados['nome'];
        
    if (!file_exists($dirname)) {
        if (!mkdir($dirname, 0777, true)) {
            $mensagem = "Erro ao criar pasta!";
            $uploadOk = 0;
        }
    } else {
        $mensagem = "Já existe uma pasta com esse nome!";
        $uploadOk = 0;
    }

    // FINALIZAR UPLOAD
    if ($uploadOk == 0) {
        $result = array('estado'=>2, 'mensagem'=> $mensagem);
    } else {
        $result = array('estado'=>1, 'mensagem'=> "Pasta criada com sucesso!");
    }
    if (isset($dados['extra'])) $extra = DIRECTORY_SEPARATOR.$dados['extra'];
    $lista = listar(realpath("../..".DIRECTORY_SEPARATOR."servidor".DIRECTORY_SEPARATOR."empresa".DIRECTORY_SEPARATOR.$empresa->id.$extra));
    $lista = utf8ize($lista);
    $result['lista'] = $lista;
    
    echo json_encode($result);
    exit;
}
else if ($dados['funcao']=="renomear") {
    unset($dados['funcao']);
    
    $dirname = realpath("../..".DIRECTORY_SEPARATOR."servidor".DIRECTORY_SEPARATOR."empresa".DIRECTORY_SEPARATOR.$empresa->id.DIRECTORY_SEPARATOR).$dados['caminho'].$dados['nome'];
    
    $novo = realpath("../..".DIRECTORY_SEPARATOR."servidor".DIRECTORY_SEPARATOR."empresa".DIRECTORY_SEPARATOR.$empresa->id.DIRECTORY_SEPARATOR).$dados['caminho'].$dados['novo_nome'];
    
    if (file_exists($novo)) {
        echo json_encode(array('estado'=>2, 'mensagem'=>"Nome já existe!"));
        exit;
    }
    
    rename($dirname, $novo);
    if (isset($dados['extra'])) $extra = DIRECTORY_SEPARATOR.$dados['extra'];
    $lista = listar(realpath("../..".DIRECTORY_SEPARATOR."servidor".DIRECTORY_SEPARATOR."empresa".DIRECTORY_SEPARATOR.$empresa->id.$extra));
    $lista = utf8ize($lista);
    
    echo json_encode(array('estado'=>1, 'mensagem'=>"Exclusão realizada com sucesso!", 'lista'=>$lista));
    exit;
}
else if ($dados['funcao']=="mover") {
    unset($dados['funcao']);
    
    $dirname = realpath("../..".DIRECTORY_SEPARATOR."servidor".DIRECTORY_SEPARATOR."empresa".DIRECTORY_SEPARATOR.$empresa->id.DIRECTORY_SEPARATOR).$dados['antigo'];
    
    $novo = realpath("../..".DIRECTORY_SEPARATOR."servidor".DIRECTORY_SEPARATOR."empresa".DIRECTORY_SEPARATOR.$empresa->id.DIRECTORY_SEPARATOR).$dados['novo'];
    
    rename($dirname, $novo);
    if (isset($dados['extra'])) $extra = DIRECTORY_SEPARATOR.$dados['extra'];
    $lista = listar(realpath("../..".DIRECTORY_SEPARATOR."servidor".DIRECTORY_SEPARATOR."empresa".DIRECTORY_SEPARATOR.$empresa->id.$extra));
    $lista = utf8ize($lista);
    
    echo json_encode(array('estado'=>1, 'mensagem'=>"Exclusão realizada com sucesso!", 'lista'=>$lista));
    exit;
}
else if($dados['funcao']=="upload") {
    header("content-type: image/your_image_type");
    
    $qtd = count($_FILES['arquivo']['name']);

    $dirname = realpath("../..".DIRECTORY_SEPARATOR."servidor".DIRECTORY_SEPARATOR."empresa".DIRECTORY_SEPARATOR.$empresa->id.DIRECTORY_SEPARATOR).$dados['caminho'];
    
    $arquivos=array();
    
    for ($i=0; $i<$qtd; $i++) {
        $target_file = $dirname .DIRECTORY_SEPARATOR. basename($_FILES["arquivo"]["name"][$i]);
        $imageFileType = pathinfo($target_file, PATHINFO_EXTENSION);

        $target_file = $dirname .DIRECTORY_SEPARATOR. removerCaracteres($_FILES['arquivo']['name'][$i]);
        $uploadOk = 1;

        // VERIFICAR SE arquivo JÁ EXISTE
        if (file_exists($target_file)) {
            $mensagem = "Já existe um arquivo com o nome {$_FILES['arquivo']['name'][$i]}!";
            $uploadOk = 0;
        }

        // VERIFICAR TAMANHO DA IMAGEM
        if ($_FILES["arquivo"]["size"][$i] > 3000000) {
            $mensagem = "Tamanho máximo permitido é de 3Mb";
            $uploadOk = 0;
        }

        // FINALIZAR UPLOAD
        if ($uploadOk == 0) {
            json_encode(array('estado'=>2, 'mensagem'=> $mensagem));
            break;
        // SE ESTIVER TUDO CERTO FAZ O UPLOAD
        } else {
            if (move_uploaded_file($_FILES["arquivo"]["tmp_name"][$i], $target_file)) {
                // TUDO OK
                array_push($arquivos, pathinfo($target_file));
            } else {
                $mensagem =  "Desculpe, ocorreu um erro ao enviar o arquivo!";
                $result = array('estado'=>2, 'mensagem'=> $mensagem);
            }
        }
    }
    
    $lista = listar(realpath("../..".DIRECTORY_SEPARATOR."servidor".DIRECTORY_SEPARATOR."empresa".DIRECTORY_SEPARATOR.$empresa->id));
    $lista = utf8ize($lista);

    $result = array('estado'=>1, 'arquivos' => $arquivos, 'mensagem'=>"Arquivos enviadas com sucesso!", 'lista'=>$lista);
    
    echo json_encode($result);
    exit;
}

function rmdir_recursive($dir) {
    foreach(scandir($dir) as $file) {
        if ('.' === $file || '..' === $file) continue;
        if (is_dir("$dir/$file")) rmdir_recursive("$dir/$file");
        else unlink("$dir/$file");
    }
    rmdir($dir);
}
?>