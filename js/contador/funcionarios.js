var novoFuncionario=0; 
var funcao = "novo";
var id_editar = 0;
$(document).ready(function() {
    funcionarios = funcionarios || [];
    atualizarFuncionarios();
    // console.log("a")
});

function toDate(time) {
    data = new Date(time*1000);
    return colocarZero(data.getDate())+"/"+colocarZero(data.getMonth()+1)+"/"+data.getFullYear()+", "+colocarZero(data.getHours())+":"+colocarZero(data.getMinutes());
}

function colocarZero(numero) {
    if (numero<10) {
        numero = "0"+numero;
    }
    return numero;
}

function atualizarFuncionarios(filtro) {
    filtro = filtro || null;
    $("#funcionarios table tr:not(:first-child)").remove();
    $.each(funcionarios, function(i, value) {
        if (filtro!=null) {
            if (value.nome.toLowerCase().indexOf(filtro.toLowerCase()) == -1) {
                return true;
            }
        }
        $("#funcionarios table").append("<tr class='spacer'><td></td></tr>");
        temp = "<tr class='geral' data-id='"+i+"'>";
        temp += "<td data-nome='Nome'>"+value.nome+"</td>";
        temp += "<td data-nome='Funcao'>"+value.funcao+"</td>";
        temp += "<td data-nome='Email'>"+value.email+"</td>";
        temp += "<td data-nome='Senha'>"+value.senha+"</td>";
        temp += "<td data-nome='Ações' class='center'><img src='../img/menu.png' title='Ver informações completas' class='resumo botao margem'><img src='../img/lixeira-branca.png' class='excluir botao vermelho' title='Excluir conta do funcionário'></td>";
        temp += "</tr>";
        $("#funcionarios table").append(temp);
    });
}

$("#pesquisar input").keyup(function(e) {
    valor = $(this).val();
    if (valor.length==0) {
        atualizarFuncionarios();
    } else {
        atualizarFuncionarios(valor);
    }
})

$("#adicionar label").click(function() {
    $("#novo-funcionario").fadeIn().css({display: "flex"});
    funcao = "novo";
    $("#novo-funcionario input, #novo-funcionario select").attr("disabled", false);
    $("#novo-funcionario [name=nome]").focus();
    $("#criar").show();
    $("#editar").hide();
});

$("#novo-funcionario img").click(function() {
    $("#novo-funcionario").fadeOut(function() {
        $(this).find("form")[0].reset();
    });
    console.log("a");
});

$(document).on("click", ".resumo", function() {
    id_editar = $(this).closest('tr').attr("data-id");
    $("#novo-funcionario input, #novo-funcionario select").attr("disabled", true);
    $("#novo-funcionario").fadeIn().css({display: "flex"});
    $.each(funcionarios[id_editar], function(i, value) {
        $("#novo-funcionario [name='"+i+"']").val(value);
    })
    $("#criar").hide();
    $("#editar").show();
});

$(document).on("click", ".excluir", function() {
    funcao = "excluir";
    id_editar = $(this).closest("tr").attr("data-id");
    data = {id: funcionarios[id_editar].id, operacao: funcao};
    // console.log(data); return;
    $.ajax({
        url: "../php/contabilidade/manterFuncionario.php",
        type: "post",
        data: data,
        success: function(result) {
            result = JSON.parse(result);
            if(result.estado==1) {
                chamarPopupConf(result.mensagem);
               // funcionarios[id_editar].estado_conta = result.funcionario.estado_conta;
                funcionarios.splice(id_editar, 1)
                atualizarFuncionarios();
            } else {
                chamarPopupErro(result.mensagem);
            }
            $("#novo-funcionario button").attr("disabled", false);
        }, 
        error: function(result) {
            console.log(result);
            chamarPopupErro("Houve um erro, tente atualizar a página!");
            $("#novo-funcionario button").attr("disabled", false);
        }
    });
});

$("#editar").click(function() {
    $("#criar").show();
    $("#editar").hide();
    funcao="editar";
    $("#novo-funcionario input, #novo-funcionario select").attr("disabled", false);
    $("#novo-funcionario [name=nome]").focus().select();
});

$("#novo-funcionario form").submit(function(e) {
    e.preventDefault();
    data = $(this).serializeArray();
    temp = {};
    $.each(data.reverse(), function(i, value) {
        temp[value.name] = value.value;
    });
    data = temp;
    data.operacao = funcao;
    if (funcao=="editar") {
        data.id = funcionarios[id_editar].id;
    }
    if ($("#novo-funcionario [name=senha]").val().length<8) {
        chamarPopupInfo("A senha deve ter no mínimo 8 digitos!");
        $("#novo-funcionario [name=senha]").focus();
        return;
    }
    $("#novo-funcionario button").attr("disabled", true);
    $.ajax({
        url: "../php/contabilidade/manterFuncionario.php",
        type: "post",
        data: data,
        success: function(result) {
           // console.log(result);
            result = JSON.parse(result);
            if(result.estado==1) {
                chamarPopupConf(result.mensagem);
                if (funcao=="editar") {
                    funcionarios[id_editar] = result.funcionario;
                } else {
                    funcionarios.push(result.funcionario);
                }
                atualizarFuncionarios();
                $("#novo-funcionario img").click();
            } else {
                chamarPopupErro(result.mensagem);
            }
            $("#novo-funcionario button").attr("disabled", false);
        }, 
        error: function(result) {
            console.log(result);
            chamarPopupErro("Houve um erro, tente atualizar a página!");
            $("#novo-funcionario button").attr("disabled", false);
        }
    });
});
