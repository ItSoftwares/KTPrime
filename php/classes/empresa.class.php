<?php

class empresa {
    private $props = [];
    public $valores_atualizar = array();
    
    public function cadastrar() {
        $senha_digitada = $this->senha;
        
        $result = DBselect('empresa', "where email = '{$this->email}'");
        $result2 = DBselect("contador", "where email = '{$this->email}'");
        $result3 = DBselect("funcionario", "where email = '{$this->email}'");
        
        if (isset($result)) {
            $result = $result[0];
            if ($this->email==$result['email']) {
                return array('estado'=>2, 'mensagem'=>"Já existe algum usuário cadastrado com esse email!");
            } else if ($this->telefone==$result['telefone']) {
                return array('estado'=>2, 'mensagem'=>"Já existe algum usuário cadastrado com esse telefone!");
            }
        } else if (isset($result2)) {
            return array('estado'=>2, 'mensagem'=>"Este é o email do contador, forneça outro email!");
        } else if (isset($result3)) {
            return array('estado'=>2, 'mensagem'=>"Este email já está sendo usado por um de seus funcionários, forneça outro email!");
        } else {
                    
            $dados = array_filter($this->toArray());
            unset($dados['repetirSenha']);
            $dados['id'] = DBcreate('empresa', $dados);
            $this->fromArray($dados);
           // ENVIAR EMAIL DE CONFIRMAÇÃO DE CADASTRO
            
           // $url = sprintf("php/ativarConta.php?nome_cliente=%s&email=%s&hash=%s", $this->nome_cliente, $this->email, $this->hash);
            $url = "http://www.cnpj.net.br";

            $mensagem = file_get_contents("../../html/emailGeral.html");
            $mensagem2 = "Olá {$this->nome_cliente} sua empresa {$this->razao_social} acaba de ser cadastrada em nosso sistema, para acessar nossa plataforma por favor faça login, acessando o link abaixo!<br><br>";
            $mensagem2 = "Informações para Login:<br> <b>Email: </b><i>{$this->email}</i>";
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

            $mail->addAddress($dados['email'], $dados['nome_cliente']);

            $mail->SMTPDebug = 0;                            // Enable verbose debug output
            
            $mail->isSMTP();                                      // Set mailer to use SMTP
            $mail->Host = 'br316.hostgator.com.br';  // Specify main and backup SMTP servers
            $mail->SMTPAuth = true;                               // Enable SMTP authentication
            $mail->Username = 'notificacoes_ktprime@cnpj.net.br';                 // SMTP username
            $mail->Password = '12345';                           // SMTP password
            $mail->SMTPSecure = 'ssl';                            // Enable TLS encryption, `ssl` also accepted
            $mail->Port = 465;                                  // TCP port to connect to

            $mail->setFrom('notificacoes_ktprime@cnpj.net.br', 'KTPrime');

            $mail->DEBUG = 0;
            $mail->Subject = 'Confirmação de Cadastro - KTPrime';
            $mail->isHTML(true);
            $mail->Body = $mensagem;
            $mail->CharSet = 'UTF-8';
            
            $result = DBselect("empresa", "order by id DESC limit 1");
            $result = $result[0];
            
            $this->criarPastas();

            if (!$mail->send()) {
               // echo 'Message could not be sent.<pre>';
               // echo $mail->ErrorInfo;
            } else {
                $mail->ClearAllRecipients();
            }
            
            return array('estado'=>1, 'mensagem'=>"Empresa cadastrada com sucesso, enviamos um email para o cliente com as informações de login!");
        }
    }
    
    public function login() {
        if (filter_var($this->email, FILTER_VALIDATE_EMAIL)) {
            $result = DBselect('empresa', "where email = '{$this->email}'");
            $result = $result[0];
           // var_dump($result);
            // Verifica se usuário está cadastrado
            if (!empty($result)) {
                // Verifica se senha está correta
                if ($result['senha'] == $this->senha) {
                    
                    if ($result['estado_conta']==0) {
                        return array('estado'=>6, 'mensagem'=> "Sua conta está bloqueada!");
                    } else if ($result['estado_conta']==1) {
                        unset($_SESSION['empresa']);
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

                        $_SESSION['empresa'] = serialize($this);
                        return array('estado'=>4, 'mensagem'=> "OK");
                    }
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
        DBupdate("empresa", $this->valores_atualizar, "where id={$temp}");
        $this->valores_atualizar = array();
        
        return array('estado'=>1, 'mensagem'=>"Empresa atualizada com sucesso!");
    }
    
    public function mensagem() {
        $mensagem2 = $this->mensagem;
        
        $url = "http://www.cnpj.net.br";

        $mensagem = file_get_contents("../../html/emailGeral.html");

        $mensagem = str_replace("--MENSAGEM--", $mensagem2, $mensagem);

        $mail = new PHPMailer;

        $mail->SMTPOptions = array(
            'ssl' => array(
                'verify_peer' => false,
                'verify_peer_name' => false,
                'allow_self_signed' => true
            )
        );

        $mail->addAddress($this->email, $this->nome);

        $mail->SMTPDebug = 0;                            // Enable verbose debug output

        $mail->isSMTP();                                      // Set mailer to use SMTP
        $mail->Host = 'br316.hostgator.com.br';  // Specify main and backup SMTP servers
        $mail->SMTPAuth = true;                               // Enable SMTP authentication
        $mail->Username = 'notificacoes_ktprime@cnpj.net.br';                 // SMTP username
        $mail->Password = '12345';                           // SMTP password
        $mail->SMTPSecure = 'ssl';                            // Enable TLS encryption, `ssl` also accepted
        $mail->Port = 465;                                  // TCP port to connect to

        $mail->setFrom('notificacoes_ktprime@cnpj.net.br', 'KTPrime');

        $mail->DEBUG = 0;
        $mail->Subject = 'Mensagem da Contabilidade - KTPrime';
        $mail->isHTML(true);
        $mail->Body = $mensagem;
        $mail->CharSet = 'UTF-8';

        if (!$mail->send()) {
               // echo 'Message could not be sent.<pre>';
               // echo $mail->ErrorInfo;
        } else {
            $mail->ClearAllRecipients();
            
            return array('estado'=>1, 'mensagem'=> "Email enviado com sucesso!", 'empresa'=> $this->toArray());
        }
    }
    
    public function criarPastas() {
        error_reporting(E_ERROR | E_PARSE);
        // var_dump($this->toArray());
        $dirname = dirname(__DIR__, 2).DIRECTORY_SEPARATOR."servidor".DIRECTORY_SEPARATOR."empresa".DIRECTORY_SEPARATOR.$this->id.DIRECTORY_SEPARATOR;
        // echo $dirname;
        // exit;
        $pasta = $dirname."anexos".DIRECTORY_SEPARATOR;
        if (!is_dir($pasta) and !mkdir($pasta, 0777, true)) {
            echo "error";
        }

        $pasta = $dirname."solicitacoes".DIRECTORY_SEPARATOR;
        if (!is_dir($pasta) and !mkdir($pasta, 0777, true)) {
            echo "error";
        }

        $pasta = $dirname."guias".DIRECTORY_SEPARATOR;
        if (!is_dir($pasta) and !mkdir($pasta, 0777, true)) {
            echo "error";
        }

        $pasta = $dirname."privado".DIRECTORY_SEPARATOR."privado_contabilidade".DIRECTORY_SEPARATOR;
        if (!is_dir($pasta) and !mkdir($pasta, 0777, true)) {
            echo "error";
        }
        
        // Sócio
        $pasta = dirname."Documentos Sócio".DIRECTORY_SEPARATOR."Nome sócio".DIRECTORY_SEPARATOR."Documentos Pessoais".DIRECTORY_SEPARATOR;
        if (!is_dir($pasta) and !mkdir($pasta, 0777, true)) {
            echo "error";
        }
        
        $pasta = $dirname."Documentos Sócio".DIRECTORY_SEPARATOR."Nome sócio".DIRECTORY_SEPARATOR."Recibo Pro labore".DIRECTORY_SEPARATOR;
        if (!is_dir($pasta) and !mkdir($pasta, 0777, true)) {
            echo "error";
        }
        
        $pasta = $dirname."Documentos Sócio".DIRECTORY_SEPARATOR."Nome sócio".DIRECTORY_SEPARATOR."Folha Pro labore".DIRECTORY_SEPARATOR;
        if (!is_dir($pasta) and !mkdir($pasta, 0777, true)) {
            echo "error";
        }
        
        $pasta = $dirname."Documentos Sócio".DIRECTORY_SEPARATOR."Nome sócio".DIRECTORY_SEPARATOR."Imposto de Renda".DIRECTORY_SEPARATOR;
        if (!is_dir($pasta) and !mkdir($pasta, 0777, true)) {
            echo "error";
        }
        
        // Empresa
        $pasta = $dirname."Documentos da Empresa".DIRECTORY_SEPARATOR."Documentos empresa".DIRECTORY_SEPARATOR;
        if (!is_dir($pasta) and !mkdir($pasta, 0777, true)) {
            echo "error";
        }
        
        $pasta = $dirname."Documentos da Empresa".DIRECTORY_SEPARATOR."Certidão Negativa".DIRECTORY_SEPARATOR;
        if (!is_dir($pasta) and !mkdir($pasta, 0777, true)) {
            echo "error";
        }
        
        $pasta = $dirname."Documentos da Empresa".DIRECTORY_SEPARATOR."Protocolos de Entrega".DIRECTORY_SEPARATOR;
        if (!is_dir($pasta) and !mkdir($pasta, 0777, true)) {
            echo "error";
        }
        
        $pasta = $dirname."Documentos da Empresa".DIRECTORY_SEPARATOR."Sindicato".DIRECTORY_SEPARATOR;
        if (!is_dir($pasta) and !mkdir($pasta, 0777, true)) {
            echo "error";
        }
        
        $pasta = $dirname."Documentos da Empresa".DIRECTORY_SEPARATOR."Declaração de Faturamento".DIRECTORY_SEPARATOR;
        if (!is_dir($pasta) and !mkdir($pasta, 0777, true)) {
            echo "error";
        }
        
        $pasta = $dirname."Documentos da Empresa".DIRECTORY_SEPARATOR."Sefip Gfip".DIRECTORY_SEPARATOR;
        if (!is_dir($pasta) and !mkdir($pasta, 0777, true)) {
            echo "error";
        }
        
        $pasta = $dirname."Documentos da Empresa".DIRECTORY_SEPARATOR."Relatório de notas fiscais emitidas".DIRECTORY_SEPARATOR;
        if (!is_dir($pasta) and !mkdir($pasta, 0777, true)) {
            echo "error";
        }
        
        $pasta = $dirname."Documentos da Empresa".DIRECTORY_SEPARATOR."Declarações Anuais".DIRECTORY_SEPARATOR."DIRF".DIRECTORY_SEPARATOR;
        if (!is_dir($pasta) and !mkdir($pasta, 0777, true)) {
            echo "error";
        }
        
        $pasta = $dirname."Documentos da Empresa".DIRECTORY_SEPARATOR."Declarações Anuais".DIRECTORY_SEPARATOR."RAIS".DIRECTORY_SEPARATOR;
        if (!is_dir($pasta) and !mkdir($pasta, 0777, true)) {
            echo "error";
        }
        
        $pasta = $dirname."Documentos da Empresa".DIRECTORY_SEPARATOR."Declarações Anuais".DIRECTORY_SEPARATOR."DEFIS".DIRECTORY_SEPARATOR;
        if (!is_dir($pasta) and !mkdir($pasta, 0777, true)) {
            echo "error";
        }

        $pasta = $dirname."Documentos da Empresa".DIRECTORY_SEPARATOR."Informe de rendimento IRPF".DIRECTORY_SEPARATOR;
        if (!is_dir($pasta) and !mkdir($pasta, 0777, true)) {
            echo "error";
        }
        
        // funcionários
        $pasta = $dirname."Documentos Funcionários".DIRECTORY_SEPARATOR."Recibo Holerite".DIRECTORY_SEPARATOR;
        if (!is_dir($pasta) and !mkdir($pasta, 0777, true)) {
            echo "error";
        }
        
        $pasta = $dirname."Documentos Funcionários".DIRECTORY_SEPARATOR."Folha de Pagamento".DIRECTORY_SEPARATOR;
        if (!is_dir($pasta) and !mkdir($pasta, 0777, true)) {
            echo "error";
        }
        
        $pasta = $dirname."Documentos Funcionários".DIRECTORY_SEPARATOR."Relatório de férias".DIRECTORY_SEPARATOR;
        if (!is_dir($pasta) and !mkdir($pasta, 0777, true)) {
            echo "error";
        }
        
        $pasta = $dirname."Documentos Funcionários".DIRECTORY_SEPARATOR."Nome funcionario".DIRECTORY_SEPARATOR."Documentos pessoais".DIRECTORY_SEPARATOR;
        if (!is_dir($pasta) and !mkdir($pasta, 0777, true)) {
            echo "error";
        }
        
        $pasta = $dirname."Documentos Funcionários".DIRECTORY_SEPARATOR."Nome funcionario".DIRECTORY_SEPARATOR."Recibo de férias".DIRECTORY_SEPARATOR;
        if (!is_dir($pasta) and !mkdir($pasta, 0777, true)) {
            echo "error";
        }
        
        $pasta = $dirname."Documentos Funcionários".DIRECTORY_SEPARATOR."Nome funcionario".DIRECTORY_SEPARATOR."Documentos Admissionais".DIRECTORY_SEPARATOR;
        if (!is_dir($pasta) and !mkdir($pasta, 0777, true)) {
            echo "error";
        }
        
        $pasta = $dirname."Documentos Funcionários".DIRECTORY_SEPARATOR."Nome funcionario".DIRECTORY_SEPARATOR."Documentos Demissionais".DIRECTORY_SEPARATOR;
        if (!is_dir($pasta) and !mkdir($pasta, 0777, true)) {
            echo "error";
        }
        
        $pasta = $dirname."Documentos Funcionários".DIRECTORY_SEPARATOR."Nome funcionario".DIRECTORY_SEPARATOR."Imposto de renda".DIRECTORY_SEPARATOR;
        if (!is_dir($pasta) and !mkdir($pasta, 0777, true)) {
            echo "error";
        }
        
        // OCULTOS AO CLIENTE
        $pasta = $dirname."privado".DIRECTORY_SEPARATOR."Documentos extras pessoais".DIRECTORY_SEPARATOR;
        if (!is_dir($pasta) and !mkdir($pasta, 0777, true)) {
            echo "error";
        }
        
        $pasta = $dirname."privado".DIRECTORY_SEPARATOR."Documentos extras funcionarios".DIRECTORY_SEPARATOR;
        if (!is_dir($pasta) and !mkdir($pasta, 0777, true)) {
            echo "error";
        }
        
        $pasta = $dirname."privado".DIRECTORY_SEPARATOR."Documentos extras empresa".DIRECTORY_SEPARATOR;
        if (!is_dir($pasta) and !mkdir($pasta, 0777, true)) {
            echo "error";
        }
        
        $pasta = $dirname."privado".DIRECTORY_SEPARATOR."Prefeitura".DIRECTORY_SEPARATOR;
        if (!is_dir($pasta) and !mkdir($pasta, 0777, true)) {
            echo "error";
        }
        
        $pasta = $dirname."privado".DIRECTORY_SEPARATOR."Certidão negativa geral".DIRECTORY_SEPARATOR;
        if (!is_dir($pasta) and !mkdir($pasta, 0777, true)) {
            echo "error";
        }
        
        // Exclusivo a Karina
        $pasta = $dirname."privado".DIRECTORY_SEPARATOR."privado_contabilidade".DIRECTORY_SEPARATOR."Contabilidade Contrato".DIRECTORY_SEPARATOR;
        if (!is_dir($pasta) and !mkdir($pasta, 0777, true)) {
            echo "error";
        }
        
        $pasta = $dirname."privado".DIRECTORY_SEPARATOR."privado_contabilidade".DIRECTORY_SEPARATOR."Recibos".DIRECTORY_SEPARATOR;
        if (!is_dir($pasta) and !mkdir($pasta, 0777, true)) {
            echo "error";
        }
    }
    
    public function lembrarSenha() {
        $url = "http://www.cnpj.net.br";
        
        $mensagem = "Olá {$this->nome_cliente} sua empresa {$this->razao_social} já está cadastrada em nosso sistema, para acessar nossa plataforma por favor faça login, acessando o link abaixo!<br><br>";
        $mensagem = "Informações para Login:<br> <b>Email: </b><i>{$this->email}</i>";
        $mensagem .= "<br><b>Senha: </b><i>{$this->senha}</i>";
        $mensagem .= "<br>";
        $mensagem .= "<a href='".$url."'>Link para Login!</a>";
        
        
        $this->mensagem = $mensagem;
        return $this->mensagem();
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