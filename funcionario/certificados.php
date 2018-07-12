<?if (!isset($_SESSION)) session_start();

require "../php/conexao.php";
require(dirname(__DIR__)."/php/sessao.php");

verificarSeSessaoExpirou();
$titulo = "Certificados"; 
?>
<!DOCTYPE HTML>
<html>
    <head>
        <title>KT Prime - Cert. Digitais</title>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <link rel="stylesheet" href="../css/contador/certificados.css" media="(min-width: 1000px)">
        <link rel="stylesheet" href="../cssmobile/contador/certificados.css" media="(max-width: 999px)">
        <link rel="stylesheet" href="../css/geral.css" media="(min-width: 1000px)">
        <link rel="stylesheet" href="../cssmobile/geral.css" media="(max-width: 999px)">
        <link href="../img/favicon.png" rel="shortcut icon" type="image/x-icon" />
    </head>
    
    <body>
        <?
        include("menus.php");
        
        $certificados = DBselect("empresa", "where tipo_cd <> '0' order by id DESC", "razao_social, id, tipo_cd, validade_cd, email");
        ?>
        <div id="container">
            <div>
            
                <section id="certificados">
                   <h3>Lista de Certificados Digitais</h3>
                   
                   <div id="pesquisar">
                        <input type="text" placeholder="Pesquisar">
                        <img src="../img/lupa.png">
                    </div>
                   
                    <div>
                        <table class="tabela-spacer">
                            <tr>
                                <th>Empresa</th>
                                <th>Tipo</th>
                                <th>Validade</th>
                                <th></th>
                            </tr>

                            <tr class="spacer"><td></td></tr>

                            <tr>
                                <td>Nome da Empresa</td>
                                <td>Tipo de certificado digital</td>
                                <td>17/09/2017, 15:29</td>
                                <td><img src="../img/exclamacao.png"></td>
                            </tr>
                            <tr class="spacer"><td></td></tr>
                            <tr>
                                <td>Nome da Empresa</td>
                                <td>Tipo de certificado digital</td>
                                <td>17/09/2017, 15:29</td>
                                <td></td>
                            </tr>
                            
                        </table>
                    </div>
                </section>
            
            </div>
        </div>
    </body>
    
    <script type="text/javascript">
        var certificados = <? echo json_encode($certificados); ?>;
        
    </script>
    <script type="text/javascript" src='../js/contador/certificado.js'></script>
</html>