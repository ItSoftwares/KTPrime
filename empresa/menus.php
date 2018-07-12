<?
require("../php/classes/empresa.class.php");

$usuario = unserialize($_SESSION['empresa']);
?>
<head>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="../css/menu.css" media="(min-width: 1000px)">
    <link rel="stylesheet" href="../cssmobile/menu.css" media="(max-width: 999px)">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
    <link href="../img/favicon.png" rel="shortcut icon" type="image/x-icon" />
<!--    <meta http-equiv="refresh" content="4">-->
</head>

<aside id="menu">
    <header>
        <a href="inicio">
        	<img src="../img/logo.png">
	        <h2>KT Prime<span>Contabilidade</span></h2>
        </a>
        <div id="botao-menu"><img src="../img/menu.png" alt="Botão Menu"></div>
    </header>
    
    <nav>
        <h3 data-aberto="0"><span></span>Principal</h3>
        <ul>
           <?
            $selecionado = "class='selecionado'";
            ?>
            <li <? if ($titulo=="Inicio") echo $selecionado; ?>>
               <a href="inicio">
                    <img src="../img/menu.png">
                    <span>Inicio</span>
                </a>
            </li>
            <li <? if ($titulo=="Financeiro") echo $selecionado; ?>>
                <a href="financeiro">
                    <img src="../img/dinheiro.png">
                    <span>Financeiro</span>
                </a>
            </li>
            <li <? if ($titulo=="Serviços Prestados") echo $selecionado; ?>>
                <a href="servicosPrestados">
                    <img src="../img/servico-prestado.png">
                    <span>Serv. Prestados</span>
                </a>
            </li>
<!--
            <li <? if ($titulo=="Obrigações") echo $selecionado; ?>>
                <a href="obrigacoes">
                    <img src="../img/obrigacao.png">
                    <span>Obrigações</span>
                </a>
            </li> 
-->
            <li <? if ($titulo=="Solicitações") echo $selecionado; ?>>
               <a href="solicitacoes">
                    <img src="../img/exclamacao.png">
                    <span>Solicitações</span>
                </a>
            </li>

            <li <? if ($titulo=="Faturamento") echo $selecionado; ?>>
               <a href="faturamento">
                    <img src="../img/dinheiro.png">
                    <span>Faturamento</span>
                </a>
            </li>
            <li <? if ($titulo=="Arquivos") echo $selecionado; ?>>
                <a href="arquivos">
                    <img src="../img/arquivo.png">
                    <span>Arquivos</span>
                </a>
            </li>
            <li <? if ($titulo=="Funcionarios") echo $selecionado; ?>>
                <a href="funcionarios">
                    <img src="../img/usuarios.png">
                    <span>Funcionarios</span>
                </a>
            </li>
            <li <? if ($titulo=="Guias") echo $selecionado; ?>>
                <a href="guias">
                    <img src="../img/guia.png">
                    <span>Guias</span>
                </a>
            </li>
            <li>
                <a href="<? echo $usuario->link_nf_venda; ?>" target="_blank">
                    <img src="../img/venda.png">
                    <span>Emitir NF Venda</span>
                </a>
            </li>
            <li>
                <a href="<? echo $usuario->link_nf_servico; ?>" target="_blank">
                    <img src="../img/servicos.png">
                    <span>Emitir NF Serviço</span>
                </a>
            </li>
            
        </ul>
    </nav>
    <footer>
        <img src="../servidor/contador/padrao.png">
        <span><? echo substr($usuario->nome_cliente,0,15); ?></span>
        <img src="../img/engrenagem.png">
    </footer>
    <div id="configuracoes">
        <ul>
<!--            <li>Configurações</li>-->
            <li id="sair">Sair</li>
        </ul>
    </div>
</aside>

<div id="barra-superior">
    <h2><? echo $usuario->razao_social; ?></h2>
    <div id="menu-acesso">
        <ul>
<!--
            <li><a href="resumo"><img src="../img/menu.png"></a><span>Resumo</span></li>
            <li><a href="servicosPrestados"><img src="../img/servico-prestado.png"></a><span>Serviços Prestados</span></li>
            <li><a href="obrigacoes"><img src="../img/obrigacao.png"></a><span>Obrigações</span></li>
            <li><a href="solicitacoes"><img src="../img/exclamacao.png"></a><span>Solicitações</span></li>
            <li><a href="financeiro"><img src="../img/dinheiro.png"></a><span>Financeiro</span></li>
            <li><a href="arquivos"><img src="../img/arquivo.png"></a><span>Arquivos</span></li>
            <li><a href="guias"><img src="../img/guia.png"></a><span>Guias</span></li>
            <li><a href="<? echo $usuario->link_nf_venda; ?>"><img src="../img/venda.png"></a><span>Emitir NF Venda</span></li>
            <li><a href="<? echo $usuario->link_nf_servico; ?>"><img src="../img/servicos.png"></a><span>Emitir NF Serviço</span></li>
-->
        </ul>
   </div>
</div>

<?
include("../html/popup.html");
?>

<script type="text/javascript">
    var usuario = <? echo json_encode($usuario->toArray()); ?>;
</script>
<script src="../js/menu.js"></script>