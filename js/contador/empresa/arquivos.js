var selecionado;
var tipo = "pasta";
var funcao = "criar_pasta";
var valido = false;
var path = '';
var mover = '';
var mostrarArquivos = true;

$(document).ready(function() {
	$("#arvore").children().remove();
	atualizarArvore(arquivos['nomes'], $("#arvore"), arquivos['caminhos'], arquivos['informacoes']);
	selecionado = $("#arvore > *:nth-child(1)");
	if (typeof guias != "undefined") path = "/guias";
	if (typeof privado != "undefined") path = "/privado";
});

function atualizarArvore(lista, elemento, caminho, informacoes) {
	if ($("#arvore h4").length == 0) {
		if (typeof guias != "undefined") $("<h4 class='nome-pasta selecionado' data-pai=1 data-aberto=1><span>Guias</span></h4>").appendTo(elemento);
		else if (typeof privado != "undefined") $("<h4 class='nome-pasta selecionado' data-pai=1 data-aberto=1><span>Privado</span></h4>").appendTo(elemento);
		else $("<h4 class='nome-pasta selecionado' data-pai=1 data-aberto=1><span>" + empresa.razao_social + "</span></h4>").appendTo(elemento);
		elemento = $("<ul class='pasta' data-aberto=1></ul>").appendTo(elemento);
	}
	$.each(lista, function(i, value) {
		if (typeof value != "object") {
			if (!mostrarArquivos) return true;
			item = $("<li class='arquivo' data-inf='" + JSON.stringify(informacoes[i]) + "'>" + value + "</li>").appendTo(elemento);
			if (informacoes[i].extension == "pdf") {
				item.addClass("pdf");
			} else if (informacoes[i].extension == "jpg" || informacoes[i].extension == "jpeg" || informacoes[i].extension == "png" || informacoes[i].extension == "gif") {
				item.addClass("imagem");
			} else {
				item.addClass("outro");
			}
		} else {
			if (i == "anexos" || i == "guias" || i == "solicitacoes" || i == "privado" || (typeof funcionario != "undefined" && i == "privado_contabilidade")) {
				console.log(typeof funcionario != "undefined" && i == "contabilidade");
				return true;
			}
			$("<h4 class='nome-pasta' data-aberto=0><span>" + i + "</span></h4>").appendTo(elemento);
			temp = $("<ul class='pasta' data-aberto=0></ul>").appendTo(elemento);
			atualizarArvore(value, temp, caminho[i], informacoes[i]);
		}
	});
}

function selecionar(elemento) {
	// if ($(elemento).attr("data-pai")==1) return;
	$("#arvore .selecionado").removeClass("selecionado");
	// console.log("teste");
	$(elemento).addClass("selecionado");
}

function pegarCaminho(elemento, caminho) {
	caminho = caminho || "";
	if (caminho == "") {
		caminho = $(elemento).text();
	} else {
		caminho = $(elemento).text() + "/" + caminho;
	}
	if ($(elemento).parent().parent().attr("id") == "arvore") {
		if (typeof guias != "undefined") return "/guias/" + caminho;
		if (typeof privado != "undefined") return "/privado/" + caminho;
		else return "/" + caminho;
	} else {
		return pegarCaminho($(elemento).parent().prev()[0], caminho);
	}
}

$(document).on("click", "h4.nome-pasta", function() {
	selecionar(this);
	$("#nova").attr("disabled", false);
	$("#renomear").attr("disabled", false);
	$("#bloquear").attr("disabled", false);
	$("#excluir").attr("disabled", false);
	$("#mover").attr("disabled", false);
	selecionado = $(this);
	tipo = "pasta";
	if ($(this).attr("data-pai") == 1) {
		if (typeof guias != "undefined") path = "/guias";
		else if (typeof privado != "undefined") path = "/privado";
		else path = "/";
		$("#renomear").attr("disabled", true);
		$("#excluir").attr("disabled", true);
		$("#bloquear").attr("disabled", true);
		$("#mover").attr("disabled", true);
		return;
	}
	caminho = pegarCaminho(this);
	path = caminho;
	$("#clique").show();
	$("#download").hide();
});

$(document).on("click", "h4.nome-pasta span", function() {
	pasta = $(this).parent().next();
	aberto = pasta.attr("data-aberto");
	aberto = aberto == 0 ? 1 : 0;
	pasta.attr("data-aberto", aberto);
	$(this).parent().attr("data-aberto", aberto)
});

$(document).on("click", "li.arquivo", function() {
	$("#nova").attr("disabled", true);
	selecionar(this);
	inf = JSON.parse($(this).attr("data-inf"));
	classe = "outro";
	if ($(this).hasClass("pdf")) classe = "pdf";
	if ($(this).hasClass("imagem")) classe = "imagem";
	$("#tipo").removeClass().addClass(classe);
	$("#download h3").text(inf.filename);
	link = decodeURIComponent(escape(inf.dirname.replace("/home/cnpjn035/public_html", "") + "/" + inf.basename));
	//    console.log(link)
	$("#download a").attr("href", link);
	$("#clique").hide();
	$("#download").show().css({
		display: "flex"
	});
	selecionado = $(this);
	tipo = "arquivo";
	$("#nova").attr("disabled", true);
	$("#excluir").attr("disabled", false);
	$("#renomear").attr("disabled", false);
	$("#bloquear").attr("disabled", false);
	$("#mover").attr("disabled", false);
	path = pegarCaminho(this);
});

$("#campo-texto input").keyup(function() {
	valido = $(this).is(":valid");
	//    console.log(valido)
});

$("#nova").click(function() {
	$("#campo-texto h3").text("Nova Pasta");
	$("#campo-texto").fadeIn().css({
		display: "flex"
	});
	funcao = "criar_pasta";
});

$("#campo-texto img").click(function() {
	$("#campo-texto").fadeOut();
	funcao = "";
});

$("#campo-texto form").submit(function(e) {
	e.preventDefault();
	if (valido == false) {
		chamarPopupInfo("Escreva um nome sem caracteres especiais!");
		return;
	}
	data = {};
	if (funcao == "criar_pasta") {
		novoNome = $("#campo-texto input").val().toLowerCase();
		if (novoNome == "anexos" || novoNome == "guias" || novoNome == "solicitacoes" || novoNome == "privado" || (path == "/privado" && novoNome == "privado_contabilidade")) {
			chamarPopupInfo("Nome inválido, escolha outro!")
			$("#campo-texto input").focus();
			return;
		}
		data['funcao'] = funcao;
		data['caminho'] = path;
		data['nome'] = $("#campo-texto input").val();
		if (typeof guias != "undefined") data['extra'] = "guias";
		else if (typeof privado != "undefined") data['extra'] = "privado";
		$.ajax({
			url: "../php/contabilidade/gerenciarArquivos.php",
			type: "post",
			data: data,
			success: function(result) {
				result = JSON.parse(result);
				if (result.estado == 1) {
					chamarPopupConf(result.mensagem);
					$("#campo-texto img").click();
					$("#campo-texto form")[0].reset();
					arquivos = result.lista;
					//                $("#arvore").children().remove();
					//                atualizarArvore(arquivos['nomes'], $("#arvore"), arquivos['caminhos'], arquivos['informacoes']);
					temp = "<h4 class='nome-pasta' data-aberto=0><span>" + data['nome'] + "</span></h4><ul class='pasta' data-aberto=0></ul>";
					selecionado.next().append(temp);
					console.log(temp);
				} else if (result.estado == 2) {
					chamarPopupInfo(result.mensagem);
				} else {
					chamarPopupErro(result.mensagem);
				}
			},
			error: function(result) {
				console.log(result);
				chamarPopupErro("Houve um erro, tente atualizar a página!");
			}
		});
	} else if (funcao == "renomear") {
		novoNome = $("#campo-texto input").val().toLowerCase();
		if (novoNome == "anexos" || novoNome == "guias" || novoNome == "solicitacoes" || novoNome == "privado") {
			chamarPopupInfo("Nome inválido, escolha outro!")
			$("#campo-texto input").focus();
			return;
		}
		inf = tipo == "pasta" ? "" : JSON.parse(selecionado.attr("data-inf"));
		data['funcao'] = funcao;
		data['nome'] = tipo == "pasta" ? selecionado.find("span").text() : inf.basename;
		data['caminho'] = path.split(data['nome'])[0];
		data['novo_nome'] = $("#campo-texto input").val();
		data['novo_nome'] = tipo == "pasta" ? data['novo_nome'] : data['novo_nome'] + "." + inf.extension;
		if (typeof guias != "undefined") data['extra'] = "guias";
		else if (typeof privado != "undefined") data['extra'] = "privado";
		$.ajax({
			url: "../php/contabilidade/gerenciarArquivos.php",
			type: "post",
			data: data,
			success: function(result) {
				result = JSON.parse(result);
				if (result.estado == 1) {
					chamarPopupConf(result.mensagem);
					arquivos = result.lista;
					if (tipo == "pasta") {
						selecionado.find("span").text(data['novo_nome']);
					} else {
						selecionado.text(data['novo_nome']);
					}
					$("#campo-texto img").click();
					$("#campo-texto form")[0].reset();
				} else if (result.estado == 2) {
					chamarPopupInfo(result.mensagem);
				} else {
					chamarPopupErro(result.mensagem);
				}
			},
			error: function(result) {
				console.log(result);
				chamarPopupErro("Houve um erro, tente atualizar a página!");
			}
		});
	}
});

$("#excluir").click(function() {
	data = {};
	data['funcao'] = "excluir";
	data['caminho'] = path;
	if (typeof guias != "undefined") data['extra'] = "guias";
	else if (typeof privado != "undefined") data['extra'] = "privado";
	if (path == "/privado/privado_contabilidade") {
		chamarPopupInfo("Pasta não pode ser Excluida!");
		return;
	}
	$.ajax({
		url: "../php/contabilidade/gerenciarArquivos.php",
		type: "post",
		data: data,
		success: function(result) {
			result = JSON.parse(result);
			if (result.estado == 1) {
				chamarPopupConf(result.mensagem);
				arquivos = result.lista;
				$("#arvore").children().remove();
				atualizarArvore(arquivos['nomes'], $("#arvore"), arquivos['caminhos'], arquivos['informacoes']);
			} else {
				chamarPopupErro(result.mensagem);
			}
		},
		error: function(result) {
			console.log(result);
			chamarPopupErro("Houve um erro, tente atualizar a página!");
		}
	});
});

$("#renomear").click(function() {
	if (path == "/privado/privado_contabilidade") {
		chamarPopupInfo("Pasta não pode ser Renomeada!");
		return;
	}
	$("#campo-texto h3").text("Renomear");
	$("#campo-texto").fadeIn().css({
		display: "flex"
	});
	funcao = "renomear";
	if (tipo == "pasta") {
		texto = selecionado.find("span").text();
		$("#campo-texto input").val(texto).focus();
	} else {
		texto = JSON.parse(selecionado.attr("data-inf")).filename;
		$("#campo-texto input").val(texto).focus();
	}
	$("#campo-texto input").select()
});

$("#mover").click(function() {
	if (path == "/privado/privado_contabilidade") {
		chamarPopupInfo("Pasta não pode ser movida!");
		return;
	}

	mover = path;
	$('#arvore').children().remove();
	mostrarArquivos = false;
	atualizarArvore(arquivos['nomes'], $("#arvore"), arquivos['caminhos'], arquivos['informacoes']);

	$('#mover, #renomear, #excluir').hide();
	$('#cancelar, #conf-mover').show();

	chamarPopupInfo('Escolha o local de destino para <br><strong>'+selecionado.text()+'</strong>');
	$("#arvore > h4").click();
});

$("#cancelar").click(function() {
	// vai cancelar o move
	mover = '';
	$('#arvore').children().remove();
	mostrarArquivos = true;
	atualizarArvore(arquivos['nomes'], $("#arvore"), arquivos['caminhos'], arquivos['informacoes']);

	$('#mover, #renomear, #excluir').show();
	$('#cancelar, #conf-mover').hide();
	$("#arvore > h4").click();
});

$("#conf-mover").click(function() {

});

$("#enviar-arquivos").bind("drop", function(e) {
	e.preventDefault();
	e.stopPropagation();
	if ($("#enviar-arquivos label").hasClass("over")) $("#enviar-arquivos label").removeClass("over");
	//    console.log(e.originalEvent.dataTransfer.files.length);
	tamanho = e.originalEvent.dataTransfer.files.length;
	if (tamanho == 0) {
		console.log("Escolha um arquivo!");
		return;
	} else if (tamanho == 1) {
		$("#enviar-arquivos #nome").text($("#enviar-arquivos input").val().replace(/C:\\fakepath\\/i, ''));
	} else {
		$("#enviar-arquivos #nome").text(tamanho + " arquivo(s) escolhido(s)");
	}
	if (tamanho > 0) {
		$("#enviar-arquivos button").show();
	}
	$("#enviar-arquivos input[type=file]")[0].files = e.originalEvent.dataTransfer.files;
});

$("#enviar-arquivos input").bind("change", function(e) {
	tamanho = e.target.files.length;
	if (tamanho == 0) {
		console.log("Escolha um arquivo!");
		return;
	} else if (tamanho == 1) {
		$("#enviar-arquivos #nome").text($(this).val().replace(/C:\\fakepath\\/i, ''));
	} else {
		$("#enviar-arquivos #nome").text(tamanho + " arquivo(s) escolhido(s)");
	}
	if (tamanho > 0) {
		$("#enviar-arquivos button").show();
	}
});

$("#enviar-arquivos").bind("dragover", function(e) {
	e.preventDefault();
	e.stopPropagation();
	if (!$("#enviar-arquivos label").hasClass("over")) $("#enviar-arquivos label").addClass("over");
});

$("#enviar-arquivos").bind("dragenter", function(e) {
	e.preventDefault();
	e.stopPropagation();
	if ($("#enviar-arquivos label").hasClass("over")) $("#enviar-arquivos label").removeClass("over");
});

$("#enviar-arquivos form").submit(function(e) {
	e.preventDefault();
	if (tipo == "arquivo") {
		chamarPopupInfo("Selecione uma pasta de destino para os arquivos!");
		return;
	}
	form = this;
	data = new FormData(this);
	data.append("funcao", "upload");
	data.append("caminho", path);

	$(this).find("button, input").attr("disabled", true);
	$.ajax({
		url: "../php/contabilidade/gerenciarArquivos.php",
		type: "post",
		data: data,
		success: function(result) {
			result = JSON.parse(result);
			console.log(result);
			if (result.estado == 1) {
				chamarPopupConf(result.mensagem);
				arquivos = result.lista;
				$.each(result.arquivos, function(i, value) {
					item = $("<li class='arquivo' data-inf='" + JSON.stringify(value) + "'>" + value.basename + "</li>").appendTo(selecionado.next());
					if (value.extension == "pdf") {
						item.addClass("pdf");
					} else if (value.extension == "jpg" || value.extension == "jpeg" || value.extension == "png" || value.extension == "gif") {
						item.addClass("imagem");
					} else {
						item.addClass("outro");
					}
				});
				form.reset();
				$("#nome").text("");
				$("#enviar-arquivos button").hide();
			} else if (result.estado == 2) {
				chamarPopupInfo(result.mensagem);
			} else {
				chamarPopupErro(result.mensagem);
			}
			$("#enviar-arquivos form").find("button, input").attr("disabled", false);
		},
		error: function(result) {
			console.log(result);
			chamarPopupErro("Houve um erro, tente atualizar a página!");
		},
		processData: false,
		contentType: false
	});
})