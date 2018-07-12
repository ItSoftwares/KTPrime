<?if (!isset($_SESSION)) session_start();

require "../php/conexao.php";
//require(dirname(__DIR__)."/php/sessao.php");
//
//verificarSeSessaoExpirou();
$titulo = "Faturamento"; 

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
        include("menus.php");
        
        $faturamentos = DBselect("faturamento", "where id_empresa={$usuario->id} order by ano, mes DESC");
        
        if (count($faturamentos)==0) $faturamentos=[];
        ?>
        
        <div id="container">
            <div>
                <section id="faturamentos">
                    <h3></h3>
                    
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
                </section>
                
            </div>
            
        </div>
    </body>
    <script type="text/javascript">
        var faturamentos = <? echo json_encode($faturamentos); ?>;
        var empresa = true;
    </script>
    <script src="../js/contador/empresa/faturamento.js?<? echo time(); ?>"></script>
</html>