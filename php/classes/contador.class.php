<?php

class Contador {
    private $props = [];
    public $valores_atualizar = array();
    
    public function cadastrar() {
        $senha = $this->senha;
        $this->hash = time();
        $this->senha = md5($this->senha.$this->hash);
        DBcreate("contador", $this->toArray());
        $this->senha = $senha;
        return $this->login();
    }
    
    public function login($existe=1) {
        if (filter_var($this->email, FILTER_VALIDATE_EMAIL)) {
            if ($existe!=1) {
//                return $this->cadastrar();
            }
            
            $result = DBselect('contador', "where email = '{$this->email}'");
            $result = $result[0];
            
            // Verifica se usuário está cadastrado
            if (!empty($result)) {        
                // Verifica se senha está correta
                if ($result['senha'] == md5($this->senha.$result['hash']) || $this->senha==$result['senha_temporaria']) {
                    
                     unset($_SESSION['contador']);
                    // echo "empresa encontrado";

                    // CAPTURAR TODAS AS INFORMAÇÕES DO CONTADOR
                    $dados = $result;
                    $this->fromArray($dados);
                    $this->valores_atualizar = array();
                    
                    // PEGAR LEMBRETES
                    $result = DBselect("lembrete", "order by data_inicio, data_validade DESC limit 100");
                    $lembretes = $result;
                    if (count($lembretes)==0) {
                        $_SESSION['lembretes'] = array();
                    } else {
                        $_SESSION['lembretes'] = $lembretes;
                    }
                    
                    // PEGAR SERVICOS
                    $result = DBselect("servico", "order by nome");
                    $servicos = $result;
                    if (count($servicos)==0) {
                        $_SESSION['servicos'] = array();
                    } else {
                        $_SESSION['servicos'] = $servicos;
                    }

                    // TEMPO PARA EXPIRAR SESSÃO 
                    $_SESSION['expire'] = time();

                    // IDENTIFICAR SESSÃO
                    $_SESSION['donoSessao']=md5('sat'.$_SERVER['REMOTE_ADDR'].$_SERVER['HTTP_USER_AGENT']);
                    session_name($_SESSION['donoSessao']);

                    $_SESSION['contador'] = serialize($this);
                    return array('estado'=>1, 'mensagem'=> "OK");
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
        DBupdate("contador", $this->valores_atualizar, "where id={$temp}");
        $this->valores_atualizar = array();
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