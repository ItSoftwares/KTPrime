<?
require "../conexao.php";
$dados = $_POST;

$result = [];

if (isset($dados['historico'])) $result = $dados['historico'];

if ($dados['funcao']=="pagar") {
    $pagamento = array();
    $pagamento['id_empresa'] = $result['id_empresa'];
    $pagamento['valor'] = $dados['valor'];
    $pagamento['descricao'] = "Pagamento referente ao mês {$result['mes']} do ano de {$result['ano']}";
    $pagamento['time'] = time();
    $pagamento['estado'] = 3;
    
    if ($dados['tipo']==0) {
        $pagamento['tipo'] = 3;
    } else {
        $pagamento['tipo'] = 4;
    }
    
    $temp=0;
    if ($result['id_pagamento']!=null) {
        $temp = DBselect("pagamento", "where id={$result['id_pagamento']}");
        $temp = $temp[0];
    }
    
    $id = DBcreate("pagamento", $pagamento);
    DBcreate("historico_pagamento", array("id_pagamento"=>$id, "id_historico"=>$result['id'], "time"=>time()));

//    if ($temp!=0 and $temp['tipo']==4) {
//        DBupdate("pagamento",array('id_proximo'=>$id) , "where id={$temp['id']}");
//    } else {
//        DBupdate("historico_servicos", array('id_pagamento'=> $id), "where id={$result['id']}");
//        DBupdate("servico_prestado", array('id_pagamento'=> $id, 'pago'=>1), "where id_historico={$result['id']}");
//    }

    //var_dump($pagamento);
    //exit;

    echo json_encode(array('estado'=>1, 'mensagem'=>"Pagamento manual criado com sucesso, iremos atualizar a página!"));
} 
else if ($dados['funcao']=="desfazer") {
    $id = $result['id_pagamento'];
    
    DBupdate("historico_servicos", array('id_pagamento'=> "NULL"), "where id_pagamento={$id}");
    DBupdate("servico_prestado", array('id_pagamento'=> "NULL", 'pago'=>0), "where id_pagamento={$id}");
    $temp = DBselect("pagamento", "where id={$result['id_pagamento']}", "id_proximo");
    if ($temp[0]['id_proximo']!=null) {
        DBdelete("pagamento", "where id={$id} or id={$temp[0]['id_proximo']}");
    } else {
        DBdelete("pagamento", "where id={$id}");
        
    }
    
    echo json_encode(array('estado'=>1, 'mensagem'=>"Pagamento desfeito com sucesso, iremos atualizar a página!"));
}
else if ($dados['funcao']=="desfazerNovo") {
    $id = $dados['id'];
    
    DBdelete("historico_pagamento", "where id_pagamento={$id}");
    DBupdate("servico_prestado", array('id_pagamento'=> "NULL", 'pago'=>0), "where id_pagamento={$id}");
    DBdelete("pagamento", "where id={$id}");
//    $temp = DBselect("pagamento", "where id={$result['id_pagamento']}", "id_proximo");
//    if ($temp[0]['id_proximo']!=null) {
//        DBdelete("pagamento", "where id={$id} or id={$temp[0]['id_proximo']}");
//    } else {
//    }
    
    echo json_encode(array('estado'=>1, 'mensagem'=>"Pagamento desfeito com sucesso, iremos atualizar a página!"));
}
else if ($dados['funcao']=="desconto") {
//     $id = $result['id_pagamento'];
    
    DBupdate("historico_servicos", array('desconto'=> $dados['desconto']), "where id={$dados['id']}");
    
    echo json_encode(array('estado'=>1, 'mensagem'=>"Desconto aplicado, iremos atualizar a página!"));
}
?>