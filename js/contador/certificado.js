var mensagem = false;
var index = 0;
var ordem = 0;

$(document).ready(function() {
    $.each(certificados, function(i, value) {
        if (value.validade_cd<0 || value.validade_cd==null || value.validade_cd=="") value.validade_cd=999999999999999;    
    });
    
    certificados.sort(function(a, b) {
        return a.validade_cd - b.validade_cd;
    });
    
    atualizarCertificados();
    
    String.prototype.replaceAll = String.prototype.replaceAll || function(needle, replacement) {
        return this.split(needle).join(replacement);
    }; 
});

$(document).on("click", "td.mensagem img:not(.disabled)", function() {
    if (!mensagem) {
        index= $(this).closest("tr").attr("data-index");
        console.log(index);
        $("#mensagem p span").text(certificados[index].razao_social.substr(0, 30));
        
        $("td.mensagem img").addClass("disabled");
        
        $("#mensagem").toggleClass("aberto");
        $("#mensagem textarea").focus();
        mensagem = true;
    } else {
        $("#mensagem textarea").focus();
    }
});

$("#mensagem .fechar").click(function() {
    $("#mensagem").toggleClass("aberto");
    $("#mensagem textarea").val("");
    $("td.mensagem img").removeClass("disabled");
    mensagem = false;
});

$("#mensagem form").submit(function(e) {
    e.preventDefault();
    
    data = {};
    data.mensagem = $("#mensagem textarea").val().replaceAll("\n", "<br>");
    data.nome = certificados[index].razao_social;
    data.email = certificados[index].email;
    data.funcao = "mensagem";
    
    console.log(data);
    
    if (data.mensagem.length==0) {
        chamarPopupInfo("Digite algo antes de enviar!");
        $("#mensagem textarea").focus();
        return
    }
    $("#mensagem button").attr("disabled", true);
    $("#mensagem .carregar").fadeIn();
    
    $.ajax({
        url: "../php/contabilidade/manterEmpresa.php",
        type: "post",
        data: data,
        success: function(result) {
            result = JSON.parse(result);
            
            console.log(result);
            
            if(result.estado==1) {
                chamarPopupConf(result.mensagem);
                
                $("#mensagem .fechar").click();
                $("#mensagem form")[0].reset();
            } else {
                chamarPopupErro(result.mensagem);
            }
            
            $("#mensagem button").attr("disabled", false);
            $("#mensagem .carregar").fadeOut();
        }, 
        error: function(result) {
            console.log(result);
            chamarPopupErro("Houve um erro, tente atualizar a página!");
        }
    });
});

function toDate(time) {
    data = new Date(time*1000);
    
    temp = "";
    
    if (time==null || time=="") {
        temp = "__/__/__";
    } else {
        temp =colocarZero(data.getDate())+"/"+colocarZero(data.getMonth()+1)+"/"+data.getFullYear();
    }
    
    return temp;
}

function colocarZero(numero) {
    if (numero<10) {
        numero = "0"+numero;
    }
    
    return numero;
}

function atualizarCertificados(filtro) {
    filtro = filtro || 0;
    
    $("#certificados table tr:not(:first-child)").remove();
    
    $.each(certificados, function(i, value) {
        $("#certificados table").append("<tr class='spacer'><td></td></tr>");
        exclamacao = "";
        if (new Date().getTime()/1000>value.validade_cd) exclamacao = "<img src='../img/exclamacao.png' title='Certificado Digital Vencido'>";
        else exclamacao = "Ok";
        
        data = toDate(value.validade_cd);
        
        if (value.validade_cd==999999999999999) data="__/__/__"; 
        
        temp = "<tr data-index='"+i+"'>";
        temp += "<td data-nome='Empresa'><a href='/empresaResumo/"+value.id+"'>"+value.razao_social.substr(0, 25)+"<img src='../img/link.png'></a></td>";
        temp += "<td data-nome='Tipo'>"+value.tipo_cd+"</td>";
        temp += "<td data-nome='Validade'>"+data+"</td>";
        temp += "<td data-nome='Estado' class='exclamacao center'>"+exclamacao+"</td>";
        temp += "<td data-nome='Mensagem' class='mensagem center'><img src='../img/mensagem.png' class='botao'></td>";
        
        $("#certificados table").append(temp);
    });
}