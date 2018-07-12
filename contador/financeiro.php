<?if (!isset($_SESSION)) session_start();

require "../php/conexao.php";
require(dirname(__DIR__)."/php/sessao.php");

verificarSeSessaoExpirou();
$titulo = "Financeiro"; 

?> 
<!DOCTYPE HTML>
<html>
    <head>
        <title>KT Prime - Financeiro</title>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <link rel="stylesheet" href="../css/contador/financeiro.css" media="(min-width: 1000px)">
        <link rel="stylesheet" href="../cssmobile/contador/financeiro.css" media="(max-width: 999px)">
        <link rel="stylesheet" href="../css/geral.css" media="(min-width: 1000px)">
        <link rel="stylesheet" href="../cssmobile/geral.css" media="(max-width: 999px)">
        <link href="../img/favicon.png" rel="shortcut icon" type="image/x-icon" />
        <link href="../img/favicon.png" rel="shortcut icon" type="image/x-icon" />
    </head>
    
    <body>
        <? 
        include("menus.php");
        
        $historico = DBselect("historico_servicos h INNER JOIN empresa e ON h.id_empresa = e.id","order by ano DESC, mes DESC, e.razao_social ASC","h.*, e.razao_social, e.dia_vencimento, concat_ws(' ',e.razao_social, e.apelido, e.codigo_cliente) as apelido");
        $pagamentos = DBselect("pagamento p INNER JOIN empresa e ON p.id_empresa = e.id", "order by time DESC", "p.*, e.razao_social as empresa");
        $historico_pagamento = DBselect("historico_pagamento");
        $servicosPrestados = DBselect("servico_prestado", "order by data DESC");
        $servicos = DBselect("servico", "");
        
        $temp = [];
        if (count($pagamentos)>0) {
            foreach($pagamentos as $key => $pag) {
                $temp[$pag['id']] = $pag;
            }
        }
        $pagamentos = $temp;
        ?>
        <div id="container">
            <div>
                <section id="acesso-rapido">
                    <ul>
                        <li data-filtro=0 class="selecionado">
                            <div><img src="../img/dinheiro.png"></div>
                            <span class="bola"></span>
                            <div>
                                <h4>0 Históricos</h4>
                                <p>Todos</p>
                            </div>
                        </li>
                        
                        <li data-filtro=1>
                            <div><img src="../img/input-mark.png"></div>
                            <span class="bola"></span>
                            <div>
                                <h4>0 Históricos</h4>
                                <p>Pagos</p>
                            </div>
                        </li>
                        
                        <li data-filtro=2>
                            <div><img src="../img/exclamacao.png"></div>
                            <span class="bola"></span>
                            <div>
                                <h4>0 Históricos</h4>
                                <p>Pendentes</p>
                            </div>
                        </li>
                         
                        <li data-filtro=3>
                            <div><img src="../img/pare.png"></div>
                            <span class="bola"></span>
                            <div>
                                <h4>0 Históricos</h4>
                                <p>Inadimplentes</p>
                            </div>
                        </li>
                    </ul>
                </section>
                
                <section id="financeiro">
                    <h3>Histórico Financeiro</h3>
                    <p>Abaixo uma lista dos serviçoes executados para os clientes!</p>
                    
                    <div id="pesquisar">
                        <select id="ano-mes">
                            <option value="0-0">Todos</option>
                        </select>
                        <input type="text" placeholder="Pesquisar empresa">
                        <img src="../img/lupa.png" alt="">
                    </div>
                    
                    <div id="periodo">
                        <span class="ano selecionado">Geral</span>
                    </div>
                    
                    <section id="valores">
                        <table>
                            
                        </table>
                        
                    </section>
                    <h3 id="total"><span>Pendente: R$ 2.500,00</span><span>Total: R$ 2.500,00</span></h3>
                </section>
                
                <section id="pagamentos">
                    <h2>Pagamentos</h2>
                    
                     <div id="pag-container">
                        <table class="tabela-spacer">
                            <?
                            foreach($pagamentos as $pag) {
                                $estado ="";
                                
                                if ($pag['estado']<=2) {
                                    $estado = "<img src='../img/exclamacao.png' class='pendente'>Pendente";
                                } else if ($pag['estado']==3 || $pag['estado']==4) {
                                    $estado = "<img src='../img/input-mark.png' class='pago'>Pago";
                                } else {
                                    $estado = "<img src='../img/pare.png' class='cancelado'>Cancelado";
                                }
                            ?>
                            <tr data-id='<? echo $pag['id']; ?>'>
                                <td data-nome='Estado'><? echo $estado; ?></td>
                                <td data-nome='Empresa'><b><? echo substr($pag['empresa'], 0, 20); ?>.</b></td>
                                <td data-nome='Data'><? echo date("d/m/Y, H:i", $pag['time']); ?></td>
                                <td data-nome='Descricao'><? echo $pag['descricao']; ?></td>
                                <td data-nome='Tipo'><? echo $pag['tipo']==1?"<img src='../img/pagamento/cartao-credito.png'>Cartão":($pag['tipo']==2?"<img src='../img/pagamento/boleto.png'>Boleto":($pag['tipo']==3?"<img src='../img/pagamento/manual.png'>Manual":"<img src='../img/pagamento/manual.png'>Parcial")); ?></td>
                                <td data-nome='Valor'>R$ <? echo number_format($pag['valor'],2,",", "."); ?></td>
                            </tr>
                            <tr class="spacer"><td></td></tr>
                            <?
                            }
                            ?>
<!--
                            <tr>
                                <td data-nome='Estado'><img src='../img/exclamacao.png' class='pendente'>Pendente</td>
                                <td data-nome='Data'>24/10/2017, 15:35</td>
                                <td data-nome='Descricao'>Um breve descrição do pagamento</td>
                                <td data-nome='Tipo'><img src='../img/pagamento/cartao-credito.png'>Cartão</td>
                                <td data-nome='Valor'>552,96</td>
                            </tr>
-->
                        </table>
                    </div>
                </section>
                
                <section id="desconto">
                    <div>
                        <h3>Dar desconto</h3>
                        <img src="../img/fechar.png">
                        <form>
                            <div class="input">
                                <label>Valor do desconto</label>
                                <input type="number" placeholder="Valor do desconto em R$" name="desconto" required>
                            </div>

                            <button class="botao">Salvar</button>
                        </form>
                    </div>
                </section>
                
                <section id="pagar">
                    <div>
                        <h3>Pagar</h3>
                        <img src="../img/fechar.png">
                        <form>
                            <div class="metade">
                                <div class="input">
                                    <label>Tipo de pagamento</label>
                                    <select id="tipo">
                                        <option value="0">Total</option>
                                        <option value="1">Parcial</option>
                                    </select>
                                </div>
                                <div class="input">
                                    <label>Valor do Pagamento</label>
                                    <input type="number" placeholder="Valor do pagamento em R$" name="valor" required>
                                </div>
                            </div>

                            <button class="botao">Pagar</button>
                        </form>
                    </div>
                </section>
            </div>
        </div>
    </body>
    
    <script type="text/javascript">
        var servicos = <? echo json_encode($servicos); ?>;
        var servicosPrestados = <? echo json_encode($servicosPrestados); ?>;
        var historico = <? echo json_encode($historico); ?>;
        var pagamentos = <? echo json_encode($pagamentos); ?>;
        var historico_pagamento = <? echo json_encode($historico_pagamento); ?>;
    </script>
    <script src="../js/contador/financeiro.js?<? echo time(); ?>"></script> 
</html>