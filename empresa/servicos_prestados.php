<?

if (!isset($_SESSION)) session_start();



require "../php/conexao.php";

require(dirname(__DIR__)."/php/sessao.php");



verificarSeSessaoExpirou();



$titulo = "Serviços Prestados"; 





?>

<!DOCTYPE HTML>

<html>

    <head>

        <title>KT Prime - Serviços Prestado</title>

        <meta charset="utf-8">

        <meta name="viewport" content="width=device-width, initial-scale=1">

        <link rel="stylesheet" href="../css/empresa/servicos_prestados.css" media="(min-width: 1000px)">

        <link rel="stylesheet" href="../cssmobile/empresa/servicos_prestados.css" media="(max-width: 999px)">

        <link rel="stylesheet" href="../css/geral.css" media="(min-width: 1000px)">

        <link rel="stylesheet" href="../cssmobile/geral.css" media="(max-width: 999px)">

        <link href="../img/favicon.png" rel="shortcut icon" type="image/x-icon" />

    </head>

    

    <body>

        <?

        include("menus.php");

        

        $servicosPrestados = DBselect('servico_prestado', "where id_empresa={$usuario->id}");

        $servicos = DBselect('servico');

        

        $temp = [];

        foreach($servicos as $servico) {

            $temp[$servico['id']] = $servico;

        }

        $servicos = $temp;

        ?>

        

        <div id="container">

            <div>

            

                <section id="servicos-prestados">

                    <h3>Serviços prestados</h3>

                    

                    <div>

                        <table>

                            <tr>

                                <th></th>

                                <th>Serviço</th>

                                <th>Descrição</th>

                                <th>Data</th>

                                <th>Anexo</th>

                            </tr>

                            <tr class="spacer"><td></td></tr>

                            <tr>

                                <td><img src="../img/exclamacao.png" class="pendente"></td>

                                <td>Teste de Obrigação para essa empresa...</td>

                                <td title="Quando esa obrigação for realizada, clique nesse botão!"><button>Finalizar</button></td>

                                <td>06/09/2017, 12:25</td>

                                <td><img src="../img/anexo.png" class="sem-anexo" title="Não possui anexos"></td>

                            </tr>

                            <tr class="spacer"><td></td></tr>

                            <tr>

                                <td><img src="../img/input-mark.png"></td>

                                <td>Teste de Obrigação para essa empresa...</td>

                                <td title="Quando esa obrigação for realizada, clique nesse botão!"><button disabled>Finalizar</button></td>

                                <td>06/09/2017, 12:25</td>

                                <td><img src="../img/anexo.png" title="Possui Anexos"></td>

                            </tr>

                        </table>

                    </div>

                </section>

            

            </div>

        </div>

    </body>

    <script type="text/javascript">

        var servicosPrestados= <? echo json_encode($servicosPrestados); ?> ;

        var servicos= <? echo json_encode($servicos); ?> ;

        var empresa= <? echo json_encode($usuario->toArray()); ?> ;

    </script>

    <script src="../js/empresa/servicos_prestados.js"></script>

</html>