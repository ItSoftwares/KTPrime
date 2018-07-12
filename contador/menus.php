<?
require(dirname(__DIR__)."/php/classes/contador.class.php");
require(dirname(__DIR__)."/php/classes/empresa.class.php");
$usuario = unserialize($_SESSION['contador']);
$menu = isset($menu)?$menu:0;
if (isset($_GET['id'])) {
    if (!isset($_SESSION['empresa'])) {
        $result = DBselect("empresa", "where id={$_GET['id']}");
        $empresa = new Empresa(); 
        $empresa->fromArray($result[0]);
        $_SESSION['empresa'] = serialize($empresa);
    } else {
        $empresa = unserialize($_SESSION['empresa']);
        if ($empresa->id!=$_GET['id']) {
            $result = DBselect("empresa", "where id={$_GET['id']}");
            $empresa = new Empresa(); 
            $empresa->fromArray($result[0]);
            $_SESSION['empresa'] = serialize($empresa);
        }
    }
} else {
    unset($_SESSION['empresa']);
}
?>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="../css/menu.css" media="(min-width: 1000px)">
    <link rel="stylesheet" href="../cssmobile/menu.css" media="(max-width: 999px)">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
</head>
<aside id="menu">
    <header>
        <a href="../contador/resumo">
        	<img src="../img/logo.png">
	        <h2>KT Prime<span>Contabilidade</span></h2>
        </a>
        <div id="botao-menu"><img src="../img/menu.png" alt="Botão Menu"></div>
    </header>
    <nav>
        <h3 data-aberto="<? echo $menu; ?>"><span></span>Principal</h3>
        <ul>
           <?
            $selecionado = "class='selecionado'";
            ?>
            <li <? if ($titulo=="Resumo") echo $selecionado; ?>>
               <a href="../contador/resumo">
                    <img src="../img/menu.png">
                    <span>Resumo</span>
                </a>
            </li>
            <li <? if ($titulo=="Servicos") echo $selecionado; ?>>
               <a href="../contabilidade/servicos">
                    <img src="../img/servicos.png">
                    <span>Serviços</span>
                </a>
            </li>
            <li <? if ($titulo=="Financeiro") echo $selecionado; ?>>
                <a href="../contador/financeiro">
                    <img src="../img/dinheiro.png">
                    <span>Financeiro</span>
                </a>
            </li>
            <li <? if ($titulo=="Empresas") echo $selecionado; ?>>
                <a href="../contabilidade/empresas">
                    <img src="../img/mala.png">
                    <span>Clientes</span>
                </a>
            </li>
            <li <? if ($titulo=="Solicitacoes") echo $selecionado; ?>>
                <a href="../contador/solicitacoes">
                    <img src="../img/exclamacao.png">
                    <span>Solicitações</span>
                </a>
            </li>
            <li <? if ($titulo=="Certificados") echo $selecionado; ?>>
                <a href="../contador/certificados">
                    <img src="../img/certificado.png">
                    <span>Cert. Digitais</span>
                </a>
            </li>
            <li <? if ($titulo=="Alvarás") echo $selecionado; ?>>
                <a href="../contador/alvaras">
                    <img src="../img/alvara.png">
                    <span>Alvarás</span>
                </a>
            </li>
            <li <? if ($titulo=="Funcionarios") echo $selecionado; ?>>
                <a href="../contador/funcionarios">
                    <img src="../img/usuarios.png">
                    <span>Funcionários</span>
                </a>
            </li>
            <li <? if ($titulo=="Contabilidade Mensal") echo $selecionado; ?>>
                <a href="../contabilidade/mensal">
                    <img src="../img/guia.png">
                    <span>Cont. Mensal</span>
                </a>
            </li>
        </ul>
        <?
        if (isset($_GET['id'])) {
            $selecionado = "class='selecionado'";
        ?>
        <h3 data-aberto="<? echo $menu==0?1:0; ?>"><span></span><? echo $empresa->razao_social; ?></h3>
        <ul>
            <li <? if ($titulo=="Resumo Empresa") echo $selecionado; ?>>
               <a href="../empresaResumo/<? echo $empresa->id ?>">
                    <img src="../img/menu.png">
                    <span>Resumo</span>
                </a>
            </li>
            <li <? if ($titulo=="Serviços Prestados Empresa") echo $selecionado; ?>>
               <a href="../empresaServicosPrestados/<? echo $empresa->id ?>">
                    <img src="../img/servico-prestado.png">
                    <span>Serv. Prestados</span>
                </a>
            </li>
            <li <? if ($titulo=="Obrigações Empresa") echo $selecionado; ?>>
               <a href="../empresaObrigacoes/<? echo $empresa->id ?>">
                    <img src="../img/obrigacao.png">
                    <span>Obrigações</span>
                </a>
            </li>
            <li <? if ($titulo=="Faturamento Empresa") echo $selecionado; ?>>
               <a href="../empresaFaturamento/<? echo $empresa->id ?>">
                    <img src="../img/dinheiro.png">
                    <span>Faturamento</span>
                </a>
            </li>
            <li <? if ($titulo=="Arquivos Empresa") echo $selecionado; ?>>
                <a href="../empresaArquivos/<? echo $empresa->id ?>">
                    <img src="../img/arquivo.png">
                    <span>Arquivos</span>
                </a>
            </li>
            <li <? if ($titulo=="Arquivos Ocultos") echo $selecionado; ?>>
                <a href="../empresaOculto/<? echo $empresa->id ?>">
                    <img src="../img/ver.png">
                    <span>Arq. Ocultos</span>
                </a>
            </li>
            <li <? if ($titulo=="Guias Empresa") echo $selecionado; ?>>
                <a href="../empresaGuias/<? echo $empresa->id ?>">
                    <img src="../img/guia.png">
                    <span>Guias</span>
                </a>
            </li>
            <li <? if ($titulo=="Solicitações Empresa") echo $selecionado; ?>>
                <a href="../empresaSolicitacoes/<? echo $empresa->id ?>">
                    <img src="../img/exclamacao.png">
                    <span>Solicitações</span>
                </a>
            </li>
            <li <? if ($titulo=="Funcionarios Empresa") echo $selecionado; ?>>
                <a href="../empresaFuncionarios/<? echo $empresa->id ?>">
                    <img src="../img/usuarios.png">
                    <span>Funcionários</span>
                </a>
            </li>
        </ul>
        <?
        }
        ?>
    </nav>
    <footer>
        <img src="../servidor/contador/padrao.png">
        <span><? echo substr($usuario->nome, 0, 15)."."; ?></span>
        <img src="../img/engrenagem.png">
    </footer>
    <div id="configuracoes">
        <ul>
            <li>Configurações</li>
            <li id="sair">Sair</li>
        </ul>
    </div>
</aside>
<section id="tela-configuracoes">
    <div>
        <h3>Configurações</h3>
        <img src="../img/fechar.png">
        <form>
            <div class="input">
                <label>Nome</label>
                <input type="text" name="nome" placeholder="Nome" value="<? echo $usuario->nome ?>">
            </div>
            <div class="input linha">
                <label>Email</label>
                <input type="text" name="email" placeholder="Email"  value="<? echo $usuario->email ?>">
            </div>
            <div class="input metade">
                <label>Nova Senha</label>
                <input type="password" name="senha" placeholder="Senha">
            </div>
            <div class="input metade">
                <label>Repita Nova Senha</label>
                <input type="password" name="repetir_senha" placeholder="Repita a Senha">
            </div>
            <button class="botao">Atualizar</button>
        </form>
    </div>
</section>
<div id="barra-superior">
    <h2><? echo $titulo; ?></h2>
    <div id="icone-notificacoes">
        <img src="../img/notificacao.png">
        <span>5</span>
    </div>
</div>
<?
include(dirname(__DIR__)."/html/popup.html");
?>
<script type="text/javascript">
    var usuario = <? echo json_encode($usuario->toArray()); ?>;
</script>
<script src="../js/menu.js"></script>