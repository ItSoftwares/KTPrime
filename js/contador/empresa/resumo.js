var editar = false;

$(document).ready(function() {
    console.log(empresa);
    
    $("#informacoes input, #informacoes textarea, #informacoes select").attr("disabled", true);
    
    for(var i=28;i>0;i--) {
        $("[name=dia_vencimento]").append("<option value='"+i+"'>"+i+"</option>");
    }
    
    
    $.each(empresa, function(i, value) {
        if (i=="validade_cd" || i=="vencimento_alvara_1" || i=="vencimento_alvara_2") {
            if (value=="" || value==null || value==1000) return true;
            $("[name="+i+"]").val(getTime(value));
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
    $("[name=cliente_desde], [name=data_abertura], [name=validade_cd]").mask("00/00/0000").trigger('input');
    
    if (empresa.estado_conta==2) {
        $("#ativar").attr("title", "Ativar Empresa!").find("img").attr("src", "../img/cadeado-destrancado.png");
    }
    
    $(".input input, .input select, .input textarea").each(function() {
        var attr = $(this).attr("required");
        if (typeof attr!="undefined") {
            $(this).parent().find("label").text($(this).parent().find("label").text()+" *");
        }
    });
});

function colocarZero(numero) {
    if (numero<10) {
        numero = "0"+numero;
    }
    
    return numero;
}

$("#editar").click(function(e) {
    e.preventDefault();
    
    if (editar==false) {
        $("#informacoes input, #informacoes textarea, #informacoes select").attr("disabled", false);
        $("#editar img").attr("src", "../img/input-mark.png");
        $("#editar").addClass("salvar").attr("title", "Salvar Alterações");
        
        $("[name=nome_cliente]").focus();
        editar=true;
        
        $("#ativar").show();
    } else {
        if (!validarInputs()) return;
        
        data = $("#informacoes form").serialize();
//        console.log(data);

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
//        console.log($("[name=cnpj]").val());
        if ($("[name=cnpj]").val().length>0 && !validarCNPJ($("[name=cnpj]").cleanVal())) {
            chamarPopupInfo("CNPJ inválido!");
            $("[name=cnpj]").focus();
            return;
        }

        if (!ValidarData($("[name=data_abertura]").val())) { 
            chamarPopupInfo("Data de abertura inválida!");
            $("[name=data_abertura]").focus();
            return;
        }

        if ($("[name=validade_cd]").val().length>0 && !ValidarData($("[name=validade_cd]").val())) {
            chamarPopupInfo("Data de vencimento do certificado digital inválida!");
            $("[name=validade_cd]").focus();
            return;
        }

        if ($("[name=cliente_desde]").val().length>0 && !ValidarData($("[name=cliente_desde]").val())) {
            chamarPopupInfo("Digíte uma data válida (Cliente desde)!");
            $("[name=cliente_desde]").focus();
            return;
        }

        if ($("[name=tipo_alvara_1]").val().length>0 && !ValidarData($("[name=vencimento_alvara_1]").val())) {
            chamarPopupInfo("Digíte uma data válida (Vencimento Alvará 1)!");
            $("[name=validade_alvara_1]").focus();
            return;
        }

        
        if ($("[name=tipo_alvara_2]").val().length>0 && !ValidarData($("[name=vencimento_alvara_2]").val())) {
            chamarPopupInfo("Digíte uma data válida (Vencimento Alvará 2)!");
            $("[name=validade_alvara_2]").focus();
            return;
        }
        $.ajax({
            url: "../php/contabilidade/manterEmpresa.php",
            type: "post",
            data: data+"&funcao=editar",
            success: function(result) {
                console.log(result);
                result = JSON.parse(result);

                if (result.estado==1) {
                    chamarPopupConf(result.mensagem);
                    
                    $.each(result.empresa, function(i, value) {
                        empresa[i]=value;
                    })
                    
                    $("#informacoes input, #informacoes textarea, #informacoes select").attr("disabled", true);
                    $("#editar img").attr("src", "../img/editar-branco.png");
                    $("#editar").removeClass("salvar").attr("title", "Editar Informações");
                    editar=false;
                    $("#ativar").hide();
                } else {
                    chamarPopupErro(result.mensagem);
                }
            }, 
            error: function(result) {
                console.log(result);
                chamarPopupErro("Houve um erro, tente atualizar a página!");
            }
        })
    }
});

$("#ativar").click(function(e) {
    data = {funcao: "bloquear", id: empresa.id, estado_conta: empresa.estado_conta==1?0:1}

    $.ajax({
        url: "../php/contabilidade/manterEmpresa.php",
        type: "post",
        data: data,
        success: function(result) {
            console.log(result);
            result = JSON.parse(result);

            if (result.estado==1) {
                chamarPopupConf(result.mensagem);

                empresa.estado_conta = data.estado_conta;
                $("#ativar").removeClass();
                if (empresa.estado_conta==0) {
                    $("#ativar img").attr("src", "../img/cadeado-destrancado.png");
                    $("#ativar").addClass("desbloquear");
                    $("#informacoes > h3 span").text("Inativa");
                } else {
                    $("#ativar img").attr("src", "../img/cadeado.png");
                    $("#ativar").addClass("bloquear");
                    $("#informacoes > h3 span").text("Ativa");
                }
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

$("#lembrar:not(.disabled)").click(function() {
    data = {funcao: "lembrar", empresa: empresa};
    
    $(this).addClass("disabled");
    $.ajax({
        url: "../php/contabilidade/manterEmpresa.php",
        type: "post",
        data: data,
        success: function(result) {
            console.log(result);
            result = JSON.parse(result);

            if (result.estado==1) {
                chamarPopupConf(result.mensagem);
                $("#lembrar").removeClass("disabled");
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

$("[name=cep]").blur(function() {
    pesquisacep($(this).cleanVal());
});

function getTime(time) {
    time = new Date(time*1000);
    
    return colocarZero(time.getDate())+"/"+colocarZero(time.getMonth()+1)+"/"+time.getFullYear();
}

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
        if(validacep.test(cep)) {
//            $("[name=estado]").attr("disabled", true);
//            $("[name=cidade]").attr("disabled", true);
//            $("[name=rua]").attr("disabled", true);
//            $("[name=bairro]").attr("disabled", true);

            //Cria um elemento javascript.
            var script = document.createElement('script');

            //Sincroniza com o callback.
            script.src = '//viacep.com.br/ws/'+ cep + '/json/?callback=meu_callback';

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

function ValidarData() {
    var aAr = typeof (arguments[0]) == "string" ? arguments[0].split("/") : arguments,
        lDay = parseInt(aAr[0]), lMon = parseInt(aAr[1]), lYear = parseInt(aAr[2]),
        BiY = (lYear % 4 == 0 && lYear % 100 != 0) || lYear % 400 == 0,
        MT = [1, BiY ? -1 : -2, 1, 0, 1, 0, 1, 1, 0, 1, 0, 1];
    return lMon <= 12 && lMon > 0 && lDay <= MT[lMon - 1] + 30 && lDay > 0;
}

function validarCPF(cpf) {
    if (cpf=="") return true;
    
    var numeros, digitos, soma, i, resultado, digitos_iguais;
    digitos_iguais = 1;
    if (cpf.length < 11)
          return false;
    for (i = 0; i < cpf.length - 1; i++)
          if (cpf.charAt(i) != cpf.charAt(i + 1))
                {
                digitos_iguais = 0;
                break;
                }
    if (!digitos_iguais)
          {
          numeros = cpf.substring(0,9);
          digitos = cpf.substring(9);
          soma = 0;
          for (i = 10; i > 1; i--)
                soma += numeros.charAt(10 - i) * i;
          resultado = soma % 11 < 2 ? 0 : 11 - soma % 11;
          if (resultado != digitos.charAt(0))
                return false;
          numeros = cpf.substring(0,10);
          soma = 0;
          for (i = 11; i > 1; i--)
                soma += numeros.charAt(11 - i) * i;
          resultado = soma % 11 < 2 ? 0 : 11 - soma % 11;
          if (resultado != digitos.charAt(1))
                return false;
          return true;
          }
    else
        return false;
  }

function validarCNPJ(cnpj) {
 
    cnpj = cnpj.replace(/[^\d]+/g,'');
 
    if(cnpj == '') return false;
     
    if (cnpj.length != 14)
        return false;
 
    // Elimina CNPJs invalidos conhecidos
    if (cnpj == "00000000000000" || 
        cnpj == "11111111111111" || 
        cnpj == "22222222222222" || 
        cnpj == "33333333333333" || 
        cnpj == "44444444444444" || 
        cnpj == "55555555555555" || 
        cnpj == "66666666666666" || 
        cnpj == "77777777777777" || 
        cnpj == "88888888888888" || 
        cnpj == "99999999999999")
        return false;
         
    // Valida DVs
    tamanho = cnpj.length - 2
    numeros = cnpj.substring(0,tamanho);
    digitos = cnpj.substring(tamanho);
    soma = 0;
    pos = tamanho - 7;
    for (i = tamanho; i >= 1; i--) {
      soma += numeros.charAt(tamanho - i) * pos--;
      if (pos < 2)
            pos = 9;
    }
    resultado = soma % 11 < 2 ? 0 : 11 - soma % 11;
    if (resultado != digitos.charAt(0))
        return false;
         
    tamanho = tamanho + 1;
    numeros = cnpj.substring(0,tamanho);
    soma = 0;
    pos = tamanho - 7;
    for (i = tamanho; i >= 1; i--) {
      soma += numeros.charAt(tamanho - i) * pos--;
      if (pos < 2)
            pos = 9;
    }
    resultado = soma % 11 < 2 ? 0 : 11 - soma % 11;
    if (resultado != digitos.charAt(1))
          return false;
           
    return true;
}

function validarInputs() {
    teste = true;
    console.log($("#nova-empresa form *:required"));
    $("#nova-empresa form *:required").each(function() {
        teste = $(this)[0].checkValidity();
        console.log(this);
        if (!teste) {
            $(this).focus();
            $(this)[0].reportValidity();
//            chamarPopupInfo("Campo obrigatorio, "+$(this).attr("placeholder")+"!")
            return false;
        }
    });
    
    return teste;
}