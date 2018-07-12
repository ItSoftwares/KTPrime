<?if (!isset($_SESSION)) session_start();

require "../../php/conexao.php";
//require(dirname(__DIR__)."/php/sessao.php");
//
//verificarSeSessaoExpirou();
$titulo = "Faturamento Empresa"; 

$menu=1;
?>
<!DOCTYPE HTML>
<html>
    <head>
        <title>KT Prime - Faturamento</title>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <link rel="stylesheet" href="../css/empresa/faturamento.css" media="(min-width: 1000px)">
        <link rel="stylesheet" href="../cssmobile/empresa/faturamento.css" media="(max-width: 999px)">
        <link rel="stylesheet" href="../css/geral.css" media="(min-width: 1000px)">
        <link rel="stylesheet" href="../cssmobile/geral.css" media="(max-width: 999px)">
        <link href="../img/favicon.png" rel="shortcut icon" type="image/x-icon" />
    </head>
    
    <body>
        <?
        include("../menus.php");
        
        $faturamentos = DBselect("faturamento", "where id_empresa={$empresa->id} order by ano, mes DESC");
        
        if (count($faturamentos)==0) $faturamentos=[];
        ?>
        
        <div id="container">
            <div>
                <section id="faturamentos">
                    <h3>Faturamentos</h3>
                    
                    <div>
                        <table>
                            <tr>
                                <th>Periodo</th>
                                <th>Valor Serviço</th>
                                <th>Valor Venda</th>
                                <th>Total Bruto</th>
                                <th>Devolução</th>
                                <th>Total Liquido</th>
                                <th>% DAS</th>
                                <th>Valor DAS</th>
                                <th>Ações</th>
                            </tr>
                            <tr>
                                <td>Agosto/2017</td>
                                <td>100,00</td>
                                <td>100,00</td>
                                <td>100,00</td>
                                <td>100,00</td>
                                <td>100,00</td>
                                <td>100,00</td>
                                <td>100,00</td>
                            </tr>
                        </table>
                    </div>
                    
                    <div id="adicionar">
                        <label>Novo<img src="../img/add.png"></label>
                        <div class="clear"></div>
                    </div>
                </section>
                
            </div>
            <section id="novo-faturamento">

                <div>
                    <h3>Novo Faturamento</h3>
                    <img src="../img/fechar.png">

                    <form>
                        <div>
                            <div class="input metade">
                                <label>Mês</label>
                                <select name="mes">
                                    <option value="0">Mês</option>
                                    <option value="1">Janeiro</option>
                                    <option value="2">Fevereiro</option>
                                    <option value="3">Março</option>
                                    <option value="4">Abril</option>
                                    <option value="5">Maio</option>
                                    <option value="6">Junho</option>
                                    <option value="7">Julho</option>
                                    <option value="8">Agosto</option>
                                    <option value="9">Setembro</option>
                                    <option value="10">Outrubro</option>
                                    <option value="11">Novembro</option>
                                    <option value="12">Dezembro</option>
                                </select>
                            </div>
                            <div class="input metade">
                                <label>Ano</label>
                                <input type="number" name="ano" placeholder="Ano de Referência" required>
                            </div>
                            <div class="input metade">
                                <label>Valor Serviço</label>
                                <input type="text" name="valor_servico" placeholder="Valor Serviço">
                            </div>

                            <div class="input metade">
                                <label>Valor Venda</label>
                                <input type="text" name="valor_venda" placeholder="Valor Venda">
                            </div>

                            <div class="input metade">
                                <label>Total Bruto</label>
                                <input type="text" name="total_bruto" placeholder="Total Bruto">
                            </div>

                            <div class="input metade">
                                <label>Devolução</label>
                                <input type="text" name="devolucao" placeholder="Devolução">
                            </div>

                            <div class="input metade">
                                <label>Total Liquido</label>
                                <input type="text" name="total_liquido" placeholder="Total Liquido">
                            </div>

                            <div class="input metade">
                                <label>% DAS</label>
                                <input type="text" name="das" placeholder="DAS">
                            </div>

                            <div class="input">
                                <label>Valor DAS</label>
                                <input type="text" name="imposto" placeholder="Imposto">
                            </div>
                        </div>

                        <button>Finalizar</button>
                    </form>
                </div>
            </section>
        </div>
    </body>
    <script type="text/javascript">
        var faturamentos = <? echo json_encode($faturamentos); ?>;
    </script>
    <script src="../js/contador/empresa/faturamento.js"></script>
</html>