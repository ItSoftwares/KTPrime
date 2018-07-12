var pendentes = 0;
var respondido = 0;

$(document).ready(function() {
    atualizarServicos();
    atualizarSolicitacoes();
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

function atualizarSolicitacoes(filtro) {
    filtro = filtro || 0;
    
    pendentes = 0;
    $("table tr:not(:first-child)").remove();
    $.each(solicitacoes, function(i, value) {
        if (value.respondido==0) pendentes++;
        if (value.respondido==1) respondido++;
        
        if (filtro>0) {
            if (filtro==1) {
                if (value.respondido==1) return true;
            }
            
            if (filtro==2) {
                if (value.respondido==0) return true;
            }
        }
        
        $("table tbody").append("<tr class='spacer'><td></td></tr>");
        
        temp = "";
        
        img = value.respondido==1?"'../img/input-mark.png'":"'../img/exclamacao.png' class='pendente'";
//        serv = value.nome_servico;
        
        temp += "<tr data-id='"+i+"'>";
        temp += "<td><img src="+img+"></td>";
        temp += "<td>"+value.descricao+"</td>";
        temp += "<td>"+toDate(value.data)+"</td>";
//        temp += "<td>"+serv+"</td>";
        temp += "<td>"+(value.respondido==1?"<img src='../img/ver.png' class='ver'>":"<img src='../img/ver.png' class='ver nao'>")+"</td>"
        temp += "<td data-nome='Anexo' class='anexo'>"+(value.tem_anexo==1?"<a href='../servidor/empresa/"+usuario.id+"/solicitacoes/"+(new Date(value.data * 1000).getFullYear())+"/"+value.id+"."+value.extensao+"' download>":"")+"<img src='../img/anexo.png' "+(value.tem_anexo==0?"class='sem-anexo' title='Não possui anexos'":"title='Possui Anexos'")+">"+(value.tem_anexo==1?"</a>":"")+"</td>";
        temp += "<td><img src='../img/lixeira-branca.png' class='excluir "+(value.respondido==1?"ok":"")+"'></td>";
        temp += "</tr>";
        
        $("table tbody").append(temp);
        
        
    });
    
    atualizarAcessoRapido();
}

function atualizarServicos() {
    newArray = {};
    $.each(servicos, function(i, value) {
        temp = "";
//        if (value.id==1) return true;
        temp = "<option value='"+value.id+"'>"+value.nome+"</option>";
        
        $("#adicionar select").append(temp);
        newArray[value.id] = value;
    });
    servicos = newArray;
    $("#adicionar select").append("<option value='-1'>Outro</option>");
}

function atualizarAcessoRapido() {
    qtd = solicitacoes.length;
    
    $("#acesso-rapido li:first-child h4").text(qtd+" Solicitações")
    $("#acesso-rapido li:nth-child(2) h4").text(pendentes+" Solicitações")
    $("#acesso-rapido li:last-child h4").text((respondido)+" Solicitações")
}

$("#acesso-rapido li:nth-child(1)").click(function() {
    atualizarSolicitacoes();
});

$("#acesso-rapido li:nth-child(2)").click(function() {
    atualizarSolicitacoes(1);
});

$("#acesso-rapido li:nth-child(3)").click(function() {
    atualizarSolicitacoes(2);
});

$("#adicionar form").submit(function(e) {
    e.preventDefault();
    
    data = new FormData();
    data.append("funcao", "novo");
//    if ($("[name=id_servico]").val()==-1) data.append("nome_servico", "Outro");
//    else data.append("nome_servico", servicos[$("[name=id_servico]").val()].nome);
    data.append("descricao", $("[name=descricao]").val());
    data.append("anexo", $("[name=image-upload]")[0].files[0]);
    
//    if ($("[name=id_servico]").val()==0) {
//        chamarPopupInfo("Selecione um serviço!");
//        $("[name=id_servico]").focus();
//        return;
//    }
     
    $.ajax({
        url: "../php/empresa/manterSolicitacao.php",
        type: "post",
        data: data,
        success: function(result) {
            result = JSON.parse(result);

            if (result.estado==1) {
                chamarPopupConf(result.mensagem);
                solicitacoes.push(result.solicitacao);
                atualizarSolicitacoes();
                $("#adicionar form")[0].reset();
            } else if (result.estado==2) {
                chamarPopupInfo(result.mensagem);
            } else {
                chamarPopupErro(result);
            }
        }, 
        error: function(result) {
            console.log(result);
            chamarPopupErro("Houve um erro, tente atualizar a página!");
        },
        processData: false,
        contentType: false
    });
});

$(document).on("click",".excluir:not(.ok)", function() {
    id_excluir=$(this).parent().parent().attr("data-id");
    
    $.ajax({
        url: "../php/empresa/manterSolicitacao.php",
        type: "post",
        data: "funcao=excluir&id_excluir="+id_excluir+"&id="+solicitacoes[id_excluir].id,
        success: function(result) {
            result = JSON.parse(result);

            if (result.estado==1) {
                chamarPopupConf(result.mensagem)
                solicitacoes.splice(id_excluir, 1);
                atualizarSolicitacoes();
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

$("#upload").bind("drop", function(e) {
    e.preventDefault();
    e.stopPropagation();
    if ($("#upload label").hasClass("over")) $("#upload label").removeClass("over");
//    console.log(e.originalEvent.dataTransfer.files.length);
    tamanho = e.originalEvent.dataTransfer.files.length;
    if (tamanho==0) {
        console.log("Escolha um arquivo!");
        return;
    }
    
    $("#upload input[type=file]")[0].files=e.originalEvent.dataTransfer.files;
    $("#upload #nome").text($('#upload input').val().replace(/C:\\fakepath\\/i, ''));
});

$("#upload #image-upload").bind("change", function(e) {
    $("#adicionar input[type=file]")[0].files = e.target.files;
    $("#upload #nome").text($(this).val().replace(/C:\\fakepath\\/i, ''));
});

$("#adicionar #image-upload").bind("change", function(e) {
    $("#upload #nome").text($(this).val().replace(/C:\\fakepath\\/i, ''));
    $("#anexo").css({background: "rgb(22, 136, 206)"})
});

$("#upload").bind("dragover", function(e) {
    e.preventDefault();
    e.stopPropagation();
    if (!$("#upload label").hasClass("over")) $("#upload label").addClass("over");
});

$("#upload").bind("dragenter", function(e) {
    e.preventDefault();
    e.stopPropagation();
    if ($("#upload label").hasClass("over")) $("#upload label").removeClass("over");
});

$("#anexo").click(function() {
    $("#upload-imagem").fadeIn().css({display: "flex"});
    
    upload = true;
});

$("#upload-imagem > img").click(function() {
    $("#upload-imagem").fadeOut();
    
    upload = false;
});

$("#upload-imagem button").click(function() {
    $("#upload-imagem").fadeOut();
});

$(document).on("click",".ver:not(.nao)", function(){
    id_resposta = $(this).parent().parent().attr("data-id");
    resposta = solicitacoes[id_resposta].resposta;
    $("#resposta p").text(resposta)
    $("#resposta").fadeIn().css({display: "flex"});
});

$("#resposta img, #resposta button").click(function() {
    $("#resposta").fadeOut();
});