<?
set_include_path('/home/cnpjn035/public_html/php/');
require "conexao.php";
// echo "teste";
$empresas = DBselect("empresa", "where estado_conta<>0 order by id DESC", "id, mensalidade, dia_vencimento");
$historico = DBselect("historico_servicos h INNER JOIN empresa e on h.id_empresa = e.id", "where e.estado_conta<>0 order by ano DESC, mes DESC", "h.id, h.id_empresa, h.mes, h.ano");
$servicos_mensais = DBselect("servico_mensal", "order by id_empresa DESC");
$servicos_prestados = DBselect("servico_prestado s INNER JOIN servico_mensal m ON s.id = m.id_servico", "", "s.*, m.id as id_mensal");
 
echo "<pre>";
 
// LIMPAR HISTORICO E DEIXA SOMENTE UM MÊS DE CADA EMPRESA
$tem=[];
$new=[];
foreach($historico as $his) {
	if (!in_array($his['id_empresa'], $tem)) {
		array_push($tem, $his['id_empresa']);
		array_push($new, $his);
	}
}
$historico=$new;
echo "Quantidade: ".count($historico)."<br>";

$temp = array();

foreach($empresas as $emp) { 
	foreach($historico as $his) {
		if ($emp['id']==$his['id_empresa']) {
			$temp[$emp['id']] = $his;
			break;
		}
	}
}
 
$historico = $temp;
// var_dump($historico);
// var_dump($empresas);
// exit;
$novos = [];
$tem = [];
$hoje = strtotime(date("d-n-Y"), time());
$mesAtual = date("n");
 
foreach($empresas as $emp) {
	$mes = $historico[$emp['id']]['mes'];
	$ano = $historico[$emp['id']]['ano'];
	$temp = "01-".($mes+1==13?1:$mes+1)."-".($mes+1==13?$ano+1:$ano);

	// var_dump(strtotime($temp)<$hoje);
	
	if (strtotime($temp)<=$hoje) {
		$dados = array('id_empresa'=> $emp['id'], 'mensalidade'=> $emp['mensalidade'], 'mes'=> ($mes+1==13?1:$mes+1), 'ano'=>($mes+1==13?$ano+1:$ano));
		// var_dump($dados);
		// echo "teste<br>";
 
		$novos[$emp['id']] = DBcreate("historico_servicos", $dados);
		array_push($tem, $emp['id']);
	}
} 

// exit;
 
//var_dump($novos);
//var_dump($tem);
 
//exit;
 
if (count($servicos_prestados)>0) {
	foreach($servicos_prestados as $serv) {
		if (!in_array($serv['id_empresa'], $tem)) continue;
		$novo = [];
 
		$novo['data'] = time();
		$novo['id_empresa'] = $serv['id_empresa'];
		$novo['id_servico'] = $serv['id_servico'];
		$novo['valor'] = $serv['valor'];
		$novo['descricao'] = $serv['descricao'];
		$novo['nome_servico'] = $serv['nome_servico'];
		$novo['id_historico'] = $novos[$serv['id_empresa']];
		$novo['gerado_mensal'] = 1;
		 
		$id = DBcreate("servico_prestado", $novo);
		echo $id."<br>";
		DBupdate("servico_mensal", array('id_servico'=>$id), "where id={$serv['id_mensal']}");
	}
} else exit; 


// $empresas = DBselect("empresa", "order by id DESC", "id, mensalidade, dia_vencimento");
// $historico = DBselect("historico_servicos", "order by id_empresa DESC, ano DESC, mes DESC", "id, id_empresa, mes, ano");
// //$servicos_mensais = DBselect("servico_mensal");
// $servicos_prestados = DBselect("servico_prestado s INNER JOIN servico_mensal m ON s.id_servico = m.id_servico", "order by data GROUP BY s.id_empresa, m.id_servico");

// echo "<pre>";
// var_dump($servicos_prestados);
// //exit;

// $temp = array();

// foreach($empresas as $emp) {
//     foreach($historico as $his) {
//         if ($emp['id']==$his['id_empresa']) {
//             $temp[$emp['id']] = $his;
//             break;
//         }
//     }
// }

// $historico = $temp;

// foreach($empresas as $emp) {
//     if (date("d")>=$emp['dia_vencimento']) {
//         $mes = $historico[$emp['id']]['mes'];
//         if ($mes<date("n")) {
//             $ano = date("Y");
			
//             $dados = array('id_empresa'=> $emp['id'], 'mensalidade'=> $emp['mensalidade'], 'mes'=>$mes+1, 'ano'=>$ano);

//             DBcreate("historico_servicos", $dados);
//         } else if ($historico[$emp['id']]['ano']<date("Y") and $mes>date("n")) {
//             $ano = date("Y");
			
//             $dados = array('id_empresa'=> $emp['id'], 'mensalidade'=> $emp['mensalidade'], 'mes'=>1, 'ano'=>$ano);

//             DBcreate("historico_servicos", $dados);
//         }
//     }
// }

// foreach($servicos_mensais as $serv) {
//     $novo = [];
	
//     $novo['data'] = time();
//     $novo['id_empresa'] = $serv['id_empresa'];
//     $novo['id_servico'] = $serv['id_servico'];
// }

// exit;
?>