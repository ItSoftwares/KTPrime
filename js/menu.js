var config = 0;
var telaConfig = 0;
var menu_lateral=0;
var trocar_senha=0;

$(document).ready(function() {
    $("#menu nav h3").each(function(i, elem) {
        teste = $(this).attr("data-aberto");
        ul = $(this).next();
        if (teste==1) {
            $(this).find("span").css("transform", "rotate(0)");
            ul.hide();
        } else {
            ul.show();
            $(this).find("span").css("transform", "rotate(90deg)");
        }
    });
    
    if (typeof usuario!="undefined") {
        if (typeof usuario.senha_temporaria!="undefined") {
            if (usuario.trocar_senha==1) {
                $("#configuracoes li:first-child").click();
                chamarPopupInfo("Troque sua senha");
                $("#tela-configuracoes [name=senha]").focus();
                trocar_senha=1;
            }
        }
    }
});

$("#menu footer img:last-child").click(function() {
    $("#configuracoes").fadeToggle();
    
    config = config == 0?1:0;
});

$("#configuracoes li:first-child, #tela-configuracoes img").click(function() {
    if (trocar_senha==1) {
        chamarPopupInfo("Troque sua senha");
        $("#tela-configuracoes [name=senha]").focus();
        return;
    }
    if (telaConfig==0) {
        $("#tela-configuracoes").fadeIn().css({display: "flex"});
    } else {
        $("#tela-configuracoes").fadeOut();
    }
    
    $("[name=senha]").val("");
    $("[name=repetir_senha]").val("");
    
    telaConfig = telaConfig==0?1:0;
    $("#configuracoes").fadeOut();
});

$("#menu nav h3").click(function() {
    ul = $(this).next();
    ul.slideToggle();
    teste = $(this).attr("data-aberto");
    if (teste==0) {
        $(this).find("span").css("transform", "rotate(0)");
    } else {
        $("#menu nav h3[data-aberto='0'] + ul").slideToggle()
        $("#menu nav h3[data-aberto='0']").attr("data-aberto", 1).find("span").css("transform", "rotate(0deg)");
        $(this).find("span").css("transform", "rotate(90deg)");
    }
    
    $(this).attr("data-aberto", teste==0?1:0);
});

$(document).on("click", "#acesso-rapido li", function() {
    if ($("#acesso-rapido li.selecionado").length==0 || $(this).hasClass("selecionado")) return;
    $("#acesso-rapido li").removeClass();
    $(this).addClass("selecionado");
});

$("#tela-configuracoes form").submit(function(e) {
    e.preventDefault();
    
    data = $(this).serialize();
    senha = $('[name=senha]').val()
    repetir_senha = $('[name=repetir_senha]').val()
    if (senha.length>0) {
        if (senha!=repetir_senha) {
            chamarPopupErro("Repita a senha corretamente");
            return;
        }
    }
    
    $.ajax({
        url: "../php/contabilidade/atualizarContador.php",
        type: "post",
        data: data,
        success: function(result) {
            result = JSON.parse(result);
            trocar_senha=0;
            chamarPopupConf(result.mensagem);
            usuario = result.contador;
            $("#tela-configuracoes img").click();
            $("#menu footer span").text(usuario.nome);
        }, 
        error: function(result) {
            console.log(result);
            chamarPopupErro("Houve um erro, tente atualizar a página!");
        }
    })
});

$("#sair").click(function() {
    location.href = "/php/sair.php";
});

$("#botao-menu").click(function() {
    if (menu_lateral==0) {
        $("#menu").addClass("aberto");
    } else {
        $("#menu").removeClass("aberto");
    }
    
    menu_lateral = menu_lateral==0?1:0;
});

$(document).bind("copy", function(event) {
    texto = window.getSelection().toString();
    
    if (texto.length==18) {
        texto = texto.replace(".", "");
        texto = texto.replace(".", "");
        texto = texto.replace("/", "");
        texto = texto.replace("-", "");
        
        console.log(texto);
        
        clipboardData = event.clipboardData || window.clipboardData || event.originalEvent.clipboardData;
        
        try {
            clipboardData.setData('text/plain', texto);
        } catch (e) {
            console.log("chrome"+event)
        }
        
        try {
            clipboardData.setData('text/plain', texto);
        } catch (e) {
            console.log("firefox"+event)
        }
        
        event.preventDefault();
    }
}); 