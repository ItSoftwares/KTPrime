<?
require "../conexao.php";
$dados = $_POST;

$result = $dados['historico'];

if ($dados['funcao']=="pagar") {
    $pagamento = array();
    $pagamento['id_empresa'] = $result['id_empresa'];
    $pagamento['valor'] = $result['total'];
    $pagamento['tipo'] = 3;
    $pagamento['descricao'] = "Pagamento referente ao mês {$result['mes']} do ano de {$result['ano']}";
    $pagamento['time'] = time();
    $pagamento['estado'] = 3;

    DBcreate("pagamento", $pagamento);

    $id = DBselect("pagamento", "where id_empresa={$result['id_empresa']} order by time DESC limit 1", "id");
    $id = $id[0]['id'];

    //var_dump($pagamento);
    //exit;

    DBupdate("historico_servicos", array('id_pagamento'=> $id), "where id={$result['id']}");
    DBupdate("servico_prestado", array('id_pagamento'=> $id, 'pago'=>1), "where id_historico={$result['id']}");

    echo json_encode(array('estado'=>1, 'mensagem'=>"Pagamento manual criado com sucesso, iremos atualizar a página!"));
} else if ($dados['funcao']=="desfazer") {
    $id = $result['id_pagamento'];
    
    DBupdate("historico_servicos", array('id_pagamento'=> "NULL"), "where id_pagamento={$id}");
    DBupdate("servico_prestado", array('id_pagamento'=> "NULL", 'pago'=>0), "where id_pagamento={$id}");
    
    DBdelete("pagamento", "where id={$id}");
    
    echo json_encode(array('estado'=>1, 'mensagem'=>"Pagamento desfeito com sucesso, iremos atualizar a página!"));
}
?>