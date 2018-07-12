<?if (!isset($_SESSION)) session_start();

require "../php/conexao.php";
require(dirname(__DIR__)."/php/sessao.php");

verificarSeSessaoExpirou();
$titulo = "Alvarás"; 
?>
<!DOCTYPE HTML>
<html>
    <head>
        <title>KT Prime - Alvarás</title>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <link rel="stylesheet" href="../css/contador/certificados.css" media="(min-width: 1000px)">
        <link rel="stylesheet" href="../cssmobile/contador/certificados.css" media="(max-width: 999px)">
        <link rel="stylesheet" href="../css/geral.css" media="(min-width: 1000px)">
        <link rel="stylesheet" href="../cssmobile/geral.css" media="(max-width: 999px)">
        <link href="../img/favicon.png" rel="shortcut icon" type="image/x-icon" />
    </head>
    
    <body>
        <?
        include("menus.php");
        
        $alvaras = DBselect("empresa", "where tipo_alvara_1 IS NOT NULL or tipo_alvara_2 IS NOT NULL order by id DESC", "tipo_alvara_1, tipo_alvara_2, vencimento_alvara_1, vencimento_alvara_2, razao_social, id");
        ?>
        <div id="container">
            <div>
            
                <section id="certificados">
                   <h3>Lista de Alvarás</h3>
                   
                   <div id="pesquisar">
                        <input type="text" placeholder="Pesquisar">
                        <img src="../img/lupa.png">
                    </div>
                   
                    <div>
                        <table class="tabela-spacer">
                            <tr>
                                <th>Empresa</th>
                                <th>Tipo</th>
                                <th>Validade</th>
                                <th></th>
                            </tr>

                            <tr class="spacer"><td></td></tr>
                        </table>
                    </div>
                </section>
            
            </div>
        </div>
    </body>
    
    <script type="text/javascript">
        var alvaras = <? echo json_encode($alvaras); ?>;
        
    </script>
    <script type="text/javascript" src='../js/contador/alvaras.js?<? echo time() ?>'></script>
</html>