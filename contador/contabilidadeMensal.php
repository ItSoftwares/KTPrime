<?if (!isset($_SESSION)) session_start();

require "../php/conexao.php";
require(dirname(__DIR__)."/php/sessao.php");

verificarSeSessaoExpirou();
$titulo = "Contabilidade Mensal"; 
// echo "<pre>";
// var_dump($_SESSION); exit;
?>
<!DOCTYPE HTML>
<html>
    <head>
        <title>KT Prime - Contabilidade Mensal</title>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <link rel="stylesheet" href="../css/contador/contabilidadeMensal.css" media="(min-width: 1000px)">
        <link rel="stylesheet" href="../cssmobile/contador/contabilidadeMensal.css" media="(max-width: 999px)">
        <link rel="stylesheet" href="../css/geral.css" media="(min-width: 1000px)">
        <link rel="stylesheet" href="../cssmobile/geral.css" media="(max-width: 999px)">
        <link href="../img/favicon.png" rel="shortcut icon" type="image/x-icon" />
    </head>
    
    <body>
        <?
        if ($_SESSION['usuario_logado']==1) include("menus.php");
        else if ($_SESSION['usuario_logado']==3) include("../funcionario/menus.php");

        $empresas = DBselect("empresa","where estado_conta<>0 order by funcionarios DESC, prolabore DESC, razao_social ASC","razao_social, id, funcionarios, prolabore, estado_conta");
        $mensal = DBselect("mensal", "order by ano DESC, mes DESC");
        $atual = -1;
        
        ?>
        <div id="container">
            <div>
            
                <section id="mensal">
                   <h3>Contabilidade Mensal</h3>
                    
                    <div class="input">
                        <select id="periodo">
                            <? foreach ($mensal as $key => $value) {
                                if ($atual==-1) $atual = $key;
                            ?>  
                            <option value="<? echo $key ?>"><? echo $value['mes']." - ".$value['ano']; ?></option>
                            <?
                            } ?>
                        </select>
                    </div>
                    <div class="clear"></div>

                   <div>
                       <table class="tabela-spacer">
                           <tr>
                               <th></th>
                               <th>Empresa</th>
                               <th class="center">Funcionario</th>
                               <th class="center">Prolabore</th>
                           </tr>

                           <? foreach ($empresas as $key => $value) { ?>
                            <tr class="spacer"><td></td></tr>
                               
                            <tr data-id="<? echo $value['id']; ?>">
                                <td data-nome="Feito"><input type="checkbox" ></td>
                                <td data-nome="Empresa"><? echo $value['razao_social']; ?></td>
                                <td data-nome="Funcionario" class="img center"><? echo $value['funcionarios']==1?"<img src='../img/input-mark.png' class='tem'>":""; ?></td>
                                <td data-nome="Prolabore" class="img center"><? echo $value['prolabore']==1?"<img src='../img/input-mark.png' class='tem'>":""; ?></td>
                            </tr>
                            
                           <? } ?>

                           <tr class="spacer"><td></td></tr>
                       </table>
                   </div>
                </section>

                <img src="../img/add.png" id="adicionar">

                <section id="novo" class="fundo">
                    <div>
                        <h3>Nova atividade</h3>
                        <img src="../img/fechar.png" class="fechar">
                        <form>
                            <div class="input metade">
                                <label>Valor do desconto</label>
                                <select name="mes">
                                    <option value="1">Janeiro</option>
                                    <option value="2">Fevereiro</option>
                                    <option value="3">Março</option>
                                    <option value="4">Abril</option>
                                    <option value="5">Maio</option>
                                    <option value="6">Junho</option>
                                    <option value="7">Julho</option>
                                    <option value="8">Agosto</option>
                                    <option value="9">Setembro</option>
                                    <option value="10">Outubro</option>
                                    <option value="11">Novembro</option>
                                    <option value="12">Dezembro</option>
                                </select>
                            </div>
                            <div class="input metade">
                                <label>Ano</label>
                                <input type="number" placeholder="Valor do desconto em R$" name="ano" required="" step="1" value="<? echo date("Y") ?>">
                            </div>

                            <button class="botao">Salvar</button>
                            <div class="clear"></div>
                        </form>
                    </div>
                </section>
            
            </div>
        </div>
    </body>
    
    <script type="text/javascript">
        var empresas = <? echo json_encode($empresas) ?>;
        var mensal = <? echo json_encode($mensal) ?>;
        var atual = <? echo $atual ?>;
    </script>
    <script type="text/javascript" src='../js/contador/contabilidadeMensal.js?<? echo time() ?>'></script>
</html>