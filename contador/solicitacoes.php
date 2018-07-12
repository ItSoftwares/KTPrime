<?if (!isset($_SESSION)) session_start();



$titulo = "Solicitacoes"; 

require "../php/conexao.php";

require(dirname(__DIR__)."/php/sessao.php");



verificarSeSessaoExpirou();



$solicitacoes = DBselect("solicitacao s INNER JOIN empresa e ON s.id_empresa = e.id", "order by data DESC", "s.*, e.razao_social as empresa");

if (count($solicitacoes)==0) $solicitacoes=[];

?>

<!DOCTYPE HTML>

<html>

    <head>

        <title>KT Prime - Solicitações</title>

        <meta charset="utf-8">

        <meta name="viewport" content="width=device-width, initial-scale=1">

        <link rel="stylesheet" href="../css/contador/solicitacoes.css" media="(min-width: 1000px)">

        <link rel="stylesheet" href="../cssmobile/contador/solicitacoes.css" media="(max-width: 999px)">

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

                <div id="acesso-rapido">

                    <ul>

                        <li class="selecionado">

                            <div><img src="../img/menu.png"></div>

                           <span class="bola"></span>

                            <div>

                                <h4>10 Solicitações</h4>

                                <p>Total</p>

                            </div>

                        </li>

                        

                        <li>

                            <div><img src="../img/exclamacao.png"></div>

                           <span class="bola"></span>

                            <div>

                                <h4>10 Solicitações</h4>

                                <p>Pendentes</p>

                            </div>

                        </li>

                        

                        <li>

                            <div><img src="../img/input-mark.png"></div>

                            <span class="bola"></span>

                            <div>

                                <h4>10 Solicitações</h4>

                                <p>Respondidas</p>

                            </div>

                        </li>

                    </ul>

                </div>

                

                <section id="solicitacoes">

                   <h3>Solicitações</h3>

                   

                   <div id="pesquisa">

                       <!-- <div id="nome"> -->

                           <input type="text" placeholder="Pesquisar Empresa">

                       <!-- </div> -->

                       

                       <!-- <div id="data"> -->

                           <input type="text" placeholder="Data 00/00/0000">

                       <!-- </div> -->

                   </div>

                   

                    <div>

                        <table class="tabela-spacer">

                            <tr>

                                <th>Estado</th>

                                <th>Empresa</th>

                                <th>Data</th>

                                <th>Descrição</th>

                                <th>Anexo</th>

                                <th>Responder</th>

                            </tr>



                            <tr class="spacer"><td></td></tr>



                            <tr>

                                <td>Nome da Empresa</td>

                                <td>17/09/2017, 15:29</td>

                                <td>Uma breve descrição da Soli...</td>

                                <td><img src="../img/responder.png"></td>

                            </tr>

                            <tr class="spacer"><td></td></tr>

                            <tr>

                                <td>Nome da Empresa</td>

                                <td>17/09/2017, 15:29</td>

                                <td>Uma breve descrição da Soli...</td>

                                <td><img src="../img/responder.png"></td>

                            </tr>

                            

                        </table>

                    </div>

                </section>

                

                <section id="responder">

                    

                    <div>

                        <img src="../img/fechar.png">

                        <h3>Responder Solicitação</h3>

                        <form action="">



                            <p class="descricao">Lorem ipsum dolor sit amet, consectetur adipisicing elit. Quisquam, necessitatibus dignissimos quia! Molestiae, rerum nisi eveniet architecto atque eos ducimus minima! Amet minus, odio! Maiores reiciendis esse, facere rem tenetur!</p>



                            <div class="input">

                                <textarea name="resposta" placeholder="Digite uma resposta para o seu cliente" required></textarea>

                            </div>



                            <button class="botao">Enviar</button>

                       </form>

                    </div>

                </section>

            </div>

        </div>

    </body>

    <script type="text/javascript">

        var servicos = <? echo json_encode($_SESSION['servicos']); ?>;

        var solicitacoes = <? echo json_encode($solicitacoes); ?>;

    </script>

    <script src="../js/jquery.mask.js"></script>

    <script src="../js/contador/solicitacoes.js?<? echo time(); ?>"></script>

</html>