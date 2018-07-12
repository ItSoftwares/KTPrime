<?

//date_default_timezone_set("America/Sao_Paulo");



if (!isset($_SESSION)) session_start(); 

 

require "../../php/conexao.php";

//require(dirname(__DIR__)."/php/sessao.php");

//

//verificarSeSessaoExpirou();

$titulo = "Serviços Prestados Empresa"; 

$menu = 1;



?>

<!DOCTYPE HTML> 

<html>

    <head>

        <title>KT Prime - Serviços Prestados</title>

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

        include("../menus.php");

        

        $servicos_prestados = DBselect("servico_prestado", "where id_empresa={$empresa->id} order by data DESC");

        $historico_pagamentos = DBselect("historico_pagamento p INNER JOIN historico_servicos s ON p.id_historico = s.id INNER JOIN pagamento pag ON p.id_pagamento = pag.id", "where s.id_empresa={$empresa->id}", "p.*, pag.estado");

        $servicos_mensais = DBselect("servico_mensal", "where id_empresa={$empresa->id}");

        $servicos = DBselect("servico", "order by nome ASC");

        $temp = [];

        if (count($servicos_mensais)>0) {

            foreach($servicos_mensais as $serv) {

                $temp[$serv['id_servico']] = $serv;

            }

        }

        $servicos_mensais = $temp;

        ?>

        

        <div id="container">

            <div>

            

                <div id="adicionar">

                   <h3>Novo Serviço Prestado</h3>

                   <form enctype="multipart/form-data">

                       <div class="input">

                           <label>Serviço</label>

                           <select name="id_servico" required title="Caso não seja escolhido um serviço, não será aplicado cobrança sobre essa obrigação">

<!--                               <option value="0">Nenhum</option>-->

                               <?

                               $temp = [];

                                foreach($servicos as $servico) {

//                                    if ($servico['id']==1) continue;

                                    echo "<option value='{$servico['id']}'>{$servico['nome']}</option>";

                                    $temp[$servico['id']] = $servico;

                                }

                               $servicos = $temp;

                               ?>

                           </select>

                       </div>

                       <div class="input">

                           <label>Data</label>

                           <input type="text" name="data" placeholder="Data 00/00/0000 " required>

                       </div>

                       <div class="input">

                           <label>Descrição</label>

                           <input type="text" name="descricao" placeholder="Descrição do serviço executado" required>

                       </div>

                       <label id="anexo"><img src="../img/anexo.png"></label>

                       <input type="file" name="image-upload" id="image-upload"/>

                       <button>Criar</button>

                       <div class="clear"></div>

                   </form>
                    <p id="nome-anexo">Sem anexo</p>
                    <div class="clear"></div>
               </div>

            

                <section id="servicos-prestados">

                    <h3>Serviços Prestados</h3>

                    

<!--

                    <div id="estado">

                        <label for="periodo-select">Estado</label>

                        <select id="periodo-select">

                            <option value="0">Todas</option>

                            <option value="1">Em aberto</option>

                            <option value="2">Concluida</option>

                        </select>

                    </div>

-->

                    

                    <div>

                        <table>

                            <tr>

                                <th></th>

                                <th>Serviço</th>

                                <th>Descrição</th>

                                <th>Data</th>

                                <th>Anexo</th>

                                <th>Ações</th>

                            </tr>

                            

                        </table>

                    </div>

                </section>

            

                <div class="fundo-extra" id="upload-imagem">

                    <div id="upload">

                        <form>

                            <input type="file" name="image-upload" id="image-upload-2"/>

                        </form>

                        <label for="image-upload-2">

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

            </div>

        </div>

    </body>

    <script type="text/javascript">

        var empresa = <? echo json_encode($empresa->toArray()); ?>;

        var servicosPrestados = <? echo json_encode($servicos_prestados); ?>;

        var servicosMensais = <? echo json_encode($servicos_mensais); ?>;

        var historicoPagamentos = <? echo json_encode($historico_pagamentos); ?>;

        var servicos = <? echo json_encode($servicos); ?>;

    </script>

    <script src="../js/jquery.mask.js"></script>

    <script src="../js/contador/empresa/servicos_prestados.js?<? echo time() ?>"></script>

</html>