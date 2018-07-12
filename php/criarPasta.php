<?
require "conexao.php";
require "classes/empresa.class.php";

DBdelete("historico_servicos", "where ano=0");

// $empresas = DBselect("empresa", "", "id");

// echo "<pre>";
// var_dump($empresas);

// DBupdate("servico_prestado", array("gerado_mensal"=>1), "where id_historico in (select id from historico_servicos where mes=4 and ano=2018)");

// foreach($empresas as $emp) {
//     $it = new Empresa();
//     $it->fromArray($emp);
//     $it->criarPastas();
// } 

//var_dump(DBselect("historico_servicos", "group by id_empresa, mes, ano having count(*) > 1", "id_empresa, mes, ano, count(*)"));

exit;
?>