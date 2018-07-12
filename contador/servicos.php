<?if (!isset($_SESSION)) session_start();
$titulo = "Servicos"; 
require "../php/conexao.php";
require(dirname(__DIR__)."/php/sessao.php");

$servicos = DBselect("servico");

verificarSeSessaoExpirou();

?>
<!DOCTYPE HTML>
<html>
	<head>
		<title>KT Prime - Serviços</title>
		<meta charset="utf-8">
		<link rel="stylesheet" href="../css/contador/servicos.css" media="(min-width: 1000px)">
		<link rel="stylesheet" href="../cssmobile/contador/servicos.css" media="(max-width: 999px)">
		<link rel="stylesheet" href="../css/geral.css" media="(min-width: 1000px)">
		<link rel="stylesheet" href="../cssmobile/geral.css" media="(max-width: 999px)">
		<link href="../img/favicon.png" rel="shortcut icon" type="image/x-icon" />
	</head>
	<body>
		<?
		if ($_SESSION['usuario_logado']==1) include("menus.php");
		else if ($_SESSION['usuario_logado']==3) include("../funcionario/menus.php");
		
		?>
		<div id="container">
			<div>
				<div id="adicionar">
					<h3>Novo Serviço</h3>
					<form>
						<div class="input">
							<label>Serviço</label>
							<input type="text" name="nome" placeholder="Nome do Serviço" required>
						</div>
						<div class="input">
							<label>Valor</label>
							<input type="number" name="valor" placeholder="Valor do Serviço" required min="0" step="any">
						</div>
						<button class="botao">Novo</button>
						<div class="clear"></div>
					</form>
				</div>
				<section id="servicos">
					<h3>Lista de Serviços</h3>
					<div>
						<table class="tabela-spacer">
							<tr>
								<th data-index="nome" class="selecionado">Serviço</th>
								<th data-index="valor">Valor</th>
								<th class="center">Ação</th>
							</tr>
						</table>
					</div>
				</section>
			</div>
		</div>
	</body>
	<script>
		var servicos = <? echo json_encode($servicos); ?>;
		
	</script>
	<script src="../js/jquery.mask.js"></script>
	<script src="../js/contador/servicos.js"></script>
</html>