<?php

class Funcionario {
    private $props = [];
    public $valores_atualizar = array();
    
    public function cadastrar() {
        $senha_digitada = $this->senha;
        $result = DBselect('empresa', "where email = '{$this->email}'");
        $result2 = DBselect("contador", "where email = '{$this->email}'");
        $result3 = DBselect("funcionario", "where email = '{$this->email}'");
        
        if (isset($result3)) {
            $result3 = $result3[0];
            if ($this->email==$result3['email']) {
                return array('estado'=>2, 'mensagem'=>"Já existe algum usuário cadastrado com esse email!");
            }
        } else if (isset($result2)) {
            return array('estado'=>2, 'mensagem'=>"Este é o email do contador, forneça outro email!");
        } else if (isset($result)) {
            return array('estado'=>2, 'mensagem'=>"Este email já está sendo usado por um de seus clientes, forneça outro email!");
        } else {
                    
            $dados = array_filter($this->toArray());
            DBcreate('funcionario', $dados);
            
//            ENVIAR EMAIL DE CONFIRMAÇÃO DE CADASTRO
            
//            $url = sprintf("php/ativarConta.php?nome_cliente=%s&email=%s&hash=%s", $this->nome_cliente, $this->email, $this->hash);
            $url = $_SERVER['SERVER_NAME']."/login";

            $mensagem = file_get_contents("../../html/emailGeral.html");
            $mensagem2 = "Olá {$this->nome_cliente} sua empresa {$this->razao_social} acaba de ser cadastrada em nosso sistema, para acessar nossa plataforma por favor faça login, acessando o link abaixo!<br><br>";
            $mensagem2 = "Informações para Login:<br><br> <b>Email: </b><i>{$this->email}</i>";
            $mensagem2 .= "<br><b>Senha: </b><i>{$senha_digitada}</i>";
            $mensagem2 .= "<br>";
            $mensagem2 .= "<a href='".$url."'>Link para Login!</a>";

            $mensagem = str_replace("--MENSAGEM--", $mensagem2, $mensagem);

            $mail = new PHPMailer;
            
            $mail->SMTPOptions = array(
                'ssl' => array(
                    'verify_peer' => false,
                    'verify_peer_name' => false,
                    'allow_self_signed' => true
                )
            );

            $mail->addAddress($dados['email'], $dados['nome']);

            $mail->SMTPDebug = 0;                            // Enable verbose debug output
            
            $mail->isSMTP();                                      // Set mailer to use SMTP
            $mail->Host = 'br316.hostgator.com.br';  // Specify main and backup SMTP servers
            $mail->SMTPAuth = true;                               // Enable SMTP authentication
            $mail->Username = 'notificacoes_ktprime@cnpj.net.br';                 // SMTP username
            $mail->Password = '12345';                           // SMTP password
            $mail->SMTPSecure = 'ssl';                             // Enable TLS encryption, `ssl` also accepted
            $mail->Port = 465;                                  // TCP port to connect to

            $mail->setFrom('notificacoes_ktprime@cnpj.net.br', 'KTPrime');

            $mail->DEBUG = 0;
            $mail->Subject = 'Confirmação de Cadastro - KTPrime';
            $mail->isHTML(true);
            $mail->Body = $mensagem;
            $mail->CharSet = 'UTF-8';

            if (!$mail->send()) {
//                echo 'Message could not be sent.<pre>';
//                echo $mail->ErrorInfo;
            } else {
                $mail->ClearAllRecipients();
                
                return array('estado'=>1, 'mensagem'=>"Funcionario cadastrado com sucesso, enviamos um email para ele com suas informações!");
            }
        }
    }
    
    public function login() {
        if (filter_var($this->email, FILTER_VALIDATE_EMAIL)) {
            $result = DBselect('funcionario', "where email = '{$this->email}'");
            $result = $result[0];
            
            // Verifica se usuário está cadastrado
            if (!empty($result)) {        
                if ($result['estado_conta']==1) {
                    return array('estado'=>2, 'mensagem'=> "Sua conta está bloqueada!");
                    exit;
                }
                // Verifica se senha está correta
                if ($result['senha'] == $this->senha) {
                    unset($_SESSION['funcionario']);
                    // echo "empresa encontrado";

                    // CAPTURAR TODAS AS INFORMAÇÕES DO empresa
                    $dados = $result;
                    $this->fromArray($dados);
                    $this->valores_atualizar = array();

                    // PEGAR SERVICOS
                    $result = DBselect("solicitacao", "where id_empresa={$this->id} order by data");
                    $solicitacoes = $result;
                    if (count($solicitacoes)==0) {
                        $_SESSION['solicitacoes'] = array();
                    } else {
                        $_SESSION['solicitacoes'] = $solicitacoes;
                    }

                    // TEMPO PARA EXPIRAR SESSÃO
                    $_SESSION['expire'] = time();

                    // IDENTIFICAR SESSÃO
                    $_SESSION['donoSessao']=md5('sat'.$_SERVER['REMOTE_ADDR'].$_SERVER['HTTP_USER_AGENT']);
                    session_name($_SESSION['donoSessao']);

                    $_SESSION['funcionario'] = serialize($this);
                    return array('estado'=>5, 'mensagem'=> "OK");
                } else {
                    return array('estado'=>2, 'mensagem'=> "Senha incorreta para esta conta!");
                }
            } else {
                return array('estado'=>2, 'mensagem'=> "Credenciais inválidas!");
            }
        } else {
            return array('estado'=>2, 'mensagem'=> "Digite um email válido");
        }
    }
    
    public function atualizar() {
        $temp = $this->id;
        DBupdate("funcionario", $this->valores_atualizar, "where id={$temp}");
        $this->valores_atualizar = array();
        
        return array('estado'=>1, 'mensagem'=>"Funcionario atualizado com sucesso!");
    }
    
    public function excluir() {
        $temp = $this->id;
        DBdelete("funcionario", "where id={$temp}");
        
        return array('estado'=>1, 'mensagem'=> "Funcionario Excluido!");
    }
    
    public function toArray() {
        return $this->props;
    }
    
    public function fromArray($post) {
        foreach($post as $key => $value) {
            $this->props[$key] = $value;
            $this->valores_atualizar[$key] = $value;
        }
    }
    
    // Gets e Sets
    public function __get($name) {
        if (isset($this->props[$name])) {
            return $this->props[$name];
        } else {
            return false;
        }
    }

    public function __set($name, $value) {
        $this->props[$name] = $value;
        $this->valores_atualizar[$name] = $value;
    }
    
    public function __wakeup(){
        foreach (get_object_vars($this) as $k => $v) {
            $this->{$k} = $v;
        }
    }
}
?>