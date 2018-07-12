<?
if (!isset($_SESSION)) session_start();
require(dirname(__DIR__)."/php/sessao.php");
require "../php/listarArquivos.php";

verificarSeSessaoExpirou();
$titulo = "Inicio"; 

?>
<!DOCTYPE HTML>
<html>
    <head>
        <title>KT Prime - Inicio</title> 
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <link rel="stylesheet" href="../css/empresa/resumo.css" media="(min-width: 1000px)">
        <link rel="stylesheet" href="../cssmobile/empresa/resumo.css" media="(max-width: 999px)">
        <link rel="stylesheet" href="../css/geral.css" media="(min-width: 1000px)">
        <link rel="stylesheet" href="../cssmobile/geral.css" media="(max-width: 999px)">
        <link href="../img/favicon.png" rel="shortcut icon" type="image/x-icon" />
    </head> 
    
    <body>
        <?
        include("menus.php");

        $lista = listar(realpath(dirname(__DIR__).DIRECTORY_SEPARATOR."servidor".DIRECTORY_SEPARATOR."inicio".DIRECTORY_SEPARATOR));

        $lista = utf8ize($lista);
        ?>
        
        <div id="container">
            <div>
                <section class="inicio">
                    
                    <div id="bemvindo">
                        <h1>Bem-vindo <? echo $usuario->razao_social; ?></h1>

                        <img src="../img/logo.png">
                        <h2>Contabilidade</h2>

                        <p>Caro cliente este é o ambiente virtual da sua contabilidade. Aqui você tem acesso fácil e rápido aos documentos empresariais quando quiser!</p>
                    </div>

                    <div id="arquivos">
                        <h1>Arquivos Importantes</h1>

                        <div id="arvore">
                            
                        </div>

                        <div id="botoes">
                            <a class="botao disabled" id="baixar" download="">Baixar</a>
                            <a class="botao disabled" id="ver" target="_BLANK">Ver</a>
                        </div>
                    </div>

                </section>
            
            </div>
        </div>
    </body>
    <script type="text/javascript">
        var arquivos = <? echo json_encode($lista); ?>;
        var inicio = true;
    </script>
    <script src="../js/jquery.mask.js"></script>
    <script src="../js/empresa/resumo.js"></script>
</html>