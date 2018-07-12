<?

if (!isset($_SESSION)) session_start();



require "../php/conexao.php";

require(dirname(__DIR__)."/php/sessao.php");



verificarSeSessaoExpirou();



$titulo = "Solicitações"; 



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

        include("menus.php");

        

        $solicitacoes = DBselect("solicitacao", "where id_empresa={$usuario->id}");

        

        if (count($solicitacoes)==0) $solicitacoes=[];

        

        $qtd_sol = 0;

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

                

               <div id="adicionar">

                   <h3>Nova Solicitação</h3>

                   <form>

                       <div class="input" style="display: none">

                           <label>Serviço</label>

                           <select name="id_servico">

                               <option value="0">Escolha</option>

                               

                           </select>

                       </div>

                       <div class="input">

                           <label>Descrição</label>

                           <input type="text" name="descricao" placeholder="Observações sobre a solicitação" required>

                       </div>

                       <label id="anexo"><img src="../img/anexo.png"></label>

                       <input type="file" name="image-upload" id="image-upload"/>

                       <button class="botao">Nova</button>

                   </form>

               </div>

               

               <section id="solicitacoes">

                   <h3>Solicitações Feitas</h3>

                    <div>

                        <table>

                            <tr>

                                <th>Estado</th>

                                <th>Descrição</th>

                                <th>Data</th>

<!--                                <th>Serviço</th>-->

                                <th>Resposta</th>

                                <th>Anexo</th>

                                <th></th>

                            </tr>

                        </table>

                    </div>

                </section>

                

                <div class="fundo-extra" id="upload-imagem">

                    <div id="upload">

                        <form>

                            <input type="file" name="image-upload" id="image-upload"/>

                        </form>

                        <label for="image-upload">

                            <img src="../img/upload.png">

                            <p>Arraste a Imagem para cá ou Clique bara buscar.</p>

                            <p id="nome"></p>

                        </label>

                    </div>

                    <div id="button">

                        <button>OK</button>

                    </div>

                    <img src="../img/fechar.png">

                </div>

                

                <div id="resposta">

                    <div>

                        <h3>Resposta da Contabilidade</h3>

                        <p>Lorem ipsum dolor sit amet, consectetur adipisicing elit. Asperiores blanditiis, laborum sequi dolor, earum quibusdam, nam numquam praesentium quo beatae odio modi rem sapiente. Facere repudiandae blanditiis enim aspernatur. Labore?</p>

                        <button>Fechar</button>

                    </div>

                    <img src="../img/fechar.png" alt="">

                </div>

            

            </div>

        </div>

    </body>

    <script type="text/javascript">

        var solicitacoes = <? echo json_encode($solicitacoes); ?>;

        var servicos = <? echo json_encode($servicos); ?>;

    </script>

    <script src="../js/empresa/solicitacoes.js?<? echo time(); ?>"></script>

</html>