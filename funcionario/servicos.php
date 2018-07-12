<?if (!isset($_SESSION)) session_start(); 

require "../php/conexao.php";

require(dirname(__DIR__)."/php/sessao.php");



verificarSeSessaoExpirou();

$titulo = "Servicos"; 



$servicos = DBselect("servico");



?>

<!DOCTYPE HTML>

<html>

    <head>

        <title>KT Prime - Serviços</title>

        <meta charset="utf-8">

        <meta name="viewport" content="width=device-width, initial-scale=1">

        <link rel="stylesheet" href="../css/contador/servicos.css" media="(min-width: 1000px)">

        <link rel="stylesheet" href="../cssmobile/contador/servicos.css" media="(max-width: 999px)">

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

                <section id="servicos">

                   <h3>Lista de Solicitações</h3>

                   

                    <div class="maior">

                        <table>

                            <tr>

                                <th data-index="nome" class="selecionado">Serviço</th>

                                <th data-index="valor">Valor</th>

                                <th>Ação</th>

                            </tr>

                        </table>

                    </div>

                </section>

            

            </div>

        </div>

    </body>

    

    <script>

        var servicos = <? echo json_encode($servicos); ?>;

        var tela_funcionario = true;

    </script>

    <script src="../js/jquery.mask.js"></script>

    <script src="../js/contador/servicos.js"></script>

</html>