var funcao = "novo";

var id_editar = 0;

var id_excluir = 0;



$(document).ready(function() {

    atualizarFaturamentos();

});



function atualizarFaturamentos() {

    $("#faturamentos tr:not(:first-child)").remove();

    $.each(faturamentos, function(i, value) {

        temp = "<tr data-id='"+i+"'>";

        temp += "<td data-nome='Periodo'>"+pegarMes(Number(value.mes))+"/"+value.ano+"</td>";

        temp += "<td data-nome='Valor Serviço'>R$ "+Number(value.valor_servico).toFixed(2).replace(".",",")+"</td>";

        temp += "<td data-nome='Valor Venda'>R$ "+Number(value.valor_venda).toFixed(2).replace(".",",")+"</td>";

        temp += "<td data-nome='Total Bruto'>R$ "+Number(value.total_bruto).toFixed(2).replace(".",",")+"</td>";

        temp += "<td data-nome='Devolução'>R$ "+Number(value.devolucao).toFixed(2).replace(".",",")+"</td>";

        temp += "<td data-nome='Total Líquidio'>R$ "+Number(value.total_liquido).toFixed(2).replace(".",",")+"</td>";

        temp += "<td data-nome='DAS'>"+value.das+"</td>";

        temp += "<td data-nome='Imposto'>R$ "+Number(value.imposto).toFixed(2).replace(".",",")+"</td>";

        if (typeof empresa=="undefined") temp += "<td data-nome='Ações'><img src='../img/editar-branco.png' class='editar'>";

        if (typeof empresa=="undefined") temp += "<img src='../img/lixeira-branca.png' class='excluir'></td>";

        temp += "</tr>";

        

        temp = temp.replace(/null/g, "0");

        

        $("#faturamentos table").append(temp);

    });

    

    $(".editar").attr("title","Clique para EDITAR este item");

    $(".excluir").attr("title","Clique para EXCLUIR este item");

}



function pegarMes(i) {

    meses = ["Janeiro", "Fevereiro", "Marco", "Abril", "Maio", "Junho", "Julho", "Agosto", "Setembro", "Outubro", "Novembro", "Dezembro"];

    

    return meses[i-1];

}



$("#adicionar label").click(function() {

    $("#novo-faturamento h3").text("Novo Faturamento");

    $("#novo-faturamento").fadeIn().css({display: "flex"});

    funcao = "novo";

    $("[name=mes]").focus();

});



$("#novo-faturamento img").click(function() {

    $("#novo-faturamento").fadeOut();

    

    $("#novo-faturamento form")[0].reset();

});



$(document).on("click",".editar", function() {

    id_editar = $(this).parent().parent().attr("data-id");

    

    $.each(faturamentos[id_editar], function(i, value) {

        $("[name="+i+"]").val(value);

    })

    

    $("#novo-faturamento h3").text("Editar Faturamento");

    $("#novo-faturamento").fadeIn().css({display: "flex"});

    funcao = "editar";

    

    $("[name=mes]").focus();

});



$(document).on("click",".excluir", function() {

    id_excluir = $(this).parent().parent().attr("data-id");

    

    data = {funcao: "excluir", id: faturamentos[id_excluir].id}

    

    $.ajax({

        url: "../php/contabilidade/manterFaturamento.php",

        type: "post",

        data: data,

        success: function(result) {

            result = JSON.parse(result);

            

            console.log(result);

            

            if(result.estado==1) {

                chamarPopupConf(result.mensagem);

                

                faturamentos.splice(id_excluir, 1);

                atualizarFaturamentos();

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



$("#novo-faturamento form").submit(function(e) {

    e.preventDefault();

    

    form = this;

    

    data = $(this).serializeArray();

    temp = {};

    $.each(data.reverse(), function(i, value) {

//        if (value.value!="") {

            temp[value.name] = value.value;

//        }

    });

    

    data = temp;

    data.funcao = funcao;

    

    if (funcao=="editar") {

        data.id = faturamentos[id_editar].id;

    }

    

    if (data.mes == 0) {

        chamarPopupInfo("Escolha um Mês válido!");

        $("[name=mes]").focus();

        return;

    }

    

    if (Object.keys(data).length <=3) {

        chamarPopupInfo("Preencha pelo menos alguma campo de valor!");

        console.log(data);

        return;

    }

    

    $.ajax({

        url: "../php/contabilidade/manterFaturamento.php",

        type: "post",

        data: data,

        success: function(result) {

            result = JSON.parse(result);

            

            console.log(result);

            

            if(result.estado==1) {

                chamarPopupConf(result.mensagem);

                

                $("#novo-faturamento > img").click();

                

                faturamentos = result.faturamentos;

                

                atualizarFaturamentos();

                form.reset();

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