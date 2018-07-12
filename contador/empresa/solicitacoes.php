<?if (!isset($_SESSION)) session_start();



require "../../php/conexao.php";

//require(dirname(__DIR__)."/php/sessao.php");

//

//verificarSeSessaoExpirou();

$titulo = "Solicitações Empresa"; 

$menu = 1;

$servicos = DBselect("servico", "", "nome, id");



?>

<!DOCTYPE HTML>

<html>

    <head>

        <title>KT Prime - Solicitações</title>

        <meta charset="utf-8">

        <meta name="viewport" content="width=device-width, initial-scale=1">

        <link rel="stylesheet" href="../css/empresa/solicitacoes.css" media="(min-width: 1000px)">

        <link rel="stylesheet" href="../cssmobile/empresa/solicitacoes.css" media="(max-width: 999px)">

        <link rel="stylesheet" href="../css/geral.css" media="(min-width: 1000px)">

        <link rel="stylesheet" href="../cssmobile/geral.css" media="(max-width: 999px)">

        <link href="../img/favicon.png" rel="shortcut icon" type="image/x-icon" />

    </head>

    

    <body>

        <?

        include("../menus.php");



        $qtd_sol = 0;

        $solicitacoes = DBselect("solicitacao s INNER JOIN empresa e ON s.id_empresa = e.id", "where id_empresa={$empresa->id}","e.razao_social as empresa, s.*");

        if (count($solicitacoes)==0) $solicitacoes=[];

        foreach ($solicitacoes as $sol) {

            if ($sol['respondido']==1) {

                $qtd_sol++;

            }

        }

        ?>

        

        <div id="container">

            <div>

                

                <div id="acesso-rapido">

                    <ul>

                        <li class="selecionado">

                            <div><img src="../img/menu.png"></div>

                           <span class="bola"></span>

                            <div>

                                <h4><? echo count($solicitacoes); ?> Solicitações</h4>

                                <p>Total</p>

                            </div>

                        </li>

                        

                        <li>

                            <div><img src="../img/exclamacao.png"></div>

                           <span class="bola"></span>

                            <div>

                                <h4><? echo count($solicitacoes)-$qtd_sol; ?> Solicitações</h4>

                                <p>Pendentes</p>

                            </div>

                        </li>

                        

                        <li>

                            <div><img src="../img/input-mark.png"></div>

                            <span class="bola"></span>

                            <div>

                                <h4><? echo $qtd_sol; ?> Solicitações</h4>

                                <p>Respondidas</p>

                            </div>

                        </li>

                    </ul>

                </div>

                

                <section id="solicitacoes">

                   <h3>Solicitações Feitas</h3>

                   

                   <div id="pesquisa">

                       <div id="data">

                           <input type="text" placeholder="Data 00/00/0000">

                       </div>

                   </div>

                   

                    <div>

                        <table>

                            <tr>

                                <th>Estado</th>

                                <th>Descrição</th>

                                <th>Data</th>

                                <th>Serviço</th>

                                <th>Anexo</th>

                                <th>Responder</th>

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



                            <button>Enviar</button>

                       </form>

                    </div>

                </section>

            

            </div>

        </div>

    </body>

    <script type="text/javascript">

        var solicitacoes = <? echo json_encode($solicitacoes); ?>;

        var servicos = <? echo json_encode($servicos); ?>;

    </script>

    <script src="../js/jquery.mask.js"></script>

    <script src="../js/contador/solicitacoes.js"></script>

</html>