var upload = false;
var editar = false;
var filtro = 0;
var tempId;
var estado = 0,
	mes = 0,
	ano = 0;
$(document).ready(function() {
	// $("[name=data_realizar]").mask("00/00/0000");
	$("[name=mes]").mask("00");
	$("[name=ano]").mask("0000");
	atualizarObrigacoes();
	obrigacoes = obrigacoes == null ? [] : obrigacoes;
});

function atualizarObrigacoes(estado, mes, ano) {
	estado = estado || 0;
	mes = mes || 0;
	ano = ano || 0;
	$("table tr:not(:first-child)").remove();
	if (obrigacoes.length > 0) ordenarPorData();
	$.each(obrigacoes, function(i, value) {
		if (estado == 1) {
			if (value.realizado == 1) return true;
		} else if (estado == 2) {
			if (value.realizado == 0) return true;
		}
		if (mes != 0 && mes < 13) {
			mes = Number(mes);
			if (new Date(value.data_realizar * 1000).getMonth() + 1 != mes) return true;
		}
		if (ano != 0 && ano > 1000 && ano < 10000) {
			ano = Number(ano);
			if (new Date(value.data_realizar * 1000).getFullYear() != ano) return true;
		}
		$("#obrigacoes table").append("<tr class='spacer'><td></td></tr>");
		time = toDate(value.data_realizar);
		temp = "<tr data-id='" + i + "'>";
		temp += "<td data-nome='Estado'>" + (value.realizado == 1 ? "<img src='../img/input-mark.png'>" : "<img src='../img/exclamacao.png' class='pendente'>") + "</td>";
		temp += "<td data-nome='Periodo'>" + getPeriodo(value.periodo) + "</td>";
		temp += "<td data-nome='Descrição'>" + value.descricao + "</td>";
		temp += "<td data-nome='Realizado'><button " + (value.realizado == 1 ? "disabled class='botao'" : "class='finalizar botao' title='Quando esa obrigação for realizada, clique nesse botão!'") + ">" + (value.realizado == 1 ? "Realizado" : "Finalizar") + "</button></td>";
		temp += "<td data-nome='Para'>" + time + "</td>";
		temp += "<td data-nome='Anexo' class='anexo center'>" + (value.tem_anexo == 1 ? "<a href='../servidor/empresa/" + empresa.id + "/anexos/" + (new Date(value.time * 1000).getFullYear()) + "/" + value.id + "." + value.extensao + "' download>" : "") + "<img src='../img/anexo.png' " + (value.tem_anexo == 0 ? "class='sem-anexo botao' title='Não possui anexos'" : "class='botao' title='Possui Anexos'") + ">" + (value.tem_anexo == 1 ? "</a>" : "") + "</td>";
		temp += "<td data-nome='Ação' class='center'><img src='../img/editar-branco.png' class='editar botao amarelo margem' title='Editar obrigação'><img src='../img/lixeira-branca.png' class='excluir botao vermelho' title='Excluir obrigação'></td>";
		temp += "</tr>";
		$("#obrigacoes table").append(temp);
	});
}

function ordenarPorData() {
	obrigacoes.sort(function(a, b) {
		return a.data_realizar < b.data_realizar;
	});
}

function toDate(time) {
	data = new Date(time * 1000);
	// return colocarZero(data.getDate())+"/"+colocarZero(data.getMonth()+1)+"/"+data.getFullYear()+", "+colocarZero(data.getHours())+":"+colocarZero(data.getMinutes());
	return colocarZero(data.getDate()) + "/" + colocarZero(data.getMonth() + 1) + "/" + data.getFullYear();
}

function colocarZero(numero) {
	if (numero < 10) {
		numero = "0" + numero;
	}
	return numero;
}

function getPeriodo(p) {
	if (p == 1) return "Mensal";
	else if (p == 2) return "Bimestral";
	else if (p == 3) return "Trimestral";
	else if (p == 4) return "Semestral";
	else if (p == 5) return "Anual";
	return "error";
}

function novaData(data, periodo) {
	meses = 0;

	if (periodo==1)	meses = 1;
	else if (periodo==2)	meses = 2;
	else if (periodo==3)	meses = 3;
	else if (periodo==4)	meses = 6;
	else if (periodo==5)	meses = 12;

	nova_data = new Date(data*1000);
	novo_mes = nova_data.getMonth() + meses;
	nova_data.setMonth(novo_mes);
	// console.log(nova_data);
	return colocarZero(nova_data.getDate())+''+colocarZero(nova_data.getMonth()+1)+''+nova_data.getFullYear();
}

$("#upload").bind("drop", function(e) {
	e.preventDefault();
	e.stopPropagation();
	if ($("#upload label").hasClass("over")) $("#upload label").removeClass("over");
	// console.log(e.originalEvent.dataTransfer.files.length);
	tamanho = e.originalEvent.dataTransfer.files.length;
	if (tamanho == 0) {
		console.log("Escolha um arquivo!");
		return;
	}
	$("#upload input[type=file]")[0].files=e.originalEvent.dataTransfer.files;
	// $("#adicionar input[type=file]")[0].files=e.originalEvent.dataTransfer.files;
	nome = $('#upload input').val().replace(/C:\\fakepath\\/i, '');
	$("#upload #nome").text(nome);
	$("#nome-anexo").text(nome);
});

$("#upload #image-upload-2").bind("change", function(e) {
	$("#adicionar input[type=file]")[0].files = e.target.files;
	nome = $(this).val().replace(/C:\\fakepath\\/i, '');
	$("#upload #nome").text(nome);
	$("#nome-anexo").text(nome);
});

$("#adicionar #image-upload").bind("change", function(e) {
	$("#anexo").css({background: "rgb(22, 136, 206)"});
	nome = $(this).val().replace(/C:\\fakepath\\/i, '');
	$("#upload #nome").text(nome);
	$("#nome-anexo").text(nome);
});

$("#upload").bind("dragover", function(e) {
	e.preventDefault();
	e.stopPropagation();
	if (!$("#upload label").hasClass("over")) $("#upload label").addClass("over");
});

$("#upload").bind("dragenter", function(e) {
	e.preventDefault();
	e.stopPropagation();
	if ($("#upload label").hasClass("over")) $("#upload label").removeClass("over");
});

$("#anexo").click(function() {
	$("#upload-imagem").fadeIn().css({display: "flex"});
	
	upload = true;
});

$("#estado select").change(function() {
	valor = $(this).val();
	filtro = valor;
	estado = valor;
	atualizarObrigacoes(valor, mes, ano);
});

$("[name=mes]").keyup(function() {
	valor = $(this).val();
	mes = valor;
	if (ano == 0) atualizarObrigacoes(estado, mes);
	else atualizarObrigacoes(estado, mes, ano);
});

$("[name=ano]").keyup(function() {
	valor = $(this).val();
	ano = valor;
	atualizarObrigacoes(estado, mes, ano);
});

$("#upload-imagem > img, #upload-imagem #button button").click(function() {
	$("#upload-imagem").fadeOut();
	upload = false;
});

$("#adicionar #cancelar").click(function() {
	editar = false;
	$("#adicionar form")[0].reset();
	$("#adicionar button").text("Criar");
	$("#adicionar h3").text("Nova Obrigação");
	$("#adicionar #cancelar").hide();
});

$(document).on("click", "tr .finalizar", function() {
	tempId = $(this).parent().parent().attr("data-id");

	data = {funcao: 'finalizar', id: obrigacoes[tempId].id, realizado: 1};
	// nova_data = novaData(obrigacoes[tempId].data_realizar, obrigacoes[tempId].periodo);
	// console.log(nova_data); return;
	$.ajax({
		url: "../php/contabilidade/manterObrigacao.php",
		type: "post",
		data: data,
		success: function(result) {
			result = JSON.parse(result);
			console.log(result);
			if (result.estado == 1) {
				chamarPopupInfo('Preparamos os campos para ser adicionado a nova obrigação!');
				chamarPopupConf(result.mensagem);
				obrigacoes[tempId].realizado = 1;
				atualizarObrigacoes();

				// Preencher form com novas informações
				$("#adicionar h3").text("Nova Obrigação");
				$("#adicionar [name=periodo]").val(obrigacoes[tempId].periodo).change();
				nova_data = novaData(obrigacoes[tempId].data_realizar, obrigacoes[tempId].periodo);

				$("#adicionar [name=data_realizar]").val(nova_data).trigger('input');
				$("#adicionar [name=descricao]").val(obrigacoes[tempId].descricao).focus().select();
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

$(document).on("click", "tr td img.editar", function() {
	// console.log("a")
	editar = true;
	tempId = $(this).parent().parent().attr("data-id");
	$("[name=descricao]").val(obrigacoes[tempId].descricao);
	$("[name=data_realizar]").val(toDate(obrigacoes[tempId].data_realizar));
	$("[name=periodo]").val(obrigacoes[tempId].periodo).focus();
	$("#adicionar button").text("Atualizar");
	$("#adicionar h3").text("Editar Obrigação");
	$("#adicionar #cancelar").show();
});

$(document).on("click", "tr td img.excluir", function() {
	tempId = $(this).parent().parent().attr("data-id");
	$.ajax({
		url: "../php/contabilidade/manterObrigacao.php",
		type: "post",
		data: {
			obrigacao: obrigacoes[tempId],
			realizado: 1,
			funcao: "excluir",
			id: obrigacoes[tempId].id
		},
		success: function(result) {
			result = JSON.parse(result);
			console.log(result);
			if (result.estado == 1) {
				chamarPopupConf(result.mensagem);
				obrigacoes.splice(tempId, 1);
				atualizarObrigacoes(filtro);
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

$("#adicionar form").submit(function(e) {
	e.preventDefault();
	form = this;
	// data = $(this).serialize();
	data = new FormData(form);
	funcao = editar == true ? "editar" : "novo";
	data.append('funcao', funcao);

	if (funcao == "editar") {
		data.append('id', obrigacoes[tempId].id);
		data.append('time', obrigacoes[tempId].time);
	}
	// console.log(data); return;
	$("#adicionar input, #adicionar select, #adicionar button").attr("disabled", true)
	$.ajax({
		url: "../php/contabilidade/manterObrigacao.php",
		type: "post",
		data: data,
		success: function(result) {
			result = JSON.parse(result);
			console.log(result);
			if (result.estado == 1) {
				chamarPopupConf(result.mensagem);
				if (editar == false) {
					obrigacoes.unshift(result.obrigacao);
					atualizarObrigacoes();
					$("#upload form")[0].reset();
					form.reset();
					$("#anexo").css({
						background: "#cacaca"
					})
					$("#upload #nome").text("");
					$("#adicionar h3").text("Nova Obrigação");
				} else {
					$("#adicionar #cancelar").click();
					obrigacoes[tempId] = result.obrigacao;
					atualizarObrigacoes(filtro);
				}

				$("#anexo").css({
					background: '#cacaca'
				});
				$("#nome-anexo").text("Sem anexo");
			} else {
				chamarPopupErro(result.mensagem);
			}

			$("#adicionar input, #adicionar select, #adicionar button").attr("disabled", false);
		},
		error: function(result) {
			console.log(result);
			chamarPopupErro("Houve um erro, tente atualizar a página!");
		},
		processData: false,
		contentType: false
	});
});