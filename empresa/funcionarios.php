<?if (!isset($_SESSION)) session_start();



require "../php/conexao.php";

//require(dirname(__DIR__)."/php/sessao.php");

//

//verificarSeSessaoExpirou();

$titulo = "Funcionarios";



$menu=1;

?>

<!DOCTYPE HTML>

<html>

    <head>

        <title>KT Prime - Funcionários</title>

        <meta charset="utf-8">

        <meta name="viewport" content="width=device-width, initial-scale=1">

        <link rel="stylesheet" href="../css/empresa/funcionarios.css" media="(min-width: 1000px)">

        <link rel="stylesheet" href="../cssmobile/empresa/funcionarios.css" media="(max-width: 999px)">

        <link rel="stylesheet" href="../css/geral.css" media="(min-width: 1000px)">

        <link rel="stylesheet" href="../cssmobile/geral.css" media="(max-width: 999px)">

        <link href="../img/favicon.png" rel="shortcut icon" type="image/x-icon" />

    </head>

    

    <body>

        <?

        include("menus.php");

        

        $funcionarios = DBselect("funcionario_empresa", "where id_empresa={$usuario->id} order by id DESC");

        

        if (count($funcionarios)==0) $funcionarios=[];

        ?>

        

        <div id="container">

            <div>

                <section id="funcionarios">

                    <h3>Funcionario</h3>

                    

                    <div>

                        <table>

                            <tr>

                                <th>Ativo</th>

                                <th>Nome</th>

                                <th>Função</th>

                                <th>Ações</th>

                            </tr>

                            <tr>

                                <td><img src="../img/exclamacao.png" class="pendente"></td>

                                <td>Fulano da Silva Moura</td>

                                <td>Secretário</td>

                                <td></td>

                            </tr>

                        </table>

                    </div>

                </section>

            </div>

            <section id="novo-funcionario">



                <div>

                    <h3>Novo Funcionário</h3>

                    <img src="../img/fechar.png">



                    <form>

                        <div>

                            <div class="input">

                                <label>Nome</label>

                                <input type="text" name="nome" placeholder="Nome do Funcionário" required>

                            </div>

                            <div class="input metade">

                                <label>Funcao</label>

                                <input type="text" name="funcao" placeholder="Função" required>

                            </div>

                            <div class="input metade">

                                <label>Ativo</label>

                                <select name="ativo">

                                    <option value="1">Sim</option>

                                    <option value="0">Não</option>

                                </select>

                            </div>



                            <div class="input metade">

                                <label>Data de Admissão</label>

                                <input type="text" name="data_admissao" placeholder="Data de Admissão">

                            </div>



                            <div class="input metade">

                                <label>Guia Sindical</label>

                                <input type="text" name="guia_sindical" placeholder="Guia Sindical">

                            </div>



                            <div class="input metade">

                                <label>CBO</label>

                                <input type="text" name="cbo" placeholder="CBO">

                            </div>

                            

                            <div class="input metade">

                                <label>DAS</label>

                                <input type="text" name="das" placeholder="DAS">

                            </div>

                            

                            <div class="input metade">

                                <label>PIS</label>

                                <input type="text" name="pis" placeholder="PIS">

                            </div>



                            <div class="input metade">

                                <label>Valor do ultimo Salário</label>

                                <input type="text" name="valor_ultimo_salario" placeholder="Valor do Ultimo Salário" required>

                            </div>

                            

                            <div class="input metade">

                                <label>Aviso</label>

                                <input type="text" name="aviso" placeholder="Aviso">

                            </div>

                            

                            <div class="input metade">

                                <label>Demissão</label>

                                <input type="text" name="demissao" placeholder="Demissão">

                            </div>

                            

                            <div class="input metade">

                                <label>Ferias</label>

                                <input type="text" name="ferias" placeholder="Ferias">

                            </div>

                            

                            <div class="input metade">

                                <label>Horario</label>

                                <input type="text" name="horario" placeholder="horario">

                            </div>



                            <div class="input">

                                <label>Imposto</label>

                                <textarea name="observacoes" placeholder="Observações"></textarea>

                            </div>

                        </div>



<!--

                        <button type="button" id="editar">Editar</button>

                        <button id="criar">Salvar</button>

-->

                    </form>

                </div>

            </section>

        </div>

    </body>

    <script type="text/javascript">

        var funcionarios = <? echo json_encode($funcionarios); ?>;

        var funcionario = true;

    </script>

    <script src="../js/jquery.mask.js"></script>

    <script src="../js/contador/empresa/funcionario.js"></script>

</html>