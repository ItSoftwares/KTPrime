<?if (!isset($_SESSION)) session_start();
 
require "../../php/conexao.php";
//require(dirname(__DIR__)."/php/sessao.php");
//
//verificarSeSessaoExpirou();
$titulo = "Obrigações Empresa"; 
$menu = 1;

?>
<!DOCTYPE HTML>
<html>
    <head>
        <title>KT Prime - Obrigações</title>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <link rel="stylesheet" href="../css/empresa/obrigacoes.css?<? echo time(); ?>" media="(min-width: 1000px)">
        <link rel="stylesheet" href="../cssmobile/empresa/obrigacoes.css?<? echo time(); ?>" media="(max-width: 999px)">
        <link rel="stylesheet" href="../css/geral.css" media="(min-width: 1000px)">
        <link rel="stylesheet" href="../cssmobile/geral.css" media="(max-width: 999px)">
        <link href="../img/favicon.png" rel="shortcut icon" type="image/x-icon" />
    </head>
    
    <body>
        <?
        include("../menus.php");
        
        $obrigacoes = DBselect("obrigacao", "where id_empresa={$empresa->id} order by data_realizar DESC");
        
        if (count($obrigacoes)==0) $obrigacoes=[];
        ?>
        
        <div id="container">
            <div>
                <div id="adicionar">
                   <h3>Nova Obrigação</h3>
                   <img src="../img/fechar.png" id="cancelar" title="Cancelar">
                   <form enctype="multipart/form-data">
                       
                       <div class="input">
                            <label>Periodo</label>
                            <select name="periodo">
                                <option value="1">Mensal</option>
                                <option value="2">Bimestral</option>
                                <option value="3">Trimestral</option>
                                <option value="4">Semestral</option>
                                <option value="5">Anual</option>
                            </select>
                        </div>
                       <div class="input">
                           <label>Data a ser Realizado</label>
                           <input type="text" name="data_realizar" placeholder="Ex.: 00/00/0000" data-mask='00/00/0000'> 
                       </div>
                       <div class="input">
                           <label>Descrição</label>
                           <input type="text" name="descricao" placeholder="Observações sobre a obrigação" required>
                       </div>
                       
                       <label id="anexo"><img src="../img/anexo.png"></label> 
                       <input type="file" name="image-upload" id="image-upload"/>
                       <button class="botao">Criar</button>
                       <div class="clear"></div>
                   </form>
                    <p id="nome-anexo">Sem anexo</p>
                    <div class="clear"></div>
               </div>
            
                <section id="obrigacoes">
                    <h3>Obrigações</h3>
                    <p>Obrigações desta empresa.</p>
<!--                    <p>Abaixo estão listados documentos, serviços solicitados a contabilidade.</p>-->
                    
                    <div id="estado">
                        <input type="text" placeholder="Mês" name="mes">
                        <input type="text" placeholder="Ano" name="ano">
                        
<!--                        <label for="periodo-select">Estado</label>-->
                        <select id="periodo-select">
                            <option value="0">Todas</option>
                            <option value="1">Em aberto</option>
                            <option value="2">Concluida</option>
                        </select>
                    </div>
                    
                    <div>
                        <table class="tabela-spacer">
                            <tr>
                                <th></th>
                                <th>Periodo</th>
                                <th>Descrição</th>
                                <th>Realizado</th>
                                <th>Para</th>
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
        var obrigacoes = <? echo json_encode($obrigacoes); ?>;
    </script>
    <script src="../js/contador/empresa/obrigacoes.js?<? echo time() ?>"></script>
    <script src="../js/jquery.mask.js"></script>
</html>