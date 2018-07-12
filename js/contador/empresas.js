var novaEmpresa = 0;
var tela = 3;
var cabecalho = ['', 'Razão Social', 'CNPJ', 'Telefone'];
var label = "";
var regimes = {
    1: "Simples Nacional",
    2: "Lucro Presumido",
    3: "Lucro Real",
    4: "Outros",
    5: "MEI"
};

$(document).ready(function() {
    $("[name=celular_1]").mask("(00) 00000-0000").trigger('input');
    $("[name=celular_2]").mask("(00) 00000-0000").trigger('input');
    $("[name=celular_3]").mask("(00) 00000-0000").trigger('input');
    $("[name=celular_4]").mask("(00) 00000-0000").trigger('input');
    $("[name=telefone_1]").mask("(00) 90000-0000").trigger('input');
    $("[name=telefone_2]").mask("(00) 90000-0000").trigger('input');
    $("[name=telefone_3]").mask("(00) 90000-0000").trigger('input');
    $("[name=cpf]").mask("000.000.000-00").trigger('input');
    $("[name=cpf_2]").mask("000.000.000-00").trigger('input');
    $("[name=cpf_3]").mask("000.000.000-00").trigger('input');
    $("[name=cpf_4]").mask("000.000.000-00").trigger('input');
    $("[name=cnpj]").mask("00.000.000/0000-00").trigger('input');
    $("[name=cep]").mask("00 000-000").trigger('input');
    $("[name=data_abertura]").mask("00/00/0000").trigger('input');
    $("[name=cliente_desde]").mask("00/00/0000").trigger('input');
    $("[name=validade_cd]").mask("00/00/0000").trigger('input');
    atualizarEmpresas2();
    for (var i = 28; i > 0; i--) {
        $("[name=dia_vencimento]").append("<option value='" + i + "'>" + i + "</option>");
    }
    data = new Date();
    mes = data.getMonth() + 1;
    for (var i = mes; i < mes + 2; i++) {
        if (i > 12) tempMes = i - 12;
        else tempMes = i;
        $("[name=primeiro_mes]").append("<option value='" + tempMes + "'>" + pegarMes(tempMes) + "</option>");
    }
    if (typeof codigo_maior != "undefined") $("[name=codigo_cliente]").val(codigo_maior);
    $(".input input, .input select, .input textarea").each(function() {
        var attr = $(this).attr("required");
        if (typeof attr != "undefined") {
            $(this).parent().find("label").text($(this).parent().find("label").text() + " *");
        }
    });
});

$("#adicionar label, #nova-empresa > div > img").click(function() {
    if (novaEmpresa == 0) {
        $("#nova-empresa").fadeIn().css({
            display: "flex"
        });
    } else {
        $("#nova-empresa").fadeOut();
    }
    novaEmpresa = novaEmpresa == 0 ? 1 : 0;
});

$("#voltar").click(function() {
    //    tela--;
    //    abrirTela(tela);
});

$("#avancar").click(function() {
    teste = validarInputs();
    if (!teste) return;
    $("#nova-empresa form").submit();
    //    if (tela==3) {
    //    } else {
    //        tela++;
    //        
    //        abrirTela(tela);
    //    }
});

$("#pesquisar input").keyup(function(e) {
    valor = $(this).val();
    if (valor.length == 0) {
        //mostrar todas;
        $(".empresa").show();
        return;
    }
    id_hide = [];
    $.each(empresas, function(i, value) {
        if (value.apelido.toLowerCase().indexOf(valor.toLowerCase()) != -1) {
            $(".empresa[data-id=" + value.id + "]").show();
        } else {
            id_hide.push(value.id);
            $(".empresa[data-id=" + value.id + "]").hide();
        }
    });
});

$(document).on("click", ".empresa", function() {
    id = $(this).attr("data-id");
    // if (usuario_logado==3) {
        // location.href = "/funcionarioEmpresaResumo/" + id;
    // } else {
        location.href = "/empresaResumo/" + id;
    // }
})

$("#acesso-rapido li:nth-child(1)").click(function() {
    atualizarEmpresas2();
});

$("#acesso-rapido li:nth-child(2)").click(function() {
    atualizarEmpresas2(3);
});

$("#acesso-rapido li:nth-child(3)").click(function() {
    atualizarEmpresas2(1);
});

$("#acesso-rapido li:nth-child(4)").click(function() {
    atualizarEmpresas2(2);
});

$("#acesso-rapido li:nth-child(5)").click(function() {
    atualizarEmpresas2(4);
});

$("[name=cep]").blur(function() {
    pesquisacep($(this).cleanVal());
});

$("#adicionar label img").hover(function() {
    $(this).siblings("span").show();
}, function() {
    $(this).siblings("span").hide();
});

$("#labels li").click(function() {
    if ($(this).hasClass("selecionado")) {
        $(this).removeClass("selecionado");
        label = "";
        cabecalho.splice(4, 1);
        empresas = original.slice();
        atualizarEmpresas2();
        return;
    }
    $("#labels li").removeClass();
    $(this).addClass("selecionado");
    label = $(this).attr("data-value");
    cabecalho[4] = $(this).text();
    ordenarEmpresas(label);
    atualizarEmpresas2();
});

$("#pesquisar select").change(function() {
    console.log($(this).val());
    if ($(this).val()==0) {
        label = "";
        cabecalho.splice(4, 1);
        empresas = original.slice();
        atualizarEmpresas2();
        return;
    }

    label = $(this).val();
    cabecalho[4] = $(this).find("option:selected").text();
    ordenarEmpresas(label);
    atualizarEmpresas2();
});

function meu_callback(conteudo) {
    if (!("erro" in conteudo)) {
        //Atualiza os campos com os valores.
        $("[name=estado]").val(conteudo.uf);
        $("[name=cidade]").val(conteudo.localidade);
        $("[name=rua]").val(conteudo.logradouro);
        $("[name=bairro]").val(conteudo.bairro);
        $("[name=cidade]").attr("disabled", false);
        $("[name=rua]").attr("disabled", false);
        $("[name=bairro]").attr("disabled", false);
        $("[name=estado]").attr("disabled", false).focus();
    } //end if.
    else {
        //CEP não Encontrado.
        chamarPopupInfo("CEP não encontrado!");
        $("[name=cep]").focus();
    }
}

function pesquisacep(valor) {
    //Nova variável "cep" somente com dígitos.
    var cep = valor.replace(/\D/g, '');
    //Verifica se campo cep possui valor informado.
    if (cep != "") {
        //Expressão regular para validar o CEP.
        var validacep = /^[0-9]{8}$/;
        //Valida o formato do CEP.
        if (validacep.test(cep)) {
            //            $("[name=estado]").attr("disabled", true);
            //            $("[name=cidade]").attr("disabled", true);
            //            $("[name=rua]").attr("disabled", true);
            //            $("[name=bairro]").attr("disabled", true);
            //Cria um elemento javascript.
            var script = document.createElement('script');
            //Sincroniza com o callback.
            script.src = '//viacep.com.br/ws/' + cep + '/json/?callback=meu_callback';
            //Insere script no documento e carrega o conteúdo.
            document.body.appendChild(script);
        } //end if.
        else {
            //cep é inválido.
            chamarPopupInfo("Formato de CEP inválido.");
            $("[name=cep]").focus();
        }
    } //end if.
    else {
        //cep sem valor, limpa formulário.
        limpa_formulário_cep();
    }
}

function atualizaEmpresas(filtro) {
    filtro = filtro || 0;
    $(".empresa").remove();
    console.log(filtro);
    $.each(empresas, function(i, value) {
        if (value.apelido == null) empresas[i].apelido = "";
        if (filtro == 1)
            if (value.pendencia == 0) return true;
        if (filtro == 2)
            if (value.inadimplente == 0) return true;
        if (filtro == 3)
            if (value.estado_conta != 1) return true;
        if (filtro == 4)
            if (value.estado_conta != 0) return true;
        temp = "";
        estado = "normal";
        imagem = "../img/input-mark.png";
        if (value.pendencia == 1) {
            estado = "pendente";
            imagem = "../img/exclamacao.png";
        }
        if (value.estado_conta == 0) {
            if (typeof tela_funcionario != "undefined") return true;
            estado = "inativa";
            imagem = "../img/cadeado.png";
        }
        if (value.inadimplente == 1) {
            estado = "ina";
            imagem = "../img/pare.png";
        }
        temp += "<li class='empresa " + estado + "' data-id='" + value.id + "'>";
        temp += "<h4 class='nome'>" + value.razao_social.substr(0, 20) + ".</h4>";
        temp += "<img src=" + imagem + ">";
        temp += "<p class='cnpj'>CNPJ: <span>" + value.cnpj + "</span></p>";
        temp += "<p class='telefone'>Telefone: <span>" + value.telefone_1 + "</span></p>";
        temp += "</li>";
        $("#empresas ul").append(temp);
    });
    $(".cnpj span").mask("00.000.000/0000-00");
    $(".telefone span").mask("(00) 0000-0000");
}

function atualizarEmpresas2(filtro) {
    filtro = filtro || 0;
    $("#empresas > ul table").remove();
    console.log(filtro);
    //    $("#empresas ul").remove();
    $("#empresas > ul").append("<table>");
    atualizarCabecalho();
    temp = "<tbody>";
    $.each(empresas, function(i, value) {
        //        console.log(value.cnpj);
        if (usuario_logado==3 && funcionario_master==0 && value.estado_conta==0) return true;
        if (value.cnpj == "" || value.cnpj == null) value.cnpj = "<span class='clear' style='display: block;'></span>";
        else value.cnpj = "<span>" + value.cnpj + "</span>";
        if (value.telefone_1 == "" || value.telefone_1 == null) value.telefone_1 = "<span class='clear' style='display: block;'></span>";
        else value.telefone_1 = "<span>" + value.telefone_1 + "</span>";
        if (value.apelido == null) empresas[i].apelido = "";
        if (filtro == 1)
            if (value.pendencia == 0) return true;
        if (filtro == 2)
            if (value.inadimplente == 0) return true;
        if (filtro == 3)
            if (value.estado_conta != 1) return true;
        if (filtro == 4)
            if (value.estado_conta != 0) return true;
        estado = "normal";
        imagem = "../img/input-mark.png";
        if (value.pendencia == 1) {
            estado = "pendente";
            imagem = "../img/exclamacao.png";
        }
        if (value.estado_conta == 0) {
            if (typeof tela_funcionario != "undefined") return true;
            estado = "inativa";
            imagem = "../img/cadeado.png";
        }
        if (value.inadimplente == 1) {
            estado = "ina";
            imagem = "../img/pare.png";
        }
        temp += "<tr class='empresa " + estado + "' data-id='" + value.id + "'>";
        temp += "<td data-nome='Estado' class='estado'><img src=" + imagem + "></td>";
        temp += "<td class='nome' data-nome='Razão Social'>" + value.razao_social.substr(0, 20) + ".</td>";
        temp += "<td class='cnpj' data-nome='CNPJ'>" + value.cnpj + "</td>";
        temp += "<td class='telefone' data-nome='Telefone'>" + value.telefone_1 + "</td>";
        if (label != "") {
            temp += "<td class='" + label + "' data-nome='" + cabecalho[4] + "' style='text-align: center; max-width: 40%;'>";
            if (value[label] == null) temp += "";
            else {
                if (label == "simples_nacional") {
                    temp += regimes[value[label]];
                } else {
                    temp += value[label];
                }
            }
            temp += "</td>";
        }
        temp += "</tr>";
    });
    $("#empresas table").append(temp + "</tbody>");
    $(".cnpj span").mask("00.000.000/0000-00").trigger('input');
    $(".telefone span").mask("(00) 0000-0000").trigger('input');
    $("td.celular_whatsapp").mask("(00) 00000-0000").trigger('input');
    $("td.cliente_desde").mask("00/00/0000").trigger('input');
}

function atualizarCabecalho() {
    temp = "<thead><tr class='cabecalho'>";
    $.each(cabecalho, function(i, value) {
        temp += "<th>" + value + "</th>";
    });
    temp += "</tr></thead>";
    //    console.log(temp);
    $("#empresas > ul table").append(temp);
    $("#empresas table th:nth-child(5)").css({
        textAlign: "center"
    })
}

function ValidarData() {
    var aAr = typeof(arguments[0]) == "string" ? arguments[0].split("/") : arguments,
        lDay = parseInt(aAr[0]),
        lMon = parseInt(aAr[1]),
        lYear = parseInt(aAr[2]),
        BiY = (lYear % 4 == 0 && lYear % 100 != 0) || lYear % 400 == 0,
        MT = [1, BiY ? -1 : -2, 1, 0, 1, 0, 1, 1, 0, 1, 0, 1];
    return lMon <= 12 && lMon > 0 && lDay <= MT[lMon - 1] + 30 && lDay > 0;
}

function validarCPF(cpf) {
    if (cpf == "") return true;
    var numeros, digitos, soma, i, resultado, digitos_iguais;
    digitos_iguais = 1;
    if (cpf.length < 11) return false;
    for (i = 0; i < cpf.length - 1; i++)
        if (cpf.charAt(i) != cpf.charAt(i + 1)) {
            digitos_iguais = 0;
            break;
        }
    if (!digitos_iguais) {
        numeros = cpf.substring(0, 9);
        digitos = cpf.substring(9);
        soma = 0;
        for (i = 10; i > 1; i--) soma += numeros.charAt(10 - i) * i;
        resultado = soma % 11 < 2 ? 0 : 11 - soma % 11;
        if (resultado != digitos.charAt(0)) return false;
        numeros = cpf.substring(0, 10);
        soma = 0;
        for (i = 11; i > 1; i--) soma += numeros.charAt(11 - i) * i;
        resultado = soma % 11 < 2 ? 0 : 11 - soma % 11;
        if (resultado != digitos.charAt(1)) return false;
        return true;
    } else return false;
}

function validarCNPJ(cnpj) {
    cnpj = cnpj.replace(/[^\d]+/g, '');
    if (cnpj == '') return false;
    if (cnpj.length != 14) return false;
    // Elimina CNPJs invalidos conhecidos
    if (cnpj == "00000000000000" || cnpj == "11111111111111" || cnpj == "22222222222222" || cnpj == "33333333333333" || cnpj == "44444444444444" || cnpj == "55555555555555" || cnpj == "66666666666666" || cnpj == "77777777777777" || cnpj == "88888888888888" || cnpj == "99999999999999") return false;
    // Valida DVs
    tamanho = cnpj.length - 2
    numeros = cnpj.substring(0, tamanho);
    digitos = cnpj.substring(tamanho);
    soma = 0;
    pos = tamanho - 7;
    for (i = tamanho; i >= 1; i--) {
        soma += numeros.charAt(tamanho - i) * pos--;
        if (pos < 2) pos = 9;
    }
    resultado = soma % 11 < 2 ? 0 : 11 - soma % 11;
    if (resultado != digitos.charAt(0)) return false;
    tamanho = tamanho + 1;
    numeros = cnpj.substring(0, tamanho);
    soma = 0;
    pos = tamanho - 7;
    for (i = tamanho; i >= 1; i--) {
        soma += numeros.charAt(tamanho - i) * pos--;
        if (pos < 2) pos = 9;
    }
    resultado = soma % 11 < 2 ? 0 : 11 - soma % 11;
    if (resultado != digitos.charAt(1)) return false;
    return true;
}

function abrirTela(t) {
    if (t == 1) {
        $("#avancar").text("Avançar");
        $("#voltar").css({
            visibility: "hidden"
        });
        $("#cliente").show();
        $("#empresa").hide();
        $("#outros").hide();
    } else if (tela == 2) {
        $("#avancar").text("Avançar");
        $("#voltar").css({
            visibility: "visible"
        });
        $("#cliente").hide();
        $("#empresa").show();
        $("#outros").hide();
    } else {
        $("#avancar").text("Criar");
        $("#voltar").css({
            visibility: "visible"
        });
        $("#cliente").hide();
        $("#empresa").hide();
        $("#outros").show();
    }
}

function pegarMes(i) {
    meses = ["Janeiro", "Fevereiro", "Marco", "Abril", "Maio", "Junho", "Julho", "Agosto", "Setembro", "Outubro", "Novembro", "Dezembro"];
    return meses[i - 1];
}

function validarInputs(form) {
    teste = true;
    form.find("*:required").each(function(i, elem) {
        if (!this.checkValidity()) {
            teste = false;
            $(this).focus();
            chamarPopupInfo("Campo Obrigatório");
            return;
        }
    });
    return teste;
}

function ordenarEmpresas(por) {
    empresas.sort(function(a, b) {
        if (por == "cliente_desde") return b['time'] - a['time'];
        return b[por] - a[por];
    });
}

// AJAX
$("#nova-empresa form").submit(function(e) {
    e.preventDefault();
    data = $(this).serialize();
    if ($("[name=senha_empresa]").val() != $("#repetir_senha").val()) {
        chamarPopupInfo("Repita a senha corretamente!");
        $("#repetir_senha").focus();
        return;
    }
    if (!validarCPF($("[name=cpf]").cleanVal())) {
        chamarPopupInfo("CPF inválido!");
        $("[name=cpf]").focus();
        return;
    }
    if (!validarCPF($("[name=cpf_2]").cleanVal())) {
        chamarPopupInfo("CPF inválido!");
        $("[name=cpf_2]").focus();
        return;
    }
    if (!validarCPF($("[name=cpf_3]").cleanVal())) {
        chamarPopupInfo("CPF inválido!");
        $("[name=cpf_3]").focus();
        return;
    }
    if (!validarCPF($("[name=cpf_4]").cleanVal())) {
        chamarPopupInfo("CPF inválido!");
        $("[name=cpf_4]").focus();
        return;
    }
    if ($("[name=cnpj]").val().length > 0 && !validarCNPJ($("[name=cnpj]").cleanVal())) {
        chamarPopupInfo("CNPJ inválido!");
        $("[name=cnpj]").focus();
        return;
    }
    if (!ValidarData($("[name=data_abertura]").val())) {
        chamarPopupInfo("Data de abertura inválida!");
        $("[name=data_abertura]").focus();
        return;
    }
    if ($("[name=validade_cd]").val().length > 0 && !ValidarData($("[name=validade_cd]").val())) {
        chamarPopupInfo("Data de vencimento do certificado digital inválida!");
        $("[name=validade_cd]").focus();
        return;
    }
    if ($("[name=cliente_desde]").val().length > 0 && !ValidarData($("[name=cliente_desde]").val())) {
        chamarPopupInfo("Digíte uma data válida!");
        $("[name=cliente_desde]").focus();
        return;
    }
    if ($("[name=tipo_alvara_1]").val().length > 0 && !ValidarData($("[name=validade_alvara_1]").cleanVal())) {
        chamarPopupInfo("Digíte uma data válida!");
        $("[name=validade_alvara_1]").focus();
        return;
    }
    if ($("[name=tipo_alvara_2]").val().length > 0 && !ValidarData($("[name=validade_alvara_2]").cleanVal())) {
        chamarPopupInfo("Digíte uma data válida!");
        $("[name=validade_alvara_2]").focus();
        return;
    }
    $("#nova-empresa button").attr("disabled", true);
    $.ajax({
        url: "../php/contabilidade/manterEmpresa.php",
        type: "post",
        data: data + "&funcao=nova",
        success: function(result) {
            result = JSON.parse(result);
            if (result.estado == 1) {
                chamarPopupConf(result.mensagem);
                empresas.push(result.empresa);
                atualizarEmpresas2();
                $("#nova-empresa > div > img").click();
                $("#nova-empresa button").attr("disabled", false);
                $("#nova-empresa form")[0].reset();
                codigo_maior = Number($("[name[codigo_cliente]]").val()) + 1;
                $("[name[codigo_cliente]]").val(codigo_maior);
            } else if (result.estado == 2) {
                chamarPopupInfo(result.mensagem);
                $("#nova-empresa button").attr("disabled", false);
            } else {
                console.log(result);
                chamarPopupErro("Houve algum erro, atualize a página!");
                $("#nova-empresa button").attr("disabled", false);
            }
        },
        error: function(result) {
            console.log(result);
            chamarPopupErro("Houve um erro, tente atualizar a página!");
            $("#nova-empresa button").attr("disabled", false);
        }
    });
});

function validarInputs() {
    teste = true;
    // console.log($("#nova-empresa form *:required"));
    $("#nova-empresa form *:required").each(function() {
        teste = $(this)[0].checkValidity();
        // console.log(this);
        if (!teste) {
            $(this).focus();
            $(this)[0].reportValidity();
            //            chamarPopupInfo("Campo obrigatorio, "+$(this).attr("placeholder")+"!")
            return false;
        }
    });
    return teste;
}