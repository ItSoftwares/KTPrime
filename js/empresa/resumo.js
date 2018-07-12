$(document).ready(function() {
    $("input, textarea, select").attr("disabled", true);
    
    for(var i=28;i>0;i--) {
        $("[name=dia_vencimento]").append("<option value='"+i+"'>"+i+"</option>");
    }
    
    $.each(usuario, function(i, value) {
        if (i=="validade_cd") {
            dataTemp = new Date(Number(value)*1000);
            $("[name="+i+"]").val(colocarZero(dataTemp.getDate())+"/"+colocarZero(dataTemp.getMonth())+"/"+dataTemp.getFullYear());
            return true;
        }
        $("[name="+i+"]").val(value);
    });
    
    $("[name=celular_1]").mask("(00) 00000-0000");
    $("[name=celular_2]").mask("(00) 00000-0000");
    $("[name=celular_3]").mask("(00) 00000-0000");
    $("[name=celular_4]").mask("(00) 00000-0000");
    $("[name=telefone_1]").mask("(00) 90000-0000");
    $("[name=telefone_2]").mask("(00) 90000-0000");
    $("[name=telefone_3]").mask("(00) 90000-0000");
    $("[name=cpf]").mask("000.000.000-00");
    $("[name=cpf_2]").mask("000.000.000-00");
    $("[name=cpf_3]").mask("000.000.000-00");
    $("[name=cpf_4]").mask("000.000.000-00");
    $("[name=cnpj]").mask("00.000.000/0000-00");
    $("[name=cep]").mask("00 000-000");
    $("[name=data_abertura]").mask("00/00/0000");
    $("[name=validade_cd]").mask("00/00/0000");

    if (typeof inicio != "undefined") {
        $("#arvore").children().remove();
        atualizarArvore(arquivos['nomes'], $("#arvore"), arquivos['caminhos'], arquivos['informacoes']);
    }
});

function colocarZero(numero) {
    if (numero<10) {
        numero = "0"+numero;
    }
    
    return numero;
}

function atualizarArvore(lista, elemento, caminho, informacoes) {
   // teste = pai.append("<h4 class='nome-pasta'>"+usuario.razao_social+"</h4>");
    if ($("#arvore h4").length==0) {
        $("<h4 class='nome-pasta selecionado' data-pai=1 data-aberto=1><span>Arquivos para baixar ou visualizar</span></h4>").appendTo(elemento);
        elemento = $("<ul class='pasta' data-aberto=1></ul>").appendTo(elemento);
    }
    $.each(lista, function(i, value) {
        if (typeof value != "object") {
            item = $("<li class='arquivo' data-inf='"+JSON.stringify(informacoes[i])+"'>"+value+"</li>").appendTo(elemento);
            
            if (informacoes[i].extension=="pdf") {
                item.addClass("pdf");
            } else if (informacoes[i].extension=="jpg" || informacoes[i].extension=="jpeg" || informacoes[i].extension=="png" || informacoes[i].extension=="gif") {
                item.addClass("imagem");
            } else {
                item.addClass("outro");    
            }
        } else {
            $("<h4 class='nome-pasta' data-aberto=0><span>"+i+"</span></h4>").appendTo(elemento);
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

$(document).on("click", "h4.nome-pasta", function() {
    if ($(this).attr("data-pai")==1) return;
    pasta = $(this).next();
    
    aberto = pasta.attr("data-aberto");
    
    aberto = aberto==0?1:0;
    pasta.attr("data-aberto", aberto);
    $(this).attr("data-aberto", aberto);
});

$(document).on("click", "li.arquivo", function() {
    selecionar(this);
    inf = JSON.parse($(this).attr("data-inf"));
    classe = "outro";
    
    if ($(this).hasClass("pdf")) {
        classe = "pdf";
        $("#baixar, #ver").removeClass('disabled');
    } else if ($(this).hasClass("imagem")) {
        classe = "imagem";
        $("#baixar, #ver").removeClass('disabled');
    } else {
        $("#baixar").removeClass('disabled');
        $("#ver").addClass('disabled');
    }

    link = inf.dirname.replace("/home/cnpjn035/public_html","")+"/"+inf.basename;
    $("#baixar, #ver").attr("href", link);
});

$("#ver").click(function(e) {
    if ($(this).hasClass('disabled')) e.preventDefault();
});