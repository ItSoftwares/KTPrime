var tipo, faturamento, mensalidade=0;
var valores = [[150,250,300,450,600],[300,500,600,900,1200],[170,300,350,450,600],[340,600,750,900,1200],[220,350,400,500,650],[390,650,800,950,1250]];
var loop;

$(document).ready(function() {
    $(document).scroll();
    
    if (typeof page != "undefined") slider(0);
});

function slider(left) {
//    console.log(left)
    loop = setTimeout(function() {
        largura = $("#clientes .container a").length;
        largura = largura*220 - $("#clientes > div").width()+30;
        
        if (left<largura*-1) {
            left=0;
            $("#clientes .container").animate({left: left+"px"}, 300, function() {
                slider(left);
            });
        } else {
            left -= 1;
            $("#clientes .container").css("left", left+"px");
            slider(left);
        }
    }, 50);
}

$("#simulador > img").click(function() {
    $("#simulador").fadeOut();
});

$("h2.final, #simular").click(function() {
    $("#simulador").fadeIn().css({display: "flex"});
});

$("[name=tipo],[name=faturamento]").change(function() {
    console.log("teste");
    
    tipo = $("[name=tipo]").val();
    faturamento = $("[name=faturamento]").val();
    
    if (tipo!="a" && faturamento!="a") {
        mensalidade = valores[tipo][faturamento];
        
        $("#resultados a").show();
        $("#resultados").css({justifyContent: "flex-end"});
    } else {
        mensalidade=0;
    }
    $(".result p").html(mensalidade+",00<span>/R$</span>");
});

$("#menu-botao").click(function() {
    $("header nav").toggleClass("aberto");
});

$(document).scroll(function() {
    if (typeof page == "undefined") return;
    invert=false;
    if (isScrolledIntoView($("#inicio")[0]) || $(window).scrollTop()<=61) {
        invert = true;
        $("#lateral li").removeClass("selecionado");
        $("#lateral li:nth-child(1)").addClass("selecionado");
        
    } else if (isScrolledIntoView($("#servicos")[0])) {
        invert = false;
        $("#lateral li").removeClass("selecionado");
        $("#lateral li:nth-child(2)").addClass("selecionado");
        
    } else if (isScrolledIntoView($("#sobre")[0])) {
        invert = false;
        $("#lateral li").removeClass("selecionado");
        $("#lateral li:nth-child(3)").addClass("selecionado");
        
    } else if (isScrolledIntoView($("#clientes")[0])) {
        invert = false;
        $("#lateral li").removeClass("selecionado");
        $("#lateral li:nth-child(4)").addClass("selecionado");
        
    } else if (isScrolledIntoView($("#mais")[0])) {
        invert = false;
        $("#lateral li").removeClass("selecionado");
        $("#lateral li:nth-child(5)").addClass("selecionado");
    }
    
    if (invert) 
        $("#lateral").css({filter: "invert(1)"});
    else
        $("#lateral").css({filter: "invert(0)"});
    
    console.log(invert)
});

$("#lateral li").click(function() {
    id = $(this).attr("data-id");
    $("body, html").animate({scrollTop: $("#"+id).offset().top+"px"});
});

function isScrolledIntoView(elem){
    var docViewTop = $(window).scrollTop();
    var docViewBottom = docViewTop + $(window).height();

    var elemTop = $(elem).offset().top;
    var elemBottom = elemTop + $(elem).height();

    return ((elemBottom <= docViewBottom) && (elemTop >= docViewTop));
}