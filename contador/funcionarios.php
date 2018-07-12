<?if (!isset($_SESSION)) session_start();
require "../php/conexao.php";
require(dirname(__DIR__)."/php/sessao.php");
verificarSeSessaoExpirou();
$titulo = "Funcionarios"; 
?>
<!DOCTYPE HTML>
<html>
    <head>
        <title>KT Prime - Funcionarios</title>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <link rel="stylesheet" href="../css/contador/funcionarios.css" media="(min-width: 1000px)">
        <link rel="stylesheet" href="../cssmobile/contador/funcionarios.css" media="(max-width: 999px)">
        <link rel="stylesheet" href="../css/geral.css" media="(min-width: 1000px)">
        <link rel="stylesheet" href="../cssmobile/geral.css" media="(max-width: 999px)">
        <link href="../img/favicon.png" rel="shortcut icon" type="image/x-icon" />
    </head>
    <body>
        <?
        include("menus.php");
        $funcionarios = DBselect("funcionario");
        ?>
        <div id="container">
            <div>
                <section id="funcionarios">
                    <h3>Funcionarios</h3>
                    <div id="pesquisar">
                        <input type="text" placeholder="Pesquisar">
                        <img src="../img/lupa.png">
                    </div>
                    <div id="func">
                        <table class="tabela-spacer">
							<tr>
								<th>Nome</th>
								<th>Função</th>
								<th>Email</th>
								<th>Senha</th>
								<th>Ações</th>
							</tr>
                        </table>
                    </div>
                    <div id="adicionar">
                        <label>Novo Funcionario<img src="../img/add.png"></label>
                        <div class="clear"></div>
                    </div>
                </section>
                <section id="novo-funcionario">
                    <div>
                        <h3>Novo Funcionario</h3>
                        <img src="../img/fechar.png">
                        <form>
                            <div class="input">
                                <label>Nome</label>
                                <input type="text" name="nome" placeholder="Nome" required>
                            </div>
                            <div class="input">
                                <label>Função</label>
                                <input type="text" name="funcao" placeholder="Função" required>
                            </div>
                            <div class="input">
                                <label>Email</label>
                                <input type="email" name="email" placeholder="Email" required>
                            </div>
                            <div class="input metade">
                                <label>Senha</label>
                                <input type="text" name="senha" placeholder="senha" required>
                            </div>
                            <div class="input metade">
                                <label>Acesso Especial</label>
                                <select name="master">
                                    <option value="0">Não</option>
                                    <option value="1">Sim</option>
                                </select>
                            </div>
                            <button type="button" id="editar" class="botao amarelo">Editar</button>
                            <button id="criar" class="botao">Salvar</button>
                        </form>
                    </div>
                </section>
            </div>
        </div>
    </body>
    <script type="text/javascript">
        var funcionarios = <? echo json_encode($funcionarios); ?>;
    </script>
    <script src="../js/contador/funcionarios.js?<? echo time(); ?>"></script>
</html>