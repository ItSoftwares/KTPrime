var periodos = {};

$(document).ready(function() {
	mensal = mensal==null?[]:mensal;

	$.each(mensal, function(i, value) {
		periodos[value.mes+"-"+value.ano] = value;
	});

	atualizarTabela(mensal[atual].array);
});

$("#adicionar").click(function() {
	$("#novo").fadeIn().css("display", "flex");
});

$("#novo .fechar").click(function() {
	$("#novo").fadeOut();
});

$(document).on("click", "table input", function() {
	checada = $(this).is(":checked");
	// console.log(checada);
	pai = $(this).closest('tr').attr("data-id");
	temp = JSON.parse(mensal[atual].array);

	if (!(pai in temp)) temp[pai] = {feito: checada?1:0};
	else temp[pai].feito = checada?1:0;
	//"{"1":{"feito":1},"2":{"feito":1},"3":{"feito":0},"4":{"feito":1},"5":{"feito":1},"6":{"feito":1},"7":{"feito":1},"8":{"feito":0},"9":{"feito":1},"10":{"feito":0},"11":{"feito":0},"12":{"feito":0},"13":{"feito":1},"14":{"feito":1},"15":{"feito":0},"16":{"feito":1},"17":{"feito":0},"18":{"feito":0},"19":{"feito":1},"20":{"feito":1},"21":{"feito":0},"22":{"feito":0},"23":{"feito":1},"24":{"feito":1},"25":{"feito":1},"26":{"feito":0},"27":{"feito":0},"28":{"feito":0},"29":{"feito":1},"30":{"feito":0},"31":{"feito":0},"32":{"feito":1},"33":{"feito":0},"35":{"feito":0},"36":{"feito":1},"37":{"feito":0},"38":{"feito":1},"41":{"feito":0},"42":{"feito":1},"43":{"feito":0},"44":{"feito":1},"45":{"feito":1},"46":{"feito":0},"47":{"feito":1},"48":{"feito":1},"49":{"feito":0},"50":{"feito":1},"51":{"feito":1}}"
	data = {};
	data.funcao = "editar";
	data.id = mensal[atual].id;
	data.array = JSON.stringify(temp);

	$("table input, #periodo").attr("disabled", true);
	$.ajax({
        url: "../php/contabilidade/manterMensal.php",
        type: "post",
        data: data,
        success: function(result) {
            console.log(result);
            result = JSON.parse(result);
           
            if(result.estado==1) {
            	// chamarPopupConf(result.mensagem);

            	mensal[atual].array = data.array;
            } else  {
            	chamarPopupInfo(result);
            }

            $("table input, #periodo").attr("disabled", false);
        }, 
        error: function(result) {
        	$("table input, #periodo").attr("disabled", false);
            console.log(result);
            chamarPopupErro("Houve um erro, tente atualizar a página!");
        }
    });
});

$("#periodo").change(function() {
	atual = $(this).val();
	atualizarTabela(mensal[atual].array);
});

$("#novo form").submit(function(e) {
	e.preventDefault();

	data = $(this).serializeArray();

	temp = {};
	$.each(data, function(i, value) {
		temp[value.name] = value.value;
	});
	data = temp;

	data.funcao = "novo";
	data.array = JSON.stringify(gerarNovoMes());

	$("#novo button").attr("disabled", true);
	$.ajax({
        url: "../php/contabilidade/manterMensal.php",
        type: "post",
        data: data,
        success: function(result) {
            console.log(result);
            result = JSON.parse(result);
           
            if(result.estado==1) {
            	chamarPopupConf(result.mensagem);

            	index = mensal.push(result.mensal)-1;

            	$('#periodo').append($('<option>', {
				    value: index,
				    text: data.mes+" - "+data.ano
				}));

				$("#periodo").val(index).change();

            	$("#novo .fechar").click();
            } else  {
            	chamarPopupInfo(result);
            }

            $("#novo button").attr("disabled", false);
        }, 
        error: function(result) {
        	$("#novo button").attr("disabled", false);
            console.log(result);
            chamarPopupErro("Houve um erro, tente atualizar a página!");
        }
    });
});

function gerarNovoMes() {
	temp = {};
	$.each(empresas, function(i, value) {
		temp[value.id] = {};
		temp[value.id]['feito'] = 0;
	});

	return temp;
}

function atualizarTabela(lista) {
	lista = JSON.parse(lista);

	$.each(lista, function(i, value) {
		if (value.feito==1) $("tr[data-id="+i+"] input").attr("checked", true);
		else $("tr[data-id="+i+"] input").attr("checked", false);
	});
}