var novo_editar = "novo";
var id_editar = 0;
var ordem=0;

$(document).ready(function() {
   if (servicos==null) servicos=[]; 
    ordenar(ordem, "nome");
    atualizarServicos();
});

function ordenar(ordem, index) {
    if (index>1) return;
    console.log(index);
    servicos.sort(function(a, b) {
        if (ordem==0) {
            if (index=="nome") {
                return a[index] < b[index] ? -1:1;
            } else {
                return Number(a[index]) < Number(b[index]) ? -1:1;
            }
        } else {
            
            if (index=="nome") {
                return a[index] > b[index] ? -1:1;
            } else {
                return Number(a[index]) > Number(b[index]) ? -1:1;
            }
        }
    });
} 

function atualizarServicos() {
    $("table tr:not(:first-child)").remove();
    
    $.each(servicos, function(i, value) {
       $("table tbody").append("<tr class='spacer'><td></td></tr>");
        
        temp = "<tr data-id='"+i+"'>";
        temp += "<td data-nome='Serviço'>"+value.nome+"</td>";
        temp += "<td data-nome='Valor'><span>R$</span> "+(Number(value.valor).toFixed(2).replace(".",","))+"</td>";
        temp += "<td class='center' data-nome='Ação'><img src='../img/editar-branco.png' class='editar botao amarelo margem'>";
        temp += "<img src='../img/lixeira-branca.png' class='excluir botao vermelho'></td>";
        temp += "</tr>"
        
        $("table tbody").append(temp);
    });
    
    if (typeof tela_funcionario!="undefined") $(".excluir, .editar").css({opacity: .3, cursor: "default"});
}

$(document).on("click",".editar", function() {
    if (typeof tela_funcionario!="undefined") return;
    
    novo_editar = "editar";
    id_editar = $(this).parent().parent().attr("data-id");
    console.log(id_editar);
    temp = servicos[id_editar];
    
    $("[name=valor]").val(temp.valor);
    $("[name=nome]").val(temp.nome).focus();
    $("#adicionar button").text("Atualizar");
    $("body, html").animate({
        scrollTop: $("#adicionar").offset().top
    })
});

$(document).on("click",".excluir", function() {
    if (typeof tela_funcionario!="undefined") return;
    id_excluir=$(this).parent().parent().attr("data-id");
    
    $.ajax({
        url: "../php/contabilidade/manterServico.php",
        type: "post",
        data: "funcao=excluir&id_excluir="+id_excluir+"&id="+servicos[id_excluir].id,
        success: function(result) {
            result = JSON.parse(result);

            if (result.estado==1) {
                chamarPopupConf(result.mensagem)
                servicos.splice(id_excluir, 1);
                atualizarServicos();
            } else if (result.estado==2) {
                chamarPopupInfo(result.mensagem)
            } else {
                chamarPopupErro(result);
            }
        }, 
        error: function(result) {
            console.log(result);
            chamarPopupErro("Houve um erro, tente atualizar a página!");
        }
    });
});

$("th").click(function() {
    index = $(this).attr("data-index");
    
    if (typeof index == "undefined") return;
    
    if ($(this).hasClass("selecionado")) {
        ordem = ordem==0?1:0;
        $(this).toggleClass("inverso");
    } else {
        $("th").removeClass();
        $(this).addClass("selecionado");
        ordem=0;
    }
    
    ordenar(ordem, index);
    atualizarServicos();
});

// ajax
$("#adicionar form").submit(function(e) {
    e.preventDefault();
    
    data = $(this).serialize();
    $("#adicionar button").attr("disabled", true);
    if (novo_editar=="novo") {
        $.ajax({
            url: "../php/contabilidade/manterServico.php",
            type: "post",
            data: data+"&funcao=novo",
            success: function(result) {
                result = JSON.parse(result);

                if (result.estado==1) {
                    chamarPopupConf(result.mensagem);
                    servicos.push(result.servico);
                    atualizarServicos();
                    $("#adicionar form")[0].reset();
                } else if (result.estado==2) {
                    chamarPopupInfo(result.mensagem);
                } else {
                    chamarPopupErro(result);
                }
                $("#adicionar button").attr("disabled", false);
            }, 
            error: function(result) {
                console.log(result);
                chamarPopupErro("Houve um erro, tente atualizar a página!");
                $("#adicionar button").attr("disabled", false);
            }
        });
    } 
    else if (novo_editar=="editar") {
        $.ajax({
            url: "../php/contabilidade/manterServico.php",
            type: "post",
            data: data+"&funcao=editar&id_editar="+id_editar+"&id="+servicos[id_editar].id,
            success: function(result) {
                result = JSON.parse(result);

                if (result.estado==1) {
                    chamarPopupConf(result.mensagem);
                    servicos[id_editar] = result.servico;
                    atualizarServicos();
                    $("#adicionar form")[0].reset();
                    $("#adicionar form button").text("Novo");
                    $("[name=nome]").focus();
                    novo_editar = "novo";
                } else if (result.estado==2) {
                    chamarPopupInfo(result.mensagem);
                } else {
                    chamarPopupErro(result);
                }
                $("#adicionar button").attr("disabled", false);
            }, 
            error: function(result) {
                console.log(result);
                chamarPopupErro("Houve um erro, tente atualizar a página!");
                $("#adicionar button").attr("disabled", false);
            }
        });
    }
});