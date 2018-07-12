<?if (!isset($_SESSION)) session_start();



require "../php/conexao.php";

require(dirname(__DIR__)."/php/sessao.php");



verificarSeSessaoExpirou();

$titulo = "Empresas"; 

$empresas = DBselect("empresa e", "order by estado_conta DESC, e.razao_social ASC", "e.id, e.razao_social, e.cnpj, e.telefone_1, e.estado_conta, e.dia_vencimento, e.codigo_cliente, concat_ws(' ',e.razao_social, e.apelido) as apelido, e.simples_nacional, e.cliente_desde, e.celular_whatsapp, e.email, e.nome_procurador, e.ramo_atividades, e.inscricao_estadual, e.cidade, (select COUNT(*) from funcionario_empresa where id_empresa=e.id) funcionarios");



$solicitacoes = DBselect("solicitacao", "where respondido=0");

$inadimplentes = DBselect("historico_servicos", "order by ano DESC, mes DESC");

$historico_pagamento = DBselect("historico_pagamento");

$pagamentos = DBselect("pagamento", "order by time DESC");

$servicosTotal = DBselect("servico_prestado", "group by id_historico" , "id_historico, SUM(valor) as total, id_empresa");



$empresa_historico = array();



// Transformar array em array referenciado

$temp = array(); 

foreach ($historico_pagamento as $h) {

    if (!array_key_exists($h['id_historico'], $temp)) $temp[$h['id_historico']] = array();

    

    array_push($temp[$h['id_historico']], $h['id_pagamento']);

}

$historico_pagamento = $temp;



// Transformar array em array referenciado

$temp = [];

if (count($pagamentos)>0) {

    foreach($pagamentos as $key => $pag) {

        $temp[$pag['id']] = $pag;

    }

}

$pagamentos = $temp;



// Transformar array em array referenciado

$temp=[];

if (count($servicosTotal)>0) {

    foreach($servicosTotal as $key => $serv) {

        $temp[$serv['id_historico']] = $serv;

    }

}

$servicosTotal = $temp;



// Transformar array em array referenciado

$temp=[];

if (count($inadimplentes)>0) {

    foreach($inadimplentes as $key => $ina) {

        $temp[$ina['id']] = $ina;

        $temp[$ina['id']]['servicos'] = 0;

        $temp[$ina['id']]['pago'] = 0;

        

        if (!array_key_exists($ina['id_empresa'], $empresa_historico)) $empresa_historico[$ina['id_empresa']] = array();

        array_push($empresa_historico[$ina['id_empresa']], $ina['id']);

    }

}

$inadimplentes = $temp;



// Colocar total que ja foi pago

foreach($historico_pagamento as $key=>$pags) {

    $total = 0;

    foreach($pags as $pag) {

        if ($pagamentos[$pag]['estado']==3 || $pagamentos[$pag]['estado']==4) {

            $total += $pagamentos[$pag]['valor'];

        }

    }

    $inadimplentes[$key]['pago'] = $total;

    if (array_key_exists($key, $servicosTotal)) $inadimplentes[$key]['servicos'] = $servicosTotal[$key]['total'];

} 



if (count($empresas)==0) $empresas = array();

if (count($solicitacoes)==0) $solicitacoes = array();

if (count($inadimplentes)==0) $inadimplentes = array();



$emp_pen=0;

$emp_ina=0;

$emp_inativas=0;

$hoje = strtotime(date("d-n-Y", time()));



foreach ($empresas as $key => $emp) {

    $empresas[$key]['pendencia']=0;

    

    if ($emp['estado_conta']==0) {

        $emp_inativas++;

    }

    

    foreach ($solicitacoes as $sol) {

        if ($sol['id_empresa']==$emp['id']) {

            $empresas[$key]['pendencia']=1;

            $emp_pen++;

            break;

        }

    } 

    

    $teste = 0;

    $empresas[$key]['inadimplente']=0;

    

    foreach($empresa_historico[$emp['id']] as $emp_h) {

        if ($empresas[$key]['inadimplente']==1) continue;

        $ina = $inadimplentes[$emp_h];

        $empresas[$key]['total'] = $ina['mensalidade']+$ina['servicos']-$ina['desconto']-$ina['pago'];

        $temp = strtotime($emp['dia_vencimento']."-".($ina['mes']+1==13?1:$ina['mes']+1)."-".($ina['mes']+1==13?$ina['ano']+1:$ina['ano']));

        if ($temp<$hoje) {

            if ($empresas[$key]['total']>0) {

                $empresas[$key]['inadimplente']=1;

            }

        }

        

        $empresas[$key]['temp'] = $emp['dia_vencimento']."-".($ina['mes']+1==13?1:$ina['mes'])."-".($ina['mes']+1==13?$ina['ano']+1:$ina['ano']);

        $empresas[$key]['hoje'] = $hoje;

    }

    

    if ($empresas[$key]['inadimplente']==1) {

        $emp_ina++;

    }

}



?>

<!DOCTYPE HTML>

<html>

    <head>

        <title>KT Prime - Empresas</title>

        <meta charset="utf-8">

        <meta name="viewport" content="width=device-width, initial-scale=1">

        <link rel="stylesheet" href="../css/contador/empresas.css" media="(min-width: 1000px)">

        <link rel="stylesheet" href="../cssmobile/contador/empresas.css" media="(max-width: 999px)">

        <link rel="stylesheet" href="../css/geral.css" media="(min-width: 1000px)">

        <link rel="stylesheet" href="../cssmobile/geral.css" media="(max-width: 999px)">

        <link href="../img/favicon.png" rel="shortcut icon" type="image/x-icon" />

    </head>

    

    <body>

        <?

            include("menus.php");

        ?>

        <div id="container">

            <div> 

                <div id="acesso-rapido" class="tres">

                    <ul>

                        <li class="selecionado">

                            <div><img src="../img/menu.png"></div>

                           <span class="bola"></span> 

                           <div>

                                <h4><? echo count($empresas)-$emp_inativas; ?> Empresas</h4>

                                <p>Total</p>

                            </div>

                        </li>

                        <li>

                            <div><img src="../img/input-mark.png"></div>

                            <span class="bola"></span> 

                            <div>

                                <h4><? echo count($empresas)-$emp_inativas; ?> Empresas</h4>

                                <p>Ativas</p>

                            </div>

                        </li>

                        <li>

                            <div><img src="../img/exclamacao.png"></div>

                            <span class="bola"></span> 

                            <div>

                                <h4><? echo $emp_pen; ?> Empresas</h4>

                                <p>Com Pendências</p>

                            </div>

                        </li>

                        

                        <li>

                            <div><img src="../img/pare.png"></div>

                           <span class="bola"></span>  

                           <div>

                                <h4><? echo $emp_ina; ?> Empresas</h4>

                                <p>Inadimplentes</p>

                            </div>

                        </li>

                    </ul>

                </div>

               

                <section id="empresas">

                    <h3>Lista de Empresas</h3>

                    

                    <div id="pesquisar">

                        <input type="text" placeholder="Pesquisar">

<!--                        <img src="../img/lupa.png">-->

                    </div>

                    <div id="labels">

                       <ul>

                           <li data-value="codigo_cliente">Código do cliente </li>

                           <li data-value="simples_nacional">Regime de tributação </li>

                           <li data-value="cliente_desde">Cliente desde </li>

                           <li data-value="celular_whatsapp">WhatsApp </li>

                           <li data-value="email">Email</li>

                           <li data-value="nome_procurador">Nome procurador</li>

                           <li data-value="dia_vencimento">Dia mensalidade</li>

                           <li data-value="funcionarios">Qtd de funcionários</li>

                           <li data-value="ramo_atividades">Ramo de atividades</li>

                           <li data-value="inscricao_estadual">Inscrição estadual</li>

                           <li data-value="cidade">Municipio</li>

                       </ul>

                   </div>

                    <ul>

                    </ul>

                </section>

            </div>

        </div>

    </body>

    <script type="text/javascript">

        var empresas = <? echo json_encode($empresas); ?>;

        var original = <? echo json_encode($empresas); ?>;

        var tela_funcionario = true;

    </script>

    <script src="../js/jquery.mask.js"></script>

    <script src="../js/contador/empresas.js"></script>

</html>