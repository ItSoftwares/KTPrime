<?
if (!isset($_SESSION)) session_start();

require "../php/conexao.php";
require_once('../php/pagseguro/config_pag.php');
require_once('../php/pagseguro/utils.php');
require(dirname(__DIR__)."/php/sessao.php");

verificarSeSessaoExpirou();

$titulo = "Financeiro"; 

$params = array(
    'email' => $PAGSEGURO_EMAIL,
    'token' => $PAGSEGURO_TOKEN
);
$header = array();

$response = curlExec($PAGSEGURO_API_URL."/sessions", $params, $header);
$json = json_decode(json_encode(simplexml_load_string($response)));
$sessionCode = $json->id;

//var_dump($json)
?>
<!DOCTYPE HTML>
<html>
    <head>
        <title>KT Prime - Financeiro</title>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <link rel="stylesheet" href="../css/empresa/financeiro.css" media="(min-width: 1000px)">
        <link rel="stylesheet" href="../cssmobile/empresa/financeiro.css" media="(max-width: 999px)">
        <link rel="stylesheet" href="../css/geral.css" media="(min-width: 1000px)">
        <link rel="stylesheet" href="../cssmobile/geral.css" media="(max-width: 999px)">
        <link href="../img/favicon.png" rel="shortcut icon" type="image/x-icon" />
    </head>
    
    <body>
        <?
        include("menus.php");
        $empresa = unserialize($_SESSION['empresa']);
        $historico = DBselect("historico_servicos", "where id_empresa={$empresa->id} order by ano, mes DESC");
        $servicos = DBselect("servico");
        $servicosPrestados = DBselect("servico_prestado", "where id_empresa={$empresa->id}");
        $pagamentos = DBselect("pagamento", "where id_empresa={$empresa->id} order by time DESC");
        $historico_pagamento = DBselect("historico_pagamento");
        $servicosTotal = DBselect("servico_prestado", "where id_empresa={$empresa->id} group by id_historico" , "id_historico, SUM(valor) as total, id_empresa");
        
        $servicosPrestados = isset($servicosPrestados)?$servicosPrestados:[];
        $pagamentos = isset($pagamentos)?$pagamentos:[];
        $empresa_historico = array();
        
        if (count($historico)==0) {
            $historico=[];   
        }
        
        $temp = [];
        if (count($servicos)>0) {
            foreach($servicos as $serv) {
                $temp[$serv['id']] = $serv;
            }
        }
        $servicos = $temp;
        
        $temp = [];
        foreach($servicosPrestados as $obri) {
            $temp[$obri['id']] = $obri;
        }
        $servicosPrestados = $temp;
        
        $temp = [];
        foreach($pagamentos as $pag) {
            $temp[$pag['id']] = $pag;
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
        
        $temp = array(); 
        foreach($historico as $h) {
            $h['servicos'] = 0;
            $h['pago'] = 0;
            if (array_key_exists($h['id'], $servicosTotal)) $h['servicos'] = $servicosTotal[$h['id']]['total'];
            $temp[$h['id']] = $h;
            array_push($empresa_historico, $h['id']);
        }
        $historico = $temp;
        
        // Transformar array em array referenciado
        $temp = array(); 
        foreach ($historico_pagamento as $h) {
            if (!array_key_exists($h['id_historico'], $temp)) $temp[$h['id_historico']] = array();

            array_push($temp[$h['id_historico']], $h['id_pagamento']);
        }
        $historico_pagamento = $temp;
        
        foreach($historico_pagamento as $key=>$pags) {
            if (!in_array($key, $empresa_historico)) continue;
            $total = 0;
            $historico[$key]['pagamento'] = 0;
            foreach($pags as $pag) {
                $historico[$key]['pagamento'] = 1;
                if ($pagamentos[$pag]['estado']==3 || $pagamentos[$pag]['estado']==4) {
                    $total += $pagamentos[$pag]['valor'];
                    $historico[$key]['pagamento'] = 2;
                }
            }
            $historico[$key]['pago'] = $total;
        }
        $temp = array(); 
        foreach ($historico as $h) {
            array_push($temp, $h);
        }
        
        $historico = $temp;
        ?>
        
        <div id="container">
            <div>
                <section id="pagar">
                   <h3>Pagamento</h3>
                    <section id="info-compra">
                        <h4>Detalhes</h4>

                        <div id="tabela">
<!--                           <h3>Informações</h3>-->
                            <table>
                                <tr>
                                    <th>Pagar</th>
                                    <th>Estado</th>
                                    <th>Periodo</th>
                                    <th>Vencimento</th>
<!--                                    <th>Até</th>-->
                                    <th>Valor</th>
                                </tr>
                                
                                <?
                                $valor = 0;
                                $descricao = "Pagamento referente ao(s) mês(es) -mes- do ano de -ano-";
                                $tempMeses = "";
                                $tempAno = "";
                                $ids  = [];
                                $ids2 = []; 
                                $ids3 = []; 
                                foreach($historico as $key => $his) {
                                    $total = $his['mensalidade']+$his['servicos']-$his['desconto']-$his['pago'];
                                    $his['total'] = $total;
                                    if ($his['total']<=0) continue;
                                    
                                    $de = strtotime("01-".$his['mes']."-".$his['ano']);
                                    $ate = strtotime("01-".($his['mes']+1>12?1:$his['mes']+1)."-".($his['mes']+1>12?$his['ano']+1:$his['ano']));
                                    
                                    $vencimento = strtotime($usuario->dia_vencimento."-".($his['mes']+1>12?1:$his['mes']+1)."-".($his['mes']+1>12?$his['ano']+1:$his['ano']));
                                    
                                    $estado = time()>$ate?"<img src='../img/exclamacao.png' class='pendente'>Aguardando Pagamento":"Mês Atual"; 
                                    
                                    if (time()>$vencimento) {
                                        $estado = "<img src='../img/exclamacao.png' class='vencido'>Vencido";
                                    }
                                    
                                    if (false) {
//                                    $his['total'] = 0;
//                                    if ($his['id_pagamento']!=null) {
//                                        $es = $pagamentos[$his['id_pagamento']]['estado']; 
//                                        if ($es==3 || $es==4) {
//                                            $tipo = $pagamentos[$his['id_pagamento']]['tipo'];
//                                            if ($tipo!=4) {
//                                                continue;
//                                            }
//                                        }
//                                    }
//                                    $de = strtotime($usuario->dia_vencimento."-".$his['mes']."-".$his['ano']);
//                                    $ate = strtotime($usuario->dia_vencimento."-".($his['mes']+1>12?1:$his['mes']+1)."-".($his['mes']+1>12?$his['ano']+1:$his['ano']));
//                                    $estado = time()>$ate?"<img src='../img/exclamacao.png' class='pendente'>Pendente":"Aberto"; 
//                                    
//                                    if ($his['id_pagamento']!=null) {
//                                        $es = $pagamentos[$his['id_pagamento']]['estado']; 
//                                        if ($es==1 || $es==2) {
//                                            $estado = "Aguardando Confirmação!";
//                                        } else if ($es==3 || $es==4) {
//                                            $teste=false;
//                                            foreach ($servicosPrestados as $serv) {
//                                                if ($serv['id_pagamento']!=3 and $serv['id_pagamento']!=4 and $serv['id_pagamento']==null) {
//                                                    $teste=true;
//                                                }
//                                            }
////                                            if ($teste==false) continue;
////                                            $his['total'] += floatval($his['mensalidade']);
//                                        } else {
//                                        }
//                                        $his['total'] += floatval($his['mensalidade']);
//                                    } else {
//                                        $his['total'] += floatval($his['mensalidade']);
//                                    }
//                                    
//                                    foreach($servicosPrestados as $k => $serv) {
////                                        if ($serv['id_historico']!=$his['id'] or $serv['pago']==1) {
//                                        if ($serv['id_historico']!=$his['id']) {
////                                            if ($pagamentos[$serv['id_pagamento']]['tipo']==3) {
////                                                
////                                            }
//                                            continue;
//                                        }
////                                        $id = $serv['id_servico'];
//                                        $his['total'] += floatval($serv['valor']);
////                                        echo $serv['id_historico'].".";
//                                        array_push($ids2, $serv['id']);
//                                    }
//                                    
//                                    if ($his['id_pagamento']!=null) {
//                                        if ($pagamentos[$his['id_pagamento']]['estado']==3 or $pagamentos[$his['id_pagamento']]['estado']==4) {
//                                            $his['total']-=$pagamentos[$his['id_pagamento']]['valor'];
//                                            if ($pagamentos[$his['id_pagamento']]['tipo']>=3) {
//                                                array_push($ids3, $his['id_pagamento']);
//                                                if ($pagamentos[$his['id_pagamento']]['id_proximo']!=null) {
//                                                    if ($pagamentos[$pagamentos[$his['id_pagamento']]['id_proximo']]['estado']==3 or $pagamentos[$pagamentos[$his['id_pagamento']]['id_proximo']]['estado']==4) {
//                                                        $his['total']-=$pagamentos[$pagamentos[$his['id_pagamento']]['id_proximo']]['valor'];
//                                                    }
//                                                }
//                                            }
//                                        }
//                                    }
//                                    
//                                    $total = $his['total']-$his['desconto'];
//                                    $total = $total<0?0:$total;
//                                    $his['total'] = $total;
//                                    if ($total==0) {
//                                        continue;
//                                    }
                                    }
                                ?>
                                <tr>
                                    <td data-nome="Pagar"><input type="checkbox" data-id='<? echo $his['id']; ?>'data-index='<? echo $key; ?>'></td>
                                    <td data-nome="Estado"><? echo $estado; ?></td>
                                    <td data-nome="Periodo"><? echo date("m - Y", $de); ?></td>
                                    <td data-nome="Vencimento"><? echo date("d/m/Y", $vencimento); ?></td>
<!--                                    <td data-nome="Até"><? echo date("d/m/Y", $ate); ?></td>-->
                                    <td data-nome="Valor">R$ <? echo number_format($total, 2, ",", "."); ?></td> 
                                </tr>
                                <?
                                    $historico[$key]['total'] = $his['total'];
//                                    if ($estado=="<img src='../img/exclamacao.png' class='pendente'>Pendente") {
                                        $tempMeses = $tempMeses==""?$his['mes']:$tempMeses.", ".$his['mes'];
                                        $tempAno = $his['ano'];
//                                        array_push($ids, $his['id']);
//                                    }
                                    $valor += floatval($his['total']);
                                }
                                $descricao = str_replace("-mes-", $tempMeses, $descricao);
                                $descricao = str_replace("-ano-", $tempAno, $descricao);
//                                echo $valor;
                                ?>
                                <tr>
                                    <td colspan="4">TOTAL A SER PAGO</td>
<!--                                    <td id="valor-total">R$ <? echo number_format($valor, 2, ",", "."); ?></td>-->
                                    <td id="valor-total">R$ 0,00</td>
                                </tr>
                            </table>
                            <button id="pagar-box">Pagar</button>
                            <div class="clear"></div>
                        </div>
                    </section>
                </section>
                
                <section id="pagamentos">
                    <h3>Histórico de Pagamentos</h3>
                    <div id="pag-container">
                        <table>
                           <?
                            foreach($pagamentos as $pag) {
                                $estado ="";
                                
                                if ($pag['estado']<=2) {
                                    $estado = "<img src='../img/exclamacao.png' class='pendente'>Aguardando Confirmação";
                                } else if ($pag['estado']==3 || $pag['estado']==4) {
                                    $estado = "<img src='../img/input-mark.png' class='pago'>Pago";
                                } else {
                                    $estado = "<img src='../img/pare.png' class='cancelado'>Cancelado";
                                }
                            ?>
                            <tr>
                                <td data-nome='Data'><? echo $estado; ?></td>
                                <td data-nome='Data'><? echo date("d/m/Y, H:i", $pag['time']); ?></td>
                                <td data-nome='Descricao'><? echo $pag['descricao']; ?></td>
                                <td data-nome='Tipo'>
                                <? 
                                echo $pag['tipo']==1?"<img src='../img/pagamento/cartao-credito.png'>Cartão":($pag['tipo']==2?(($pag['estado']==3 || $pag['estado']==4)?"<img src='../img/pagamento/boleto.png'>Boleto":"<a href='{$pag['link_boleto']}' target='_blank'><img src='../img/pagamento/boleto.png'>Boleto</a>"):($pag['tipo']==3?"<img src='../img/pagamento/manual.png'>Manual":"<img src='../img/pagamento/manual.png'>Parcial"));
                                
                               // echo $pag['tipo']==1?"<img src='../img/pagamento/cartao-credito.png'>Cartão":($pag['tipo']==2?"<img src='../img/pagamento/boleto.png'>Boleto":($pag['tipo']==3?"<img src='../img/pagamento/manual.png'>Manual":"<img src='../img/pagamento/manual.png'>Parcial"));
                                ?>
                                </td>
                                <td data-nome='Valor'><? echo number_format($pag['valor'],2,",", "."); ?></td>
                            </tr>
                            <?
                            }
                            ?>
                        </table>
                    </div>
                </section>
                
                <section id="historico">
                    <h3>Serviços Solicitados por Periodo</h3>
                    <div id="valores">
                        <table></table>
                    </div>
                </section>
              
                <section id="fundo-pagar">
                    <img src="../img/fechar.png" class="fechar">
                    
                    <div>
                        <div id="forma-pagamento">
                            <h3>Forma de Pagamento</h3>
                            <input type="radio" id="cartao" name="pagamento">
                            <label for="cartao">Cartão</label>
                            <input type="radio" id="boleto" name="pagamento">
                            <label for="boleto">Boleto</label>
                            <input type="radio" id="deposito" name="pagamento">
                            <label for="deposito">Depósito em conta</label>

                            <div id="tipo"><img src="../img/pagamento/cartao-credito.png"></div>
                        </div>
                        
                        <section id="pagar-cartao">
                           <h4>Pagar com Cartão</h4>

                           <div class="container">
                                <form>
                                   <input type="hidden" name="brand">
                                   <input type="hidden" name="token">
                                   <input type="hidden" name="senderHash">
                                   <input type="hidden" name="valor" value='<? echo $valor; ?>'>
                                   <input type="hidden" name="descricao" value='<? echo $descricao; ?>'>
                                    <div class="input">
                                        <label for="titular">Nome do Titular</label>
                                        <input type="text" id="titular" placeholder="Titular do Cartão" data-id="nome" name="titular">
                                    </div>

                                    <div class="input">
                                        <label for="numero">Número do Cartão</label>
                                        <input type="text" id="numero" placeholder="Número do Cartão" data-id="numero-cartao" name="numero">
                                    </div>

                                    <div class="input metade">
                                        <label for="validade">Validade</label>
                                        <input type="text" id="validade" placeholder="xx/xxxx" data-id="validade" name="validade">
                                    </div>

                                    <div class="input metade">
                                        <label for="cvc">Código CVC</label>
                                        <input type="text" id="cvc" placeholder="Código CVC" data-id="cvc" name="cvc">
                                    </div>

                                    <button type="button" <? echo $valor==0?"disabled":"" ?>>Realizar Pagamento</button>

                                    <div id="confirmar">
                                        <img src='../img/fechar.png'>
                                        <div>
                                            <h3>Titular do Cartão</h3>
                                            <div class="input">
                                                <label for="cpf">CPF</label>
                                                <input type="text" id="cpf" placeholder="CPF" name="cpf" required>
                                            </div>
                                            <div class="input metade">
                                                <label for="aniversario">Data de Nascimento</label>
                                                <input type="text" id="aniversario" placeholder="00/00/0000" name='aniversario' required>
                                            </div>
                                            <div class="input metade">
                                                <label for="telefone">Telefone</label>
                                                <input type="text" id="telefone" placeholder="Telefone" name="telefone" required>
                                            </div>

                                            <button>Finalizar</button>
                                        </div>
                                    </div>
                                </form>

                                <div id="cartao-container">
                                    <div id="cartao-credito">
                                        <div class="cartao frente">
                                            <div id="chip"><span></span></div>
                                            <h3 id="brand"></h3>
                                            <!-- <div id="bandeira"><img src="../img/pagamento/bandeira-visa.png"></div> -->
                                            <p class="numero-cartao">0000 0000 0000 0000</p>
                                            <p class="nome">NOME DO TITULAR</p>
                                            <p class="validade">07/2019</p>
                                        </div>
                                        <div class="cartao verso">
                                            <div class="fita"></div>
                                            <div class="faixa"></div>
                                            <p class="cvc">373</p>
                                        </div>
                                    </div>
                                </div>
                           </div>
                        </section>

                        <section id="pagar-boleto">
                            <h4>Pagar com Boleto</h4>

                            <div class="container">
                               <form>
                                   <input type="hidden" name="senderHash">
                                   <input type="hidden" name="valor" value='<? echo $valor; ?>'>
                                   <input type="hidden" name="descricao" value='<? echo $descricao; ?>'>
                                    <button <? echo $valor==0?"disabled":"" ?>>Emitir Boleto</button>
                               </form>
                            </div>
                        </section>

                        <section id="pagar-deposito">
                            <h4>Depósito em Conta</h4>

                            <div class="container">
                                <div>
                                    <p>Agência: <span>3348</span></p>
                                    <p>Conta C.: <span>12556-3</span></p>
                                    <p>Favorecido: <span>Karina s n campos contabilidade</span></p>
                                    <p>CNPJ: <span>23.458.288/0001-48</span></p>
                                    <p>Enviar comprovante para <span>karina@ktprime.com.br</span></p>
                                </div>

                                <img src="../img/pagamento/bradesco.png">
                            </div>
                        </section>
                    </div>
                </section>
            </div>
            <section id="comprovante">
                <img src="../img/fechar.png" class="fechar">
                
                <img src="../img/loading.gif" class='loading'>
                
                <div>
                    <h2>Pagamento de R$ <span class='valor'>0,00</span> realizado com sucesso!</h2>
                    <h5>Detalhes da transação:</h5>
                    <p class="descricao">Lorem ipsum dolor sit amet, consectetur adipisicing elit.</p>
                    
                    <h3>
                        <div>Valor no Cartão:</div>
                        <div>R$ <span class="valor">0,00</span></div>
                    </h3>
                    
                    <p class="codigo">761FE12B-AE8D-4BD9-B431-6CF7AC7A8EDB</p>
                </div>
            </section>  
        </div>
    </body>
    <script type="text/javascript">
        var historico = <?php echo json_encode($historico);?>;
        var servicosPrestados = <?php echo json_encode($servicosPrestados);?>;
        var historico_pagamento = <?php echo json_encode($historico_pagamento);?>;
        var empresa_historico = <?php echo json_encode($empresa_historico);?>;
        var servicos = <?php echo json_encode($servicos);?>;
        var servicosTotal = <?php echo json_encode($servicosTotal);?>;
        var pagamentos = <?php echo json_encode($pagamentos);?>;
        var id_sessao = '<?php echo $sessionCode;?>';
        var valor = <?php echo $valor;?>;
        var ids = <?php echo json_encode($ids);?>;
        var ids2 = <?php echo json_encode($ids2);?>;
        var ids3 = <?php echo json_encode($ids3);?>;
    </script>
    <script type="text/javascript" src="https://stc.pagseguro.uol.com.br/pagseguro/api/v2/checkout/pagseguro.directpayment.js"></script> 
<!--    <script type="text/javascript" src="https://stc.sandbox.pagseguro.uol.com.br/pagseguro/api/v2/checkout/pagseguro.directpayment.js"></script>-->
    <script src="../js/jquery.mask.js"></script>
    <script src="../js/empresa/financeiro.js?<? echo time(); ?>"></script>
</html>