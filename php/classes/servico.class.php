<?php

class Servico {
    private $props = [];
    public $valores_atualizar = array();
    
    public function novo() {
        DBcreate("servico", $this->toArray());
        
        return array('estado'=>1, 'mensagem'=> "Serviço Criado com sucesso!");
    }
    
    public function atualizar() {
        $temp = $this->id;
        DBupdate("servico", $this->valores_atualizar, "where id={$temp}");
        $this->valores_atualizar = array();
        
        return array('estado'=>1, 'mensagem'=> "Serviço Atualizado!");
    }
    
    public function excluir() {
        $temp = $this->id;
        
        DBdelete("servico", "where id={$temp}");
        
        return array('estado'=>1, 'mensagem'=> "Serviço Excluido!");
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