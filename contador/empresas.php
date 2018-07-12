<?if (!isset($_SESSION)) session_start();

require "../php/conexao.php"; 
require(dirname(__DIR__)."/php/sessao.php");

verificarSeSessaoExpirou();
$titulo = "Empresas"; 
$empresas = DBselect("empresa e", "order by estado_conta DESC, e.razao_social ASC", "e.id, e.razao_social, e.cnpj, e.telefone_1, e.estado_conta, e.dia_vencimento, e.codigo_cliente, concat_ws(' ',e.razao_social, e.apelido, e.codigo_cliente) as apelido, e.simples_nacional, e.cliente_desde, e.celular_whatsapp, e.email, e.nome_procurador, e.ramo_atividades, e.inscricao_estadual, e.cidade, (select COUNT(*) from funcionario_empresa where id_empresa=e.id) funcionarios");

//$empresas = DBselect("empresa e INNER JOIN funcionario_empresa f ON e.id = f.id_empresa", "order by estado_conta DESC, e.razao_social ASC", "e.id, e.razao_social, e.cnpj, e.telefone_1, e.estado_conta, e.dia_vencimento, e.codigo_cliente, e.apelido, e.simples_nacional, e.cliente_desde, e.celular_whatsapp, e.email, e.nome_procurador, e.ramo_atividades, e.inscricao_estadual, e.cidade, COUNT(f.*) as funcionarios");

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
$codigo_maior = 0;
foreach ($empresas as $key => $emp) {
    if ($emp['cliente_desde']!="" && $emp['cliente_desde']!=null && strlen($emp['cliente_desde'])==8) {
        $empresas[$key]['cliente_desde'] = maskData($emp['cliente_desde']);
        $empresas[$key]['time'] = strtotime($empresas[$key]['cliente_desde']);
    } else $empresas[$key]['time'] = "0";

    if (intval($emp['codigo_cliente'])>intval($codigo_maior)) $codigo_maior=$emp['codigo_cliente'];
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

// echo $codigo_maior;

function maskData($data) {
    return substr($data, 0, 2)."-".substr($data, 2, 2)."-".substr($data, 4, 4);
}

?>
<!DOCTYPE HTML>
<html>
    <head>
        <title>KT Prime - Clientes</title>
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
        if ($_SESSION['usuario_logado']==1) include("menus.php");
        else if ($_SESSION['usuario_logado']==3) include("../funcionario/menus.php");
        ?>
        <div id="container">
            <div> 
                <div id="acesso-rapido">
                    <ul>
                        <li class="selecionado">
                            <div><img src="../img/menu.png"></div>
                           <span class="bola"></span> 
                           <div>
                                <h4><? echo count($empresas); ?> Empresas</h4>
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
                        <? if ($_SESSION['usuario_logado']==1 or ($_SESSION['usuario_logado']==3 and $usuario->master==1)) {?>
                        <li>
                            <div><img src="../img/cadeado.png"></div>
                           <span class="bola"></span>  
                           <div>
                                <h4><? echo $emp_inativas; ?> Empresas</h4>
                                <p>Inativas</p>
                            </div>
                        </li>
                        <?}?>
                    </ul>
                </div>
               
                <section id="empresas">
                    <h3>Empresas</h3>
                    
                    <div id="pesquisar">
                        <select>
                            <option value="0">Filtro</option>
                            <option value="codigo_cliente">Código do cliente </option>
                           <option value="simples_nacional">Regime de tributação </option>
                           <option value="cliente_desde">Cliente desde </option>
                           <option value="celular_whatsapp">WhatsApp </option>
                           <option value="email">Email</option>
                           <option value="nome_procurador">Nome procurador</option>
                           <option value="dia_vencimento">Dia mensalidade</option>
                           <option value="funcionarios">Qtd de funcionários</option>
                           <option value="ramo_atividades">Ramo de atividades</option>
                           <option value="inscricao_estadual">Inscrição estadual</option>
                           <option value="cidade">Municipio</option>
                        </select>
                        <input type="text" placeholder="Pesquisar">
                    </div>
                    <!-- <div id="labels">
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
                   </div> -->
                   
                    <ul>
                    </ul>
                    <!-- </div> -->
                    <? if ($_SESSION['usuario_logado']==1 or ($_SESSION['usuario_logado']==3 and $usuario->master==1)) {?>
                    <div id="adicionar">
                       <label><span>Nova Empresa</span><img src="../img/add.png"></label>
                       <div class="clear"></div>
                    </div>
                    <?}?>
                </section>
            
                <section id="nova-empresa">
                    <div>
                        <h3>Nova Empresa</h3>
                        <img src="../img/fechar.png">
                        
                        <form>
                            <section>
                               <h4>Pessoa Física<span>1</span></h4>
                               
                               <div id="cliente">
                                   <div class="input metade">
                                        <label>Nome do Responsável Legal (sócio 1)</label>
                                        <input type="text" name="nome_cliente" placeholder="Nome do Responsável" required />
                                    </div>
                                    <div class="input metade">
                                        <label>PIS</label>
                                        <input type="text" name="pis_1" placeholder="PIS Responsável">
                                    </div>
                                    <div class="input metade">
                                        <label>CPF</label>
                                        <input type="text" name="cpf" placeholder="CPF" required />
                                    </div>
                                    <div class="input metade">
                                        <label>Celular</label>
                                        <input type="text" name="celular_1" placeholder="Celular" required />
                                    </div>
                                    <div class="input">
                                        <label>Email</label>
                                        <input type="email" name="email_1" placeholder="Email" required />
                                    </div>
                                    <div class="input metade">
                                        <label>Código E-CAC</label>
                                        <input type="text" name="codigo_ecac_1" placeholder="Código E-CAC">
                                    </div>
                                    <div class="input metade">
                                        <label>Senha E-CAC</label>
                                        <input type="text" name="senha_ecac_1" placeholder="Senha E-CAC">
                                    </div>
                                    <div class="input metade">
                                        <label>% Participação</label>
                                        <input type="number" name="percentual_socio_1" placeholder="% Participação no capital" step="0.01">
                                    </div>
                                    <div class="input metade">
                                        <label>Valor Participação</label>
                                        <input type="text" name="valor_socio_1" placeholder="Valor Participação no capital" step="0.01">
                                    </div>
                                    <div class="input">
                                    </div>
                                    <hr>
                                    <!--                           -->
                                    <!--Sócio 1 renomeado para socio 2 -->
                                    <!--                           -->
                                    <div class="input metade">
                                        <label>Nome do 2º Sócio</label>
                                        <input type="text" name="nome_socio_1" placeholder="Nome do 1º Sócio">
                                    </div>
                                    <div class="input metade">
                                        <label>PIS do 2º Sócio</label>
                                        <input type="text" name="pis_2" placeholder="PIS">
                                    </div>
                                    <div class="input metade">
                                        <label>CPF do 2º Sócio</label>
                                        <input type="text" name="cpf_2" placeholder="CPF">
                                    </div>
                                    <div class="input metade">
                                        <label>Celular do 2º Sócio</label>
                                        <input type="text" name="celular_2" placeholder="Celular">
                                    </div>
                                    <div class="input">
                                        <label>Email do 2º Sócio</label>
                                        <input type="email" name="email_2" placeholder="Email">
                                    </div>
                                    <div class="input metade">
                                        <label>Código E-CAC do 2º Sócio</label>
                                        <input type="text" name="codigo_ecac_2" placeholder="Código E-CAC">
                                    </div>
                                    <div class="input metade">
                                        <label>Senha E-CAC do 2º Sócio</label>
                                        <input type="text" name="senha_ecac_2" placeholder="Senha E-CAC">
                                    </div>
                                    <div class="input metade">
                                        <label>% Participação</label>
                                        <input type="text" name="percentual_socio_2" placeholder="% Participação no capital" step="0.01">
                                    </div>
                                    <div class="input metade">
                                        <label>Valor Participação</label>
                                        <input type="text" name="valor_socio_2" placeholder="Valor Participação no capital" step="0.01">
                                    </div>
                                    <div class="input">
                                    </div>
                                    <hr>
                                    <!--                           -->
                                    <!--Sócio 2 renomeado para socio 3 -->
                                    <!--                           -->
                                    <div class="input metade">
                                        <label>Nome do 3º Sócio</label>
                                        <input type="text" name="nome_socio_2" placeholder="Nome do 2º Sócio">
                                    </div>
                                    <div class="input metade">
                                        <label>PIS do 3º Sócio</label>
                                        <input type="text" name="pis_3" placeholder="PIS">
                                    </div>
                                    <div class="input metade">
                                        <label>CPF do 3º Sócio</label>
                                        <input type="text" name="cpf_3" placeholder="CPF">
                                    </div>
                                    <div class="input metade">
                                        <label>Celular do 3º Sócio</label>
                                        <input type="text" name="celular_3" placeholder="Celular">
                                    </div>
                                    <div class="input">
                                        <label>Email do 3º Sócio</label>
                                        <input type="email" name="email_3" placeholder="Email">
                                    </div>
                                    <div class="input metade">
                                        <label>Código E-CAC do 3º Sócio</label>
                                        <input type="text" name="codigo_ecac_3" placeholder="Código E-CAC">
                                    </div>
                                    <div class="input metade">
                                        <label>Senha E-CAC do 3º Sócio</label>
                                        <input type="text" name="senha_ecac_3" placeholder="Senha E-CAC">
                                    </div>
                                    <div class="input metade">
                                        <label>% Participação</label>
                                        <input type="text" name="percentual_socio_3" placeholder="% Participação no capital" step="0.01">
                                    </div>
                                    <div class="input metade">
                                        <label>Valor Participação</label>
                                        <input type="text" name="valor_socio_3" placeholder="Valor Participação no capital" step="0.01">
                                    </div>
                                    <div class="input">
                                    </div>
                                    <hr>
                                    <!--                           -->
                                    <!--REPRESENTANTE LEGAL-->
                                    <!--                           -->
                                    <div class="input metade">
                                        <label>Nome do Procurador/Representante</label>
                                        <input type="text" name="nome_procurador" placeholder="Nome do Procurador">
                                    </div>
                                    <div class="input metade">
                                        <label>PIS do Procurador/Representante</label>
                                        <input type="text" name="pis_4" placeholder="PIS">
                                    </div>
                                    <div class="input metade">
                                        <label>CPF do Procurador/Representante</label>
                                        <input type="text" name="cpf_4" placeholder="CPF">
                                    </div>
                                    <div class="input metade">
                                        <label>Celular do Procurador/Representante</label>
                                        <input type="text" name="celular_4" placeholder="Celular">
                                    </div>
                                    <div class="input">
                                        <label>Email do Procurador/Representante</label>
                                        <input type="email" name="email_4" placeholder="Email">
                                    </div>
                                    <div class="input metade">
                                        <label>Código E-CAC do Procurador/Representante</label>
                                        <input type="text" name="codigo_ecac_4" placeholder="Código E-CAC">
                                    </div>
                                    <div class="input metade">
                                        <label>Senha E-CAC do Procurador/Representante</label>
                                        <input type="text" name="senha_ecac_4" placeholder="Senha E-CAC">
                                    </div>
                                    <div class="input">
                                        <label>Tipo de vínculo</label>
                                        <input type="text" name="tipo_vinculo_procurador" placeholder="Descreva o tipo de vínculo">
                                    </div>
                                    <div class="input">
                                    </div>
                               </div>
                               
                               <h4>Pessoa Jurídica<span>2</span></h4>
                               
                               <div id="empresa">
                                    <div class="input">
                                        <label>Razão Social</label>
                                        <input type="text" name="razao_social" placeholder="Razão Social" required />
                                    </div>
                                    <div class="input metade">
                                        <label>CNPJ</label>
                                        <input type="text" name="cnpj" placeholder="CNPJ">
                                    </div>
                                    <div class="input metade">
                                        <label>Data de Abertura</label>
                                        <input type="text" name="data_abertura" placeholder="Data de Abertura" required />
                                    </div>
                                    <div class="input">
                                        <label>Email Acesso Site</label>
                                        <input type="email" name="email" placeholder="Email" required />
                                    </div>
                                    <div class="input metade">
                                        <label>Senha Acesso Site</label>
                                        <input type="password" name="senha_empresa" placeholder="Senha" required />
                                    </div>
                                    <div class="input metade">
                                        <label>Repita a Senha</label>
                                        <input type="password" id="repetir_senha" placeholder="Repita a Senha" required />
                                    </div>
                                    <div class="input metade">
                                        <label>Código Cliente</label>
                                        <input type="text" name="codigo_cliente" placeholder="Código Cliente" required />
                                    </div>
                                    <div class="input metade">
                                        <label>Caixa Arquivo</label>
                                        <input type="text" name="caixa_arquivo" placeholder="Caixa Arquivo">
                                    </div>
                                    <div class="input metade">
                                        <label>Inscrição Municipal - CCM</label>
                                        <input type="text" name="inscricao_municipal" placeholder="Inscrição Municipal">
                                    </div>
                                    <div class="input metade">
                                        <label>Inscrição Estadual</label>
                                        <input type="text" name="inscricao_estadual" placeholder="Inscrição Estadual">
                                    </div>
                                    <div class="input metade">
                                        <label>NIRE / Registro</label>
                                        <input type="text" name="nire" placeholder="NIRE / Registro">
                                    </div>
                                    <div class="input metade">
                                        <label>Tipo de Cliente/Regime Tributário</label>
                                        <select name="simples_nacional">
                                            <option value="1">Simples nacional</option>
                                            <option value="2">Lucro presumido</option>
                                            <option value="3">Lucro real</option>
                                            <option value="5">MEI</option>
                                            <option value="6">Pessoa Física</option>
                                            <option value="4">Outros</option>
                                        </select>
                                    </div>
                                    <div class="input metade">
                                        <label>Tipo de Empresa</label>
                                        <select name="data_simples_nacional">
                                            <option value="1">Prestação de serviços</option>
                                            <option value="2">Prestação de serviços e comércio</option>
                                            <option value="3">Comercio</option>
                                            <option value="4">Outros</option>
                                        </select>
                                    </div>
                                    <div class="input metade">
                                        <label>Cliente desde</label>
                                        <input type="text" name="cliente_desde" placeholder="Data de inicio das atividades" data-mask="00/00/0000">
                                    </div>
                                    <div class="input">
                                        <label>Nome Fantasia</label>
                                        <input type="text" name="nome_fantasia" placeholder="Nome fantasia da empresa">
                                    </div>

                                    <div class="input">
                                        <label>Forma de captação</label>
                                        <input type="text" name="captacao" placeholder="Como a empresa foi captada">
                                    </div>
                                    <div class="input metade">
                                        <label>Valor da Mensalidade (R$)</label> 
                                        <input type="number" name="mensalidade" placeholder="Mensalidade R$" min="0" step="any" required />
                                    </div>
                                    <div class="input metade">
                                        <label>Dia de vencimento</label>
                                        <select name="dia_vencimento">
                                        </select>
                                    </div>
                                    <div class="input">
                                        <label>Ramo de Atividades</label>
                                        <input type="text" name="ramo_atividades" placeholder="Ramo de Atividades">
                                    </div>
                                    <div class="input metade">
                                        <label>CEP</label>
                                        <input type="text" name="cep" placeholder="CEP">
                                    </div>
                                    <div class="input metade">
                                        <label>Estado</label>
                                        <input type="text" name="estado" placeholder="Estado">
                                    </div>
                                    <div class="input metade">
                                        <label>Cidade</label>
                                        <input type="text" name="cidade" placeholder="Cidade">
                                    </div>
                                    <div class="input metade">
                                        <label>Número</label>
                                        <input type="text" name="numero" placeholder="Numero">
                                    </div>
                                    <div class="input">
                                        <label>Rua</label>
                                        <input type="text" name="rua" placeholder="Rua">
                                    </div>
                                    <div class="input metade">
                                        <label>Complemento</label>
                                        <input type="text" name="complemento" placeholder="Complemento">
                                    </div>
                                    <div class="input metade">
                                        <label>Bairro</label>
                                        <input type="text" name="bairro" placeholder="Bairro">
                                    </div>
                                    
                                    <div class="input metade">
                                        <label>Telefone 1</label>
                                        <input type="text" name="telefone_1" placeholder="Telefone 1">
                                    </div>
                                    <div class="input metade">
                                        <label>Telefone 2</label>
                                        <input type="text" name="telefone_2" placeholder="Telefone 2">
                                    </div>
                                    <div class="input metade">
                                        <label>Telefone 3</label>
                                        <input type="text" name="telefone_3" placeholder="Telefone 3">
                                    </div>
                                    <div class="input metade">
                                        <label>Celular Whatsapp</label>
                                        <input type="text" name="celular_whatsapp" placeholder="Celular Whatsapp" data-mask="(00) 00000-0000">
                                    </div>
                                    <div class="input metade">
                                        <label>Email 2</label>
                                        <input type="email" name="email_reserva" placeholder="Email">
                                    </div>
                                    <div class="input metade">
                                        <label>Login Seguro Desemprego</label>
                                        <input type="text" name="login_seguro_desemprego" placeholder="Login Seguro Desemprego">
                                    </div>
                                    <div class="input metade">
                                        <label>Senha Seguro Desemprego</label>
                                        <input type="text" name="senha_seguro_desemprego" placeholder="Senha Seguro Desemprego">
                                    </div>
                                    <div class="input metade">
                                        <label>Prolabore</label>
                                        <select name="prolabore">
                                            <option value="0">Não</option>
                                            <option value="1">Sim</option>
                                        </select>
                                    </div>
                                    <div class="input metade">
                                        <label>Funcionarios</label>
                                        <select name="funcionarios">
                                            <option value="0">Não</option>
                                            <option value="1">Sim</option>
                                        </select>
                                    </div>
                               </div>
                               
                               <h4>Dados Bancários<span>3</span></h4>
                               
                               <div id="bancarios">
                                   <div class="input metade">
                                       <label>Banco 1</label>
                                       <input type="text" name="banco_1" placeholder="Banco 1">
                                   </div>
                                   <div class="input metade">
                                       <label>Agência 1</label>
                                       <input type="text" name="agencia_1" placeholder="Agência 1">
                                   </div>
                                   <div class="input metade">
                                       <label>Conta 1</label>
                                       <input type="text" name="conta_1" placeholder="Conta 1">
                                   </div>
                                   <div class="input metade">
                                       <label>Favorecido 1</label>
                                       <input type="text" name="favorecido_1" placeholder="Favorecido 1">
                                   </div>
                                   <hr>
                                   
                                   <div class="input metade">
                                       <label>Banco 2</label>
                                       <input type="text" name="banco_2" placeholder="Banco 2">
                                   </div>
                                   <div class="input metade">
                                       <label>Agência 2</label>
                                       <input type="text" name="agencia_2" placeholder="Agência 2">
                                   </div>
                                   <div class="input metade">
                                       <label>Conta 2</label>
                                       <input type="text" name="conta_2" placeholder="Conta 2">
                                   </div>
                                   <div class="input metade">
                                       <label>Favorecido 2</label>
                                       <input type="text" name="favorecido_2" placeholder="Favorecido 2">
                                   </div>
                                   <hr>
                                   
                                   <div class="input metade">
                                       <label>Banco 3</label>
                                       <input type="text" name="banco_3" placeholder="Banco 3">
                                   </div>
                                   <div class="input metade">
                                       <label>Agência 3</label>
                                       <input type="text" name="agencia_3" placeholder="Agência 3">
                                   </div>
                                   <div class="input metade">
                                       <label>Conta 3</label>
                                       <input type="text" name="conta_3" placeholder="Conta 3">
                                   </div>
                                   <div class="input metade">
                                       <label>Favorecido 3</label>
                                       <input type="text" name="favorecido_3" placeholder="Favorecido 3">
                                   </div>
                                   <hr>
                                   
                                   <div class="input metade">
                                       <label>Banco 4</label>
                                       <input type="text" name="banco_4" placeholder="Banco 4">
                                   </div>
                                   <div class="input metade">
                                       <label>Agência 4</label>
                                       <input type="text" name="agencia_4" placeholder="Agência 4">
                                   </div>
                                   <div class="input metade">
                                       <label>Conta 4</label>
                                       <input type="text" name="conta_4" placeholder="Conta 4">
                                   </div>
                                   <div class="input metade">
                                       <label>Favorecido 4</label>
                                       <input type="text" name="favorecido_4" placeholder="Favorecido 4">
                                   </div>
                                   <hr>
                               </div>
                               
                               <h4>Cadastro de Senhas<span>4</span></h4>
                               
                               <div id="cadastro_senhas">
                                   <div class="input metade">
                                       <label>Login Municipal</label>
                                       <input type="text" name="login_prefeitura" placeholder="Login Municipal - Prefeitura">
                                   </div>
                                   <div class="input metade">
                                       <label>Senha Municipal</label>
                                       <input type="text" name="senha_prefeitura" placeholder="Senha Municipal - Prefeitura">
                                   </div>
                                   <div class="input metade">
                                       <label>Login Estadual - Posto Fiscal</label>
                                       <input type="text" name="login_sefaz" placeholder="Login Estadual - SEFAZ">
                                   </div>
                                   <div class="input metade">
                                       <label>Senha Estadual - Senha Online</label>
                                       <input type="text" name="senha_sefaz" placeholder="Senha Estadual - SEFAZ">
                                   </div>
                                    <div class="input">
                                       <label>Acesso Federal - Código Simples Nacional</label>
                                       <input type="text" name="login_simples_nacional" placeholder="Acesso Federal - Simples Nacional">
                                   </div>
                                   <div class="input metade">
                                       <label>Login ECAC Pessoa Jurídica</label>
                                       <input type="text" name="login_ecac_pessoa_juridica" placeholder="Login ECAC Pessoa Jurídica">
                                   </div>
                                   <div class="input metade">
                                       <label>Senha ECAC Pessoa Jurídica</label>
                                       <input type="text" name="senha_ecac_pessoa_juridica" placeholder="Senha ECAC Pessoa Jurídica">
                                   </div>
                               </div>
                               
                               <h4>Outras Informações<span>5</span></h4>
                               
                               <div id="outros">
                                    <div class="input">
                                        <label>Primeiro Mês Cobrança</label>
                                        <select name="primeiro_mes">
                                        </select>
                                    </div>
                                    <div class="input metade">
                                        <label>Tipo de Certificado Digital e Senha</label>
                                        <input type="text" name="tipo_cd" placeholder="Tipo de Certificado Digital e Senha">
                                    </div>
                                    <div class="input metade">
                                        <label>Validade Certificado Digital</label>
                                        <input type="text" name="validade_cd" placeholder="Validade Cerificado Digital">
                                    </div>
                                   
                                    <div class="input metade">
                                        <label>Link para emissão de NFs de Serviço</label>
                                        <input type="text" name="link_nf_servico" placeholder="Cole o link aqui">
                                    </div>
                                    
                                    <div class="input metade">
                                        <label>Link para emissão de NFs de Venda</label>
                                        <input type="text" name="link_nf_venda" placeholder="Cole o link aqui">
                                    </div>
                                    
                                    <div class="input metade">
                                        <label>Senha para link de NFs de Venda</label>
                                        <input type="text" name="senha_nf_venda" placeholder="Digite a senha aqui">
                                    </div>
                                    
                                    <div class="input metade">
                                        <label>Apelido (para buscas)</label>
                                        <input type="text" name="apelido" placeholder="Informe palavras chave">
                                    </div>
                                    
                                    <div class="input metade">
                                        <label>Tipo do 1º Alvará</label>
                                        <input type="text" name="tipo_alvara_1" placeholder="Informe o tipo do 1º alvará">
                                    </div>
                                    
                                    <div class="input metade">
                                        <label>Vencimento do 1º Alvará</label>
                                        <input type="text" name="vencimento_alvara_1" placeholder="Informe o vencimento do 1º alvará" data-mask="00/00/0000">
                                    </div>
                                    
                                    <div class="input metade">
                                        <label>Tipo do 2º Alvará</label>
                                        <input type="text" name="tipo_alvara_2" placeholder="Informe o tipo do 2º alvará">
                                    </div>
                                    
                                    <div class="input metade">
                                        <label>Vencimento do 2º Alvará</label>
                                        <input type="text" name="vencimento_alvara_2" placeholder="Informe o vencimento do 2º alvará" data-mask="00/00/0000">
                                    </div>
                                    
                                    <div class="input">
                                        <label>Observações</label>
                                        <textarea name="observacoes" placeholder="Observações"></textarea>
                                    </div>
                                    
                                    <div class="input">
                                        <label>Observações Privadas</label>
                                        <textarea name="observacoes_privadas" placeholder="Observações privadas"></textarea>
                                    </div>
                                    
                                    <!-- <div class="input"></div> -->
                               </div>
                                
                                <div id="botoes">
                                    <div class="input"></div>
                                    <button id="avancar" type="button" class="botao">Criar</button>
                                </div>
                            </section>
                        </form>
                    </div>
                </section>
            </div>
        </div>
    </body>
    <script type="text/javascript">
        var empresas = <? echo json_encode($empresas); ?>;
        var original = <? echo json_encode($empresas); ?>;
        var ina = <? echo json_encode($inadimplentes); ?>;
        var servicosTotal = <? echo json_encode($servicosTotal); ?>;
        var empresaHistorico = <? echo json_encode($empresa_historico); ?>;
        var historico_pagamento = <? echo json_encode($historico_pagamento); ?>;
        var usuario_logado = <? echo $_SESSION['usuario_logado']; ?>;
        var funcionario_master = <? echo $_SESSION['usuario_logado']==3?$usuario->master:0; ?>;
        var codigo_maior = Number(<? echo $codigo_maior; ?>)+1;
    </script>
    <script src="../js/jquery.mask.js"></script>
    <script src="../js/contador/empresas.js?<? echo time(); ?>"></script>
</html>