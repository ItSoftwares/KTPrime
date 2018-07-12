<?
date_default_timezone_set("America/Sao_Paulo");
require "../conexao.php";
require "../classes/empresa.class.php";
require "../vendor/autoload.php";
session_start();
$dados = $_POST;

$empresa = new Empresa();

if ($dados['funcao']=="nova") {
    unset($dados['funcao']);
    $dados['senha'] = $dados['senha_empresa'];
    unset($dados['senha_empresa']);
    
    $data = str_replace("/","-", $dados['validade_cd']);
    $data = strtotime($data);
    $dados['validade_cd'] = $data;
    $dados['validade_cd'] += 1000; 
    
    if (isset($dados['vencimento_alvara_1'])) {
        $data = str_replace("/","-",$dados['vencimento_alvara_1']);
        $data = strtotime($data);
        $dados['vencimento_alvara_1'] = $data;
        $dados['vencimento_alvara_1'] += 1000; 
    }
    
    if (isset($dados['vencimento_alvara_2'])) {
        $data = str_replace("/","-",$dados['vencimento_alvara_2']);
        $data = strtotime($data);
        $dados['vencimento_alvara_2'] = $data;
        $dados['vencimento_alvara_2'] += 1000; 
    }
    
    if (isset($dados['cliente_desde'])) $dados['cliente_desde'] = preg_replace('/[^a-z0-9]/i', '', $dados['cliente_desde']);
    if (isset($dados['cnpj'])) $dados['cnpj'] = preg_replace('/[^a-z0-9]/i', '', $dados['cnpj']);
    if (isset($dados['cpf'])) $dados['cpf'] = preg_replace('/[^a-z0-9]/i', '', $dados['cpf']);
    if (isset($dados['cpf_2'])) $dados['cpf_2'] = preg_replace('/[^a-z0-9]/i', '', $dados['cpf_2']);
    if (isset($dados['cpf_3'])) $dados['cpf_3'] = preg_replace('/[^a-z0-9]/i', '', $dados['cpf_3']);
    if (isset($dados['cpf_4'])) $dados['cpf_4'] = preg_replace('/[^a-z0-9]/i', '', $dados['cpf_4']);
    if (isset($dados['cep'])) $dados['cep'] = preg_replace('/[^a-z0-9]/i', '', $dados['cep']);
    if (isset($dados['telefone_1'])) $dados['telefone_1'] = preg_replace('/[^a-z0-9]/i', '', $dados['telefone_1']);
    if (isset($dados['telefone_2'])) $dados['telefone_2'] = preg_replace('/[^a-z0-9]/i', '', $dados['telefone_2']);
    if (isset($dados['telefone_3'])) $dados['telefone_3'] = preg_replace('/[^a-z0-9]/i', '', $dados['telefone_3']);
    if (isset($dados['celular_1'])) $dados['celular_1'] = preg_replace('/[^a-z0-9]/i', '', $dados['celular_1']);
    if (isset($dados['celular_2'])) $dados['celular_2'] = preg_replace('/[^a-z0-9]/i', '', $dados['celular_2']);
    if (isset($dados['celular_3'])) $dados['celular_3'] = preg_replace('/[^a-z0-9]/i', '', $dados['celular_3']);
    if (isset($dados['celular_4'])) $dados['celular_4'] = preg_replace('/[^a-z0-9]/i', '', $dados['celular_4']);
    if (isset($dados['celular_whatsapp'])) $dados['celular_whatsapp'] = preg_replace('/[^a-z0-9]/i', '', $dados['celular_whatsapp']);
    
    $mes = $dados['primeiro_mes'];
    $ano = $mes<date("n")?(date("Y")+1):date("Y");
    
    unset($dados['primeiro_mes']);
    
    $dados['hash'] = substr(time().time(), 0, 16);
    
    for ($i=1; $i<5; $i++) {
        $bancario = $dados['banco_'.$i]."..".$dados['agencia_'.$i]."..".$dados['conta_'.$i]."..".$dados['favorecido_'.$i];
        $banco = $dados['banco_'.$i];
        unset($dados['banco_'.$i]);
        unset($dados['agencia_'.$i]);
        unset($dados['conta_'.$i]);
        unset($dados['favorecido_'.$i]);
        
        if (!isset($banco) || $banco=="") continue;
        $dados['dados_banc_'.$i] = openssl_encrypt($bancario, "AES-256-CBC", $dados['hash'], 0, $dados['hash']);
    }
    
    $empresa->fromArray($dados);
    $result = $empresa->cadastrar();
    
    $nova = DBselect("empresa", "order by id DESC limit 1","id, razao_social, cnpj, telefone_1, estado_conta");
    $nova = $nova[0];
    $empresa = new Empresa();
    $empresa->fromArray($nova);
    $result['empresa'] = $empresa->toArray();
    
    // criar historico_servicos com mensalidade
    $mensalidade = $dados['mensalidade'];
    $historico = array();
    
    $dados = array('id_empresa'=> $empresa->id, 'mensalidade'=> $mensalidade, 'mes'=>$mes, 'ano'=>$ano);
    
    DBcreate("historico_servicos", $dados);
    
    echo json_encode($result); 
    exit;
} 
else if ($dados['funcao']=="editar") {
    unset($dados['funcao']);
    
    $data = str_replace("/","-",$dados['validade_cd']);
    $data = strtotime($data);
    $dados['validade_cd'] = $data;
    $dados['validade_cd'] += 1000;
    
    if (isset($dados['tipo_alvara_1']) and ($dados['tipo_alvara_1']!="" or $dados['tipo_alvara_1']!=null)) {
        $data = str_replace("/","-",$dados['vencimento_alvara_1']);
        $data = strtotime($data);
        $dados['vencimento_alvara_1'] = $data;
        $dados['vencimento_alvara_1'] += 1000; 
    }
    
    if (isset($dados['tipo_alvara_2']) and ($dados['tipo_alvara_2']!="" or $dados['tipo_alvara_2']!=null)) {
        $data = str_replace("/","-",$dados['vencimento_alvara_2']);
        $data = strtotime($data);
        $dados['vencimento_alvara_2'] = $data;
        $dados['vencimento_alvara_2'] += 1000; 
    }
    
    $temp = unserialize($_SESSION['empresa']);
    
    foreach($temp->toArray() as $key => $value) {
        if (isset($dados[$key]) && $dados[$key]==$value) {
            unset($dados[$key]);
        }
    }
    
    $dados['id'] = $temp->id;
    
    if (isset($dados['cliente_desde'])) $dados['cliente_desde'] = (string)preg_replace('/[^a-z0-9]/i', '', $dados['cliente_desde']);
    if (isset($dados['cnpj'])) $dados['cnpj'] = (string)preg_replace('/[^a-z0-9]/i', '', $dados['cnpj']);
    if (isset($dados['cpf'])) $dados['cpf'] = (string)preg_replace('/[^a-z0-9]/i', '', $dados['cpf']);
    if (isset($dados['cpf_2'])) $dados['cpf_2'] = (string)preg_replace('/[^a-z0-9]/i', '', $dados['cpf_2']);
    if (isset($dados['cpf_3'])) $dados['cpf_3'] = (string)preg_replace('/[^a-z0-9]/i', '', $dados['cpf_3']);
    if (isset($dados['cpf_4'])) $dados['cpf_4'] = (string)preg_replace('/[^a-z0-9]/i', '', $dados['cpf_4']);
    if (isset($dados['cep'])) $dados['cep'] = (string)preg_replace('/[^a-z0-9]/i', '', $dados['cep']);
    if (isset($dados['telefone_1'])) $dados['telefone_1'] = (string)preg_replace('/[^a-z0-9]/i', '', $dados['telefone_1']);
    if (isset($dados['telefone_2'])) $dados['telefone_2'] = (string)preg_replace('/[^a-z0-9]/i', '', $dados['telefone_2']);
    if (isset($dados['telefone_3'])) $dados['telefone_3'] = (string)preg_replace('/[^a-z0-9]/i', '', $dados['telefone_3']);
    if (isset($dados['celular_1'])) $dados['celular_1'] = (string)preg_replace('/[^a-z0-9]/i', '', $dados['celular_1']);
    if (isset($dados['celular_2'])) $dados['celular_2'] = (string)preg_replace('/[^a-z0-9]/i', '', $dados['celular_2']);
    if (isset($dados['celular_3'])) $dados['celular_3'] = (string)preg_replace('/[^a-z0-9]/i', '', $dados['celular_3']);
    if (isset($dados['celular_4'])) $dados['celular_4'] = (string)preg_replace('/[^a-z0-9]/i', '', $dados['celular_4']);
    if (isset($dados['celular_whatsapp'])) $dados['celular_whatsapp'] = (string)preg_replace('/[^a-z0-9]/i', '', $dados['celular_whatsapp']);
    
    if ($temp->hash==null or $temp->hash=="") {
        $dados['hash'] = substr(time().time(), 0, 16);
    } else {
        $dados['hash'] = $temp->hash;
    }
    
    for ($i=1; $i<5; $i++) {
        if (!isset($dados['banco_'.$i])) continue;
        $bancario = $dados['banco_'.$i]."..".$dados['agencia_'.$i]."..".$dados['conta_'.$i]."..".$dados['favorecido_'.$i];
        $banco = $dados['banco_'.$i];
        unset($dados['banco_'.$i]);
        unset($dados['agencia_'.$i]);
        unset($dados['conta_'.$i]);
        unset($dados['favorecido_'.$i]);
        
        if (!isset($banco) || $banco=="") continue;
        $dados['dados_banc_'.$i] = openssl_encrypt($bancario, "AES-256-CBC", $dados['hash'], 0, $dados['hash']);
    }
    
    $empresa->fromArray($dados);

    $result = $empresa->atualizar();
    
    foreach($dados as $key => $value) {
        $temp->$key = $value;
    }
    
    //atualizar mensalidade do historico atual
    if (array_key_exists("mensalidade", $dados)) {
        $historico = DBselect("historico_servicos", "where id_empresa={$empresa->id} order by id DESC limit 1");
        $historico = $historico[0];
        DBupdate("historico_servicos", array('mensalidade'=>$dados['mensalidade']), "where id = {$historico['id']}");
        $temp->mensalidade = $dados['mensalidade'];
    }
    
    // Se estiver habilitando empresa cria novo historico
    if (array_key_exists("estado_conta", $dados) and $dados['estado_conta']==1) {
        $historico = DBselect("historico_servicos", "where id_empresa={$empresa->id} order by id DESC limit 1");
        $historico = $historico[0];
        
        if ($historico['mes']!=date("n") and $historico['ano']!=date("Y")) {
            DBcreate("historico_servicos", array(
            'id_empresa' =>$temp->id,
            'ano' => date("n"),
            'mes' => date("Y"),
            'mensalidade' => $temp->mensalidade
            ));
        }
    }
    
    $result['empresa'] = $empresa->toArray();
    $_SESSION['empresa'] = serialize($temp);
    echo json_encode($result);
    exit;
} 
else if ($dados['funcao']=="bloquear") {
    unset($dados['funcao']);
    
    $empresa->fromArray($dados);
    $result = $empresa->atualizar();
    
    $temp = unserialize($_SESSION['empresa']);
    
    $temp->estado_conta = $dados['estado_conta'];
    $_SESSION['empresa'] = serialize($temp);
    echo json_encode($result);
    exit;
}
else if ($dados['funcao']=="mensagem") {
    unset($dados['funcao']);
    
    $empresa->fromArray($dados);
    $result = $empresa->mensagem();
    
    echo json_encode($result);
    exit;
}
else if ($dados['funcao']=="lembrar") {
    unset($dados['funcao']);
    
    $empresa->fromArray($dados['empresa']);
    $result = $empresa->lembrarSenha();
    
    echo json_encode($result);
    exit;
}
?>