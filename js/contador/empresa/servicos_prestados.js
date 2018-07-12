var upload = false;
var filtro = 0;

$(document).ready(function() {
    servicosPrestados = servicosPrestados==null?[]:servicosPrestados;
    historicoPagamentos = historicoPagamentos==null?[]:historicoPagamentos;
    $("#adicionar [name=data]").mask("00/00/0000");
    
    if (historicoPagamentos.length>0) {
        temp = {};
        $.each(historicoPagamentos, function(i, value) {
            temp[value.id_historico] = value;
        });
        historicoPagamentos = temp;
    }
    atualizarServicosPrestados();
});

function atualizarServicosPrestados(estado) {
    estado = estado || 0;
    $("table tr:not(:first-child)").remove();
    
    $.each(servicosPrestados, function(i, value) {
        if (estado==1) {
            if (value.realizado==1) return true;
        } else if (estado==2) {
            if (value.realizado==0) return true;
        }
        
        $("#servicos-prestados table").append("<tr class='spacer'><td></td></tr>");
        time = toDate(value.data);
        
        pago = "";
        
        if (value.id_historico in historicoPagamentos && (historicoPagamentos[value.id_historico].estado==3 || historicoPagamentos[value.id_historico].estado==4)) pago = "pago";
        
       // console.log((Number(value.id_hist
        
        temp = "<tr data-id='"+i+"'>";
        temp += "<td data-nome='Estado'><img src='../img/input-mark.png'></td>";
        temp += "<td data-nome='Serviço'>"+value.nome_servico+"</td>";
        temp += "<td data-nome='Descrição'>"+value.descricao+"</td>";
        temp += "<td data-nome='Data'>"+time+"</td>";
        temp += "<td data-nome='Anexo' class='anexo'>"+(value.tem_anexo==1?"<a href='../servidor/empresa/"+empresa.id+"/anexos/"+(new Date(value.data * 1000).getFullYear())+"/"+value.id+"."+value.extensao+"' download>":"")+"<img src='../img/anexo.png' "+(value.tem_anexo==0?"class='sem-anexo' title='Não possui anexos'":"title='Possui Anexos'")+">"+(value.tem_anexo==1?"</a>":"")+"</td>";
        temp += "<td data-nome='Ação'>";
        temp += !(value.id in servicosMensais)?"<img src='../img/mensal.png' class='mensal' title='Clique aqui para manter esse serviço mensalmente'>":"<img src='../img/mensal-ok.png' class='mensal ok' title='Clique aqui para remover esse serviço mensalmente'>"
        temp += "<img src='../img/lixeira-branca.png' class='excluir "+pago+"' title='Excluir serviço prestado'>";
        temp += "</td></tr>";
        
        $("#servicos-prestados table").append(temp);
    });
}

function toDate(time) {
    data = new Date(time*1000);
    // return colocarZero(data.getDate())+"/"+colocarZero(data.getMonth()+1)+"/"+data.getFullYear()+", "+colocarZero(data.getHours())+":"+colocarZero(data.getMinutes());
    return colocarZero(data.getDate())+"/"+colocarZero(data.getMonth()+1)+"/"+data.getFullYear();
}

function colocarZero(numero) {
    if (numero<10) {
        numero = "0"+numero;
    }
    
    return numero;
}

$("#upload").bind("drop", function(e) {
    e.preventDefault();
    e.stopPropagation();
    if ($("#upload label").hasClass("over")) $("#upload label").removeClass("over");
   // console.log(e.originalEve
    tamanho = e.originalEvent.dataTransfer.files.length;
    if (tamanho==0) {
        console.log("Escolha um arquivo!");
        return;
    }
    
    $("#upload input[type=file]")[0].files=e.originalEvent.dataTransfer.files;
    // $("#adicionar input[type=file]")[0].files=e.originalEvent.dataTransfer.files;
    nome = $('#upload input').val().replace(/C:\\fakepath\\/i, '');
    $("#upload #nome").text(nome);
    $("#nome-anexo").text(nome);
});

$("#upload input[type=file]").bind("change", function(e) {
    // console.log("teste");
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

$("#estado select").change(function() {
    valor = $(this).val();
    filtro = valor;
    atualizarServicosPrestados(valor);
});

$("#anexo").click(function() {
    $("#upload-imagem").fadeIn().css({display: "flex"});
    
    upload = true;
});

$("#upload-imagem > img").click(function() {
    $("#upload-imagem").fadeOut();
    
    upload = false;
});

$("#upload-imagem button").click(function() {
    $("#upload-imagem").fadeOut();
});

$(document).on("click", "tr td:last-child img.excluir:not(.pago)", function() {
    tempId = $(this).parent().parent().attr("data-id");
    
    $.ajax({
        url: "../php/contabilidade/manterServicoPrestado.php",
        type: "post",
        data: {servicoPrestado: servicosPrestados[tempId], realizado: 1, funcao: "excluir", id: servicosPrestados[tempId].id},
        success: function(result) {
            result = JSON.parse(result);

            console.log(result);

            if (result.estado==1) {
                chamarPopupConf(result.mensagem);
                servicosPrestados.splice(tempId, 1);
                atualizarServicosPrestados(filtro);
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

$(document).on("click", "tr td:last-child img.excluir.pago", function() {
    chamarPopupInfo("Este serviço já foi pago não pode ser excluido!")
});

$("#adicionar form").submit(function(e) {
    e.preventDefault();
    form = this;
    data = new FormData();
    data.append("id_servico", $("[name=id_servico]").val());
    data.append("funcao", "novo");
    data.append("data", $("[name=data]").val());
    data.append("descricao", $("[name=descricao]").val());
    data.append("anexo", $("[name=image-upload]")[0].files[0]);
    data.append("valor", servicos[$("[name=id_servico]").val()].valor);
    data.append("nome_servico", servicos[$("[name=id_servico]").val()].nome);
    console.log(data);
    $("#adicionar button").attr("disabled", true);
    $.ajax({
        url: "../php/contabilidade/manterServicoPrestado.php",
        type: "post",
        data: data,
        success: function(result) {
            result = JSON.parse(result);

            console.log(result);

            if (result.estado==1) {
                chamarPopupConf(result.mensagem);
                servicosPrestados.unshift(result.servicoPrestado);
                atualizarServicosPrestados();
                $("#upload form")[0].reset();
                form.reset();
                $("#anexo").css({background: "#cacaca"})
                $("#upload #nome").text("");
            } else {
                chamarPopupErro(result.mensagem);
            }
            $("#adicionar button").attr("disabled", false);
        }, 
        error: function(result) {
            console.log(result);
            chamarPopupErro("Houve um erro, tente atualizar a página!");
            $("#adicionar button").attr("disabled", false);
        },
        processData: false,
        contentType: false
    });
});

$(document).on("click", "tr td img.mensal",function() {
    tempId = $(this).parent().parent().attr("data-id");
    
    funcao = "";
    
    if ($(this).hasClass("ok")) funcao = "remover";
    else funcao = "adicionar";
    
    $.ajax({
        url: "../php/contabilidade/manterServicoPrestado.php",
        type: "post",
        data: {servicoPrestado: servicosPrestados[tempId], funcao: funcao},
        success: function(result) {
            result = JSON.parse(result);

            console.log(result);

            if (result.estado==1) {
                chamarPopupConf(result.mensagem);
                
                if (funcao=="remover") {
                    delete servicosMensais[servicosPrestados[tempId].id];
                } else {
                    servicosMensais[result.novo.id_servico] = (result.novo);
                }
                
                atualizarServicosPrestados()
            } else {
                chamarPopupErro(result.mensagem);
            }
        }, 
        error: function(result) {
            console.log(result);
            chamarPopupErro("Houve um erro, tente atualizar a página!");
            $("#adicionar button").attr("disabled", true);
        }
    });
});