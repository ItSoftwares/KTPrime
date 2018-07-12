<?if (!isset($_SESSION)) session_start();

require "../../php/conexao.php";
require "../../php/listarArquivos.php";
// require(dirname(__DIR__)."/php/sessao.php");
//
// verificarSeSessaoExpirou();
$titulo = "Arquivos Empresa"; 
$menu=1;

?>
<!DOCTYPE HTML>
<html>
    <head>
        <title>KT Prime - Arquivos</title>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <link rel="stylesheet" href="../css/empresa/arquivos.css?<? echo time() ?>" media="(min-width: 1000px)">
        <link rel="stylesheet" href="../cssmobile/empresa/arquivos.css?<? echo time() ?>" media="(max-width: 999px)">
        <link rel="stylesheet" href="../css/geral.css" media="(min-width: 1000px)">
        <link rel="stylesheet" href="../cssmobile/geral.css" media="(max-width: 999px)">
        <link href="../img/favicon.png" rel="shortcut icon" type="image/x-icon" />
    </head>
    
    <body>
        <?
        include("../menus.php");

        $lista = listar(realpath(dirname(__DIR__).DIRECTORY_SEPARATOR."..".DIRECTORY_SEPARATOR."servidor".DIRECTORY_SEPARATOR."empresa".DIRECTORY_SEPARATOR.$empresa->id.DIRECTORY_SEPARATOR));
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
                        </div>
                        
                        <div id="enviar-arquivos">
                            <form>
                                <label for="input-upload">
                                    <img src="../img/upload.png">

                                    <p>Clique ou Arraste o arquivo a ser enviado a contabilidade!</p>
                                    <span id="nome"></span>
                                </label>
                                <input type="file" name="arquivo[]" id="input-upload" style="display: none" multiple>
                                <button>Enviar Arquivo</button>
                            </form>
                        </div>
                    </div>
                </section>
            
                <section id="baixar">
                    <div id="controle">
                        <h3>Gerenciamento</h3>
                        <p>Escolha um arquivo ou pasta para gerenciar</p>
                        <div>
                            <button id="nova" class="botao">Criar Pasta</button>
                            <button id="excluir" disabled title="Selecione uma pasta ou um arquivo para EXCLUIR" class="botao vermelho"><img src="../img/lixeira-branca.png"></button>
                            <button id="renomear" disabled title="Selecione uma pasta ou um arquivo para RENOMEAR" class="botao amarelo"><img src="../img/editar-branco.png"></button>
                            <button id="mover" disabled title="Selecione uma pasta ou um arquivo para MOVER" class="botao"><img src="../img/move.png"></button>
                            <button id="cancelar" class="botao vermelho margem" style="display: none;">Cancelar</button>
                            <button id="conf-mover" class="botao" style="display: none;">Mover</button>
<!--                            <button id="bloquear" disabled title="Selecione uma pasta ou arquivo para BLOQUEAR/DESBLOQUEAR"><img src="../img/cadeado.png"></button>-->
                        </div>
                    </div>
                   
                    <div id="clique">
                        <img src="../img/click.png">
                        <h3>Escolha um arquivo ou pasta.</h3>
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
                <section id="campo-texto">
                  <img src="../img/fechar.png">
                   <div>
                       <h3>TITULO</h3>
                       <form>
                            <div class="input">
                                <input type="text" name="nome" pattern='^[ 0-9a-zA-Z\b]+$' placeholder="Nome">
<!--                                <input type="text" name="nome" placeholder="Nome">-->
                            </div>
                            <button>OK</button>
                        </form>
                   </div>
                </section>
            </div>
        </div>
        
    </body>
    
    <script type="text/javascript">
        var arquivos = <? echo json_encode($lista); ?>;
        var empresa = <? echo json_encode($empresa->toArray()); ?>;
    </script>
    <script src="../js/contador/empresa/arquivos.js?<? echo time(); ?>"></script>
</html>