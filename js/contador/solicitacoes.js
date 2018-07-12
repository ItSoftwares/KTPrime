var pendentes = 0;
var respondido = 0;
var filtro_acesso = 0;
var nome_pesquisa = 0;
var data_pesquisa = 0;
var id_responder;

$(document).ready(function() {
    atualizarServicos();
    atualizarSolicitacoes();
    
    $("#data input").mask("00/00/0000");
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

function atualizarSolicitacoes(filtro, nome, data) {
    filtro = filtro || 0;
    nome = nome || 0;
    data = data || 0;
    
    pendentes = 0;
    respondido = 0;
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
        
        if (nome!=0) {
            if (value.empresa.toLowerCase().indexOf(nome.toLowerCase()) == -1) {
                return true;
            }
        }
        
        if (data!=0) {
            data=new Date(data);  
            data2 = new Date(value.data*1000);
            
            if (data.getDate()!=data2.getDate() || data.getMonth()!=data2.getMonth() || data.getFullYear()!=data2.getFullYear()) return true;
        }
        
        $("table tbody").append("<tr class='spacer'><td></td></tr>");
        
        temp = "";
        
        img = value.respondido==1?"'../img/input-mark.png'":"'../img/exclamacao.png' class='pendente'";
       // serv = value.nome_servico;
        
        temp += "<tr data-id='"+i+"'>";
        temp += "<td data-nome='Estado'><img src="+img+"></td>";
        if (typeof tela_funcionario!="undefined") temp += "<td data-nome='Empresa'><a href='/funcionarioEmpresaSolicitacoes/"+value.id_empresa+"'>"+value.empresa.substring(0,18)+"<img src='../img/link.png'></a></td>";
        else temp += "<td data-nome='Empresa'><a href='/empresaSolicitacoes/"+value.id_empresa+"'>"+value.empresa.substring(0,18)+"<img src='../img/link.png'></a></td>";
        temp += "<td data-nome='Data'>"+toDate(value.data)+"</td>";
        temp += "<td data-nome='Descricao' class='descricao'>"+value.descricao+"</td>";
        temp += "<td data-nome='Anexo'>"+(value.tem_anexo==1?"<a href='../servidor/empresa/"+value.id_empresa+"/solicitacoes/"+(new Date(value.data * 1000).getFullYear())+"/"+value.id+"."+value.extensao+"' download>":"")+"<img src='../img/anexo.png' "+(value.tem_anexo==0?"class='sem-anexo anexo botao disabled' title='Não possui anexos'":"title='Possui Anexos'")+" class='anexo botao'>"+(value.tem_anexo==1?"</a>":"")+"</td>";
        temp += "<td data-nome='Responder'>"+(value.respondido==0?"<img src='../img/responder.png' class='responder botao' title='Responder solicitação'>":"<img src='../img/responder.png' class='responder respondido botao disabled' title='Solicitação respondida'>")+"</td>"
        temp += "</tr>";
        
        $("table tbody").append(temp);
    });
    
    atualizarAcessoRapido();
    if (typeof tela_funcionario!="undefined") $("table tr td button").css({opacity: .3, cursor: "default"});
}

function atualizarServicos() {
    newArray = {};
    $.each(servicos, function(i, value) {
        temp = "";
        if (value.id==1) return true;
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

$("#pesquisa #nome input").keyup(function() {
    nome_pesquisa = $(this).val();
    
    if (nome_pesquisa.length>0) {
        if (data_pesquisa!=0) {
            atualizarSolicitacoes(filtro_acesso, nome_pesquisa, data_pesquisa);
        } else {
            atualizarSolicitacoes(filtro_acesso, nome_pesquisa);
        }
    } else {
        nome_pesquisa = 0;
        if (data_pesquisa!=0) {
            atualizarSolicitacoes(filtro_acesso, 0, data_pesquisa);
        } else {
            atualizarSolicitacoes(filtro_acesso);
        }
    }
});

$("#pesquisa #data input").keyup(function() {
    data_pesquisa = $(this).val();
    
    if (data_pesquisa.length!=10) {
        data_pesquisa=0;
    } else {
        data_pesquisa = data_pesquisa.split("/");
        data_pesquisa = data_pesquisa[1]+"/"+data_pesquisa[0]+"/"+data_pesquisa[2];
    }
    
    if (data_pesquisa.length>0 && data_pesquisa!=0) {
        if (nome_pesquisa!=0) {
            atualizarSolicitacoes(filtro_acesso, nome_pesquisa, data_pesquisa);
        } else {
            atualizarSolicitacoes(filtro_acesso, 0, data_pesquisa);
        }
    } else {
        data_pesquisa = 0;
        if (nome_pesquisa!=0) {
            atualizarSolicitacoes(filtro_acesso, nome_pesquisa);
        } else {
            atualizarSolicitacoes(filtro_acesso);
        }
    }
});

$("#acesso-rapido li:nth-child(1)").click(function() {
    atualizarSolicitacoes();
    filtro_acesso=0;
});

$("#acesso-rapido li:nth-child(2)").click(function() {
    atualizarSolicitacoes(1);
    filtro_acesso=1;
});

$("#acesso-rapido li:nth-child(3)").click(function() {
    atualizarSolicitacoes(2);
    filtro_acesso=2;
});

$(document).on("click","#solicitacoes table tr td button",function() {
    if (typeof tela_funcionario!="undefined") return;
    tempId = $(this).parent().parent().attr("data-id");
    $.ajax({
        url: "../php/empresa/manterSolicitacao.php",
        type: "post",
        data: "respondido=1&funcao=realizar&id="+solicitacoes[tempId].id,
        success: function(result) {
            result = JSON.parse(result);

            console.log(result);

            if (result.estado==1) {
                chamarPopupConf(result.mensagem);
                solicitacoes[tempId].respondido=1;
                atualizarSolicitacoes();
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

$("#responder img").click(function() {
    $("#responder").fadeOut();
});

$(document).on("click",".responder:not(.respondido)",function() {
    id_responder = $(this).parent().parent().attr("data-id");
    $("#responder p.descricao").text(solicitacoes[id_responder].descricao);
    $("#responder").fadeIn().css({display: "flex"});
});

$("#responder form").submit(function(e) {
    e.preventDefault();
    data.resposta=$("#responder textarea").val();
    data.respondido=1;
    data.funcao="responder";
    data.id=solicitacoes[id_responder].id;
    
    $.ajax({
        url: "../php/empresa/manterSolicitacao.php",
        type: "post",
        data: data,
        success: function(result) {
            result = JSON.parse(result);

            console.log(result);

            if (result.estado==1) {
                chamarPopupConf(result.mensagem);
                solicitacoes[id_responder].respondido=1;
                solicitacoes[id_responder].resposta=$("#responder textarea").val();
                atualizarSolicitacoes();
                
                $("#responder form")[0].reset();
                $("#responder").fadeOut();
            } else {
                chamarPopupErro(result.mensagem);
            }
        }, 
        error: function(result) {
            console.log(result);
            chamarPopupErro("Houve um erro, tente atualizar a página!");
        }
    });
})