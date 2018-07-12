<?
if (!isset($_SESSION)) session_start();

require "../php/conexao.php";
require(dirname(__DIR__)."/php/sessao.php");

verificarSeSessaoExpirou();

$qtd_empresas = DBselect("empresa", "", "count(id) as quantidade");
$empresas = DBselect("empresa", "", "id, razao_social");
$qtd_solic = DBselect("solicitacao", "where respondido=0", "count(id) as quantidade");
$qtd_lembrete = DBselect("lembrete", "where realizado=0", "count(id) as quantidade");
$titulo = "Resumo";
?>
<!DOCTYPE HTML>
<html>
    <head>
        <title>KT Prime - Resumo</title>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <link rel="stylesheet" href="../css/contador/resumo.css" media="(min-width: 1000px)">
        <link rel="stylesheet" href="../cssmobile/contador/resumo.css" media="(max-width: 999px)">
        <link rel="stylesheet" href="../css/geral.css" media="(min-width: 1000px)">
        <link rel="stylesheet" href="../cssmobile/geral.css" media="(max-width: 999px)">
        <link href="../img/favicon.png" rel="shortcut icon" type="image/x-icon" />
    </head>
    
    <body>
        <?
        include("menus.php");
        ?>
        
        <div id="container">
            <div>
                <section id="acesso-rapido">
                    <ul>
                        <li onclick="location.href='empresas'">
                            <div data-cor="blue"><img src="../img/mala.png"></div>
                            <div>
                                <h4><? echo $qtd_empresas[0]['quantidade']; ?> Empresas</h4>
                                <p>Total</p>
                            </div>
                        </li>

                        <li onclick="location.href='solicitacoes'">
                            <div><img src="../img/exclamacao.png"></div>
                            <div>
                                <h4><? echo $qtd_solic[0]['quantidade']; ?> Solicitações</h4>
                                <p>Pendentes</p>
                            </div>
                        </li>

                        <li>
                            <div><img src="../img/obrigacao.png"></div>
                            <div>
                                <h4><? echo $qtd_lembrete[0]['quantidade']; ?> Lembretes</h4>
                                <p>Pendentes</p>
                            </div>
                        </li>
                    </ul>
                </section>

                <section id="lembretes">
                    <div id="escolher">
                        <h3>Lembretes</h3>
                        <p>Escolha no calendario abaixo o dia que deseja ver os lembretes pessoais e das empresas</p>

                        <div id="calendario">
                            <header>
                                <div id="mes">Agosto</div>
                                <div id="ano">2017</div>
                                <img src="../img/seta-dupla.png" id="voltar-mes">
                                <img src="../img/seta-dupla.png" id="avancar-mes">
                            </header>
                            <div id="nome-semana">
                                <span>Dom</span>
                                <span>Seg</span>
                                <span>Ter</span>
                                <span>Qua</span>
                                <span>Qui</span>
                                <span>Sex</span>
                                <span>Sab</span>
                            </div>
                            <article>
                                <!-- gerar dias -->
                                <? for ($i=0; $i<6; $i++) { ?>
                                <div class="semana">
                                    <span>0</span>
                                    <span>0</span>
                                    <span>0</span>
                                    <span>0</span>
                                    <span>0</span>
                                    <span>0</span>
                                    <span>0</span>
                                </div>
                                <? } ?>
                            </article>
                        </div>
                    </div>
                    <div id="listar">
                        <header>
                            <h4><? echo date("d/m/Y", time()) ?></h4>
                            <label>Novo Lembrete<img src="../img/add.png"></label>
                        </header>
                        <nav>
                            <label data-id=0 class="selecionado">PESSOAL<span>12</span></label>
                            <label data-id=1>EMPRESAS<span>3</span></label>
                        </nav>

                        <article>
                            <ul>
                                
                            </ul>
                        </article>
                        
                        <footer>
                            <p>Mostrar todos os lembretes do mês</p>
                        </footer>
                    </div>
                </section>
            </div>
        </div>
        
        <section id="novo-lembrete">
            <div>
                <h3>Novo Lembrete</h3>
                <img src="../img/fechar.png">
                
                <form>
                    <div class="input">
                        <label>Titulo</label>
                        <input type="text" name="titulo" required placeholder="Titulo do Lembrete">
                    </div>
                    
                    <div class="input metade">
                        <label>Tipo</label>
                        <select name="tipo">
                            <option value="0">Normal</option>
                            <option value="1">Alvará</option>
                        </select>
                    </div>
                    
                    <div class="input metade">
                        <label>Empresa</label>
                        <select name="id_empresa">
                            <option value="0">Nenhuma</option>
                            <?
                            if (isset($empresas)) {
                                foreach($empresas as $emp) {
                            ?>
                            <option value="<? echo $emp['id']; ?>"><? echo $emp['razao_social']; ?></option>
                            <?
                                }
                            }
                            ?>
                        </select>
                    </div>
                    
                    <div class="input metade">
                        <label>Data</label>
                        <input type="text" name="data_inicio" placeholder="00/00/0000">
                    </div>
                    
                    <div class="input metade">
                        <label>Data Validade</label>
                        <input type="text" name="data_validade" placeholder="00/00/0000">
                    </div>
                    
                    <button class="botao">Criar Lembrete</button>
                </form>
            </div>
        </section>
    </body>
    <script type="text/javascript">
        var dataGeral=<? echo time() ?>;
        var lembretes=<? echo json_encode($_SESSION['lembretes']); ?>;
        var sessao=<? echo json_encode($_SESSION); ?>;
    </script>
    <script src="../js/jquery.mask.js"></script>
    <script src="../js/contador/resumo.js"></script>
</html>