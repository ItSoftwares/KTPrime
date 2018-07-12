<?if (!isset($_SESSION)) session_start();?>
<!DOCTYPE HTML>
<? 
require "../php/conexao.php";
require "../php/listarArquivos.php";
require(dirname(__DIR__)."/php/sessao.php");

verificarSeSessaoExpirou();

$titulo = "Arquivos"; 

?>
<html>
    <head>
        <title>KT Prime - Financeiro</title>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <link rel="stylesheet" href="../css/empresa/arquivos.css" media="(min-width: 1000px)">
        <link rel="stylesheet" href="../cssmobile/empresa/arquivos.css" media="(max-width: 999px)">
        <link rel="stylesheet" href="../css/geral.css" media="(min-width: 1000px)">
        <link rel="stylesheet" href="../cssmobile/geral.css" media="(max-width: 999px)">
        <link href="../img/favicon.png" rel="shortcut icon" type="image/x-icon" />
    </head>
    
    <body>
        <?
        include("menus.php");

        $lista = listar(realpath(dirname(__DIR__).DIRECTORY_SEPARATOR."servidor".DIRECTORY_SEPARATOR."empresa".DIRECTORY_SEPARATOR.$usuario->id.DIRECTORY_SEPARATOR));
        $lista = utf8ize($lista);
//        var_dump($lista);
        ?>
        
        <div id="container">
            <div>
            
                <section id="arquivos">
                    <h3>Documentos da Empresa</h3>
                    <p>Abaixo estão listados os documentos de sua empresa, enviados pela contabilidade.</p>
                    
                    <div>
                        <div id="arvore">
                           <h4 class="nome-pasta">Nome da empresa</h4>
                            <ul class="pasta" data-aberto=0>
                                <li class="arquivo outro">Documento 1</li>
                                <li class="arquivo pdf">Documento 2</li>
                                <h4 class="nome-pasta">Imagens</h4>
                                <ul class="pasta" data-aberto=0>
                                    <li class="arquivo imagem">Imagem 1</li>
                                </ul>
                                <li class="arquivo outro">Documento 3</li>
                                <li class="arquivo outro">Documento 1</li>
                                <li class="arquivo pdf">Documento 2</li>
                                <h4 class="nome-pasta">Imagens</h4>
                                <ul class="pasta" data-aberto=0>
                                    <li class="arquivo imagem">Imagem 1</li>
                                    <li class="arquivo outro">Documento 1</li>
                                    <li class="arquivo pdf">Documento 2</li>
                                    <h4 class="nome-pasta">Imagens</h4>
                                    <ul class="pasta" data-aberto=0>
                                        <li class="arquivo imagem">Imagem 1</li>
                                    </ul>
                                    <li class="arquivo outro">Documento 3</li>
                                </ul>
                                <li class="arquivo outro">Documento 3</li>
                            </ul>
                        </div>
                        
                        <div id="enviar-arquivos" style="display: none">
                            <form>
                                <label>
                                    <img src="../img/upload.png">

                                    <p>Clique ou Arraste o arquivo a ser enviado a contabilidade!</p>
                                </label>
                                <input type="file" name="arquivo" style="display: none">
                                <button>Enviar Arquivo</button>
                            </form>
                        </div>
                    </div>
                </section>
            
                <section id="baixar">
                    <div id="clique">
                        <img src="../img/click.png">
                        <h3>Escolha um arquivo para poder baixa-lo.</h3>
                    </div>
                    
                    <div id="download">
<!--                        <div id="tipo"></div>-->
                        <div>
                            <h3>TITULO do Arquivo</h3>
                        	<div>
                        		<a href="#" id="down" download class="botao margem">Baixar</a>
                        		<a href="#" id="ver" target="_BLANK" class="botao">Ver</a>
                        	</div>
                        </div>
                    </div>
                </section>
            </div>
        </div>
    </body>
    
    <script type="text/javascript">
        var arquivos = <? echo json_encode($lista); ?>;
    </script>
    <script src="../js/empresa/arquivos.js"></script>
</html>