$(document).ready(function() {
    $("#arvore").children().remove();
    atualizarArvore(arquivos['nomes'], $("#arvore"), arquivos['caminhos'], arquivos['informacoes']);
});

function atualizarArvore(lista, elemento, caminho, informacoes) {
   // teste = pai.append("<h4 class='nome-pasta'>"+usuario.razao_social+"</h4>");
    if ($("#arvore h4").length==0) {
        if (typeof guias=="undefined") $("<h4 class='nome-pasta selecionado' data-pai=1 data-aberto=1><span>"+usuario.razao_social+"</span></h4>").appendTo(elemento);
        else $("<h4 class='nome-pasta selecionado' data-pai=1 data-aberto=1><span>Guias</span></h4>").appendTo(elemento);
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
            if (i=="anexos" || i=="guias" || i=="solicitacoes" || i=="privado") return true;
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
    pasta = $(this).next();
    
    aberto = pasta.attr("data-aberto");
    
    aberto = aberto==0?1:0;
    pasta.attr("data-aberto", aberto);
    $(this).attr("data-aberto", aberto);
});

$(document).on("click", "li.arquivo", function() {
    $("#nova").attr("disabled", true);
    selecionar(this);
    inf = JSON.parse($(this).attr("data-inf"));
    classe = "outro";
    
    if ($(this).hasClass("pdf")) classe = "pdf";
    if ($(this).hasClass("imagem")) classe = "imagem";
    
    $("#tipo").removeClass().addClass(classe);
    
    $("#download h3").text(inf.filename);
    link = inf.dirname.replace("/home/cnpjn035/public_html","")+"/"+inf.basename;
   // console.log(link)
    $("#download a").attr("href", link);
    
    $("#clique").hide();
    $("#download").show().css({display: "flex"});
});