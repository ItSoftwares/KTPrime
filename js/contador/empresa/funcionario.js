var funcao = "novo";
var id_editar=0, id_excluir=0;

$(document).ready(function() {
    atualizarFuncionarios();
    
    $("[name=data_admissao]").mask("00/00/0000")
});

function atualizarFuncionarios() {
    $("#funcionarios table tr:not(:first-child)").remove();
    
    $.each(funcionarios, function(i, value) {
        temp = "<tr data-id="+i+">";
        temp += "<td data-nome='Ativo' class='estado'>"+(value.ativo==0?" <img src='../img/exclamacao.png' class='pendente'>":" <img src='../img/input-mark.png'>")+"</td>";
        temp += "<td data-nome='Nome'>"+(value.nome)+"</td>";
        temp += "<td data-nome='Função'>"+(value.funcao)+"</td>";
        temp += "<td data-nome='Ações'><img src='../img/menu.png' title='Ver informações completas' class='resumo'>";
        if (typeof funcionarios == "undefined") temp += "<img src='../img/lixeira-branca.png' class='excluir'>";
        temp += "</td>";
        temp += "</tr>";
        
        $("#funcionarios table").append(temp);
    });
    
    if (typeof tela_funcionario != "undefined") $(".excluir").css({opacity: .3, cursor: "default"})
}

$(document).on("click", ".resumo", function() {
    id_editar = $(this).parent().parent().attr("data-id");
    
    $.each(funcionarios[id_editar], function(i, value) {
        $("#novo-funcionario [name="+i+"]").val(value);
    });
    
    $("#novo-funcionario h3").text("Detalhes do Funcionário");
    $("#novo-funcionario button#editar").show();
    $("#novo-funcionario button#criar").hide();
    $("#novo-funcionario").fadeIn().css({display: "flex"});
    $("#novo-funcionario input, #novo-funcionario textarea, #novo-funcionario select").attr("disabled", true)
});

$("#editar").click(function() {
    $("#novo-funcionario button#editar").hide();
    $("#novo-funcionario button#criar").show();
    $("#novo-funcionario input, #novo-funcionario textarea, #novo-funcionario select").attr("disabled", false);
    funcao = "editar";
    $("#novo-funcionario [name=nome]").focus().select();
});

$("#adicionar label").click(function() {
    funcao = "novo";
    $("#novo-funcionario button#editar").hide();
    $("#novo-funcionario button#criar").show();
    $("#novo-funcionario input, #novo-funcionario textarea, #novo-funcionario select").attr("disabled", false);
    $("#novo-funcionario").fadeIn().css({display: "flex"});
    $("#novo-funcionario [name=nome]").focus().select();
});

$("#novo-funcionario img").click(function() {
    $("#novo-funcionario").fadeOut(function() {
        $("#novo-funcionario form")[0].reset();
    });
});

$("#novo-funcionario form").submit(function(e) {
    e.preventDefault();
    
    data = $(this).serialize();
    
    data += "&operacao="+funcao;
    if (funcao=="editar") data += "&id="+funcionarios[id_editar].id;
    
    $.ajax({
        url: "../php/empresa/manterFuncionario.php",
        type: "post",
        data: data,
        success: function(result) {
            result = JSON.parse(result);
            
            console.log(result);
            
            if(result.estado==1) {
                chamarPopupConf(result.mensagem);
                funcionarios = result.funcionarios;
                $("#novo-funcionario img").click();
                atualizarFuncionarios();
                $("#novo-funcionario form")[0].reset();
            }  else {
                chamarPopupErro(result.mensagem);
            }
        }, 
        error: function(result) {
            console.log(result);
            chamarPopupErro("Houve um erro, tente atualizar a página!");
        }
    })
});

$(document).on("click",".excluir", function() {
    if (typeof tela_funcionario != "undefined") return;
    
})