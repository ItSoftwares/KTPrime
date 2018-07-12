var anos = {};
var id_hide = [];
var tempId = 0;
var periodo = "0-0";
var pesquisa = "";
var filtro = 0;
var acesso = [0, 0, 0, 0];
var ocupado = false;
var ultimoPeriodo = false;
var penultimoPeriodo = false;
var mostrando = [];

$(document).ready(function() {
    temp = {};
    $.each(servicos, function(i, value) {
         temp[value.id] = value;
    });
    servicos = temp;
    // console.log(historico_pagamento);
    temp = {};
    $.each(historico_pagamento, function(i, value) {
         if (!(value.id_historico in temp)) {
             temp[value.id_historico] = [];
         }
        
         temp[value.id_historico].push(value.id_pagamento);
    });
    historico_pagamento = temp;
    atualizarFinanceiro();
});

function pegarMes(i) { 
    meses = ["Janeiro", "Fevereiro", "Marco", "Abril", "Maio", "Junho", "Julho", "Agosto", "Setembro", "Outubro", "Novembro", "Dezembro"];
    
    return meses[i-1];
}

function colocarZero(numero) {
    if (numero<10) return "0"+numero;
    else return numero
}

function atualizarFinanceiro(filtro) {
    filtro = filtro || 0;
    
    $("#valores tr").remove();
    total_periodo = 0;
    total_pendente = 0;
    teste = 0;
    $.each(historico, function(i, value) {
        valor_pendente = 0;
        value.bruto = 0;
        value.valor_pago = 0;
        value.pagamentos=0;
        teste = total_pendente;
        pago = false;
        pendente = false;
        total = 0;
        temp = "";
        // LINHA DA MENSALIDADE
        valor_pendente += Number(value.mensalidade);
        value.bruto += Number(value.mensalidade);
        total += Number(value.mensalidade);
        temp += "<tr class='servicos' data-id='"+value.id_empresa+"' data-refer='"+value.id+"' data-img='1'>";
        temp += "<td></td>";
        temp += "<td colspan='4'>Mensalidade</td>";
       // temp += "<td>ESTADO_MENSALIDADE</td>";
        temp += "<td>R$ "+toMoney(value.mensalidade)+"</td>";
        temp += "<td colspan='3'></td</tr>";
        
        // LINHA DOS SERVIÇOS PRESTADOS
        $.each(servicosPrestados, function(j, refer) {
            if (refer.id_historico!=value.id) return true;
            refer.dia_vencimento = value.dia_vencimento;
            refer.mes = value.mes;
            refer.ano = value.ano;
            servicosPrestados[j] = refer;
            valor_pendente += Number(refer.valor);
            value.bruto += Number(refer.valor);;
            total += Number(refer.valor);
            temp += "<tr class='servicos' data-id='"+value.id_empresa+"' data-refer='"+value.id+"' data-img='1'>";
            temp += "<td></td>";
            temp += "<td colspan='1'>"+refer.nome_servico+"</td>";
            temp += "<td colspan='3'>"+refer.descricao+"</td>";
            temp += "<td>R$ "+toMoney(refer.valor)+"</td>"; 
            temp += "<td>"+(refer.gerado_mensal==0?"Avulso":"Mensal")+"</td>"; 
            temp += "<td colspan='2'></td</tr>";
        });
        // LINHA DO DESCONTO
        desconto = false;
        if (value.desconto>0) {
            temp += "<tr class='servicos' data-id='"+value.id_empresa+"' data-refer='"+value.id+"'>";
            temp += "<td></td>";
            temp += "<td colspan='4'>Desconto</td>";
            temp += "<td>R$ -"+toMoney(value.desconto)+"</td>";
            temp += "<td colspan='3'></td></tr>";
            total -= value.desconto;
            total_pendente -= value.desconto;
            valor_pendente -= value.desconto;
            desconto = true;
        }
        
        // LINHA DO(S) PAGAMENTOS
        if (value.id in historico_pagamento) {
            temp = imprimirPagamentos(temp, value, value.id_pagamento, total, valor_pendente);
            
            value = temp.value;
            total = temp.total;
            valor_pendente = temp.valor_pendente;
            temp = temp.temp;
        }
        
        // LINHA GERAL
        estado_pagamento = getEstadoPagamento(value, valor_pendente, pago, value.desconto);
        value.estado = estado_pagamento.tempEstado;
        valor_pendente = estado_pagamento.valor_pendente;
        pago = estado_pagamento.pago;
        value.pago = pago;
        value.valor_pago = estado_pagamento.valor_pago;
        estado_pagamento = estado_pagamento.estado_pagamento;
        
        valor_pendente = value.bruto-value.desconto-value.valor_pago;
        
        value.total = total; 
        value.total_pendente = valor_pendente;
        
        if (total==0) {
            estado_pagamento="<img src='../img/input-mark.png' class='pago'>Pago";
            value.estado=1;
            value.pago=true;
        }
        
        if (value.total_pendente<0) value.total_pendente = 0;
        
        if (Number(value.mes)+1==13) value.vencimento = colocarZero(value.dia_vencimento)+"/01/"+(Number(value.ano)+1); 
        else value.vencimento = colocarZero(value.dia_vencimento)+"/"+colocarZero(Number(value.mes)+1)+"/"+value.ano;
        geral = "<tr class='geral' data-aberto=0 data-id='"+value.id_empresa+"' data-refer='"+value.id+"' data-index='"+i+"' data-periodo='"+value.mes+"-"+value.ano+"'>";
        geral += "<td><span></span></td>";
        geral += "<td>"+value.razao_social.substr(0, 20)+".</td>";
        geral += "<td><b "+(value.estado==3?"style='color: #ff5722'":"")+">"+value.vencimento+"</b></td>"
        geral += "<td>"+pegarMes(value.mes).substr(0, 3)+" - "+value.ano+"</td>";
        geral += "<td>"+estado_pagamento+"</td>";
        geral += "<td> R$ "+toMoney(value.total_pendente)+"</td>";
        geral += "<td><button class='desconto botao' "+((value.total_pendente==0 && desconto==false)?"disabled":"")+">Desconto</button></td>";
        if (value.pago) {
            geral += "<td><button class='botao' disabled>Pago</button></td>"; 
        } else {
            geral += "<td><button title='Clique para informar ao sistema que a conta foi paga.' class='pagar botao' "+(value.total_pendente-Number(value.desconto)==0?"disabled":"")+">Pagar</button></td>";
        } 
        geral+="<td><img src='../img/fechar.png' class='excluir botao vermelho' title='Clique para excluir esse periodo!'></td></tr>";
        
        total_periodo += Number(value.bruto);
        total_periodo += Number(value.desconto);
        total_pendente += Number(valor_pendente);
        $("#valores table").append(geral+temp); 
        
        if (!anos.hasOwnProperty(value.ano)) {
            anos[value.ano] = [];
            grupo = $("<optgroup label='"+value.ano+"' data-id='"+value.ano+"'></optgroup>");
        }
        else grupo = $("#ano-mes optgroup[data-id="+value.ano+"]");

        if (anos[value.ano].indexOf(value.mes) > -1) {

        } else {
            anos[value.ano].push(value.mes);
            grupo.append("<option value='"+value.mes+"-"+value.ano+"'>"+pegarMes(value.mes).substr(0, 3)+"-"+value.ano+"</option>");
            if (!ultimoPeriodo) {
                ultimoPeriodo = value.mes+"-"+value.ano;
            }
        }

        if (!penultimoPeriodo && (value.mes+"-"+value.ano)!=ultimoPeriodo) {
            penultimoPeriodo = value.mes+"-"+value.ano;
        }
            
        $("#ano-mes").append(grupo);
        
        acesso[0]++;
        acesso[value.estado]++;
    });
    
    $("#total span:nth-child(1)").text("Pendente: R$ "+toMoney(total_pendente));
    $("#total span:nth-child(2)").text("Total: R$ "+toMoney(total_periodo));
    
    atualizarAcesso();
    // console.log(penultimoPeriodo);
    // $("#ano-mes").val(penultimoPeriodo);
    // $("#ano-mes").change();
}

function toMoney(valor) {
    valor = Number(valor);
    return valor.toFixed(2).replace(".", ",").replace(/(\d)(?=(\d{3})+\,)/g, '$1.');
}

function imprimirPagamentos(temp, value, id_pagamento, total, valor_pendente) {
    $.each(historico_pagamento[value.id], function(index, pag) {
        // if (pag==161) console.log(pagamentos[pag]);
        tipo = "Cartão de crédito";
        if (pagamentos[pag].tipo==2) tipo="Boleto Bancário";
        else if (pagamentos[pag].tipo>2) tipo="Manual";
        
        temp += "<tr class='servicos' data-id='"+value.id_empresa+"' data-refer='"+value.id+"'>";
        temp += "<td></td>";
        temp += "<td colspan='3'>Pagamento "+tipo+"</td>";
        temp += "<td></td>";
        temp += "<td>R$ -"+toMoney(pagamentos[pag].valor)+"</td>";
        
        if (tipo=="Manual") {
            temp += "<td colspan='2'></td>";
            temp += "<td data-id="+pagamentos[pag].id+"><img src='../img/lixeira-branca.png' class='excluir botao vermelho' title='Clique para excluir esse pagamento!'></td></tr>";
        } else {
            temp += "<td colspan='3'></td></tr>";
        }
        
        if (pagamentos[pag].estado==3 || pagamentos[pag].estado==4) {
            total -= pagamentos[pag].valor;
            valor_pendente -= pagamentos[pag].valor;
        }
    });
    
    return {temp: temp, value: value, total: total, valor_pendente: valor_pendente};
}

function getEstadoPagamento(value, valor_pendente, pago, desconto) {
    novaData = new Date();
    novaData = new Date(novaData.getFullYear(), novaData.getMonth(), novaData.getDate());
    estado_pagamento = getTextoPagamento(0);
    tempEstado=0;
    valor_pago = 0;
    if (value.id in historico_pagamento) {
        $.each(historico_pagamento[value.id], function(index, pag) {
            if (pagamentos[pag].estado==3 || pagamentos[pag].estado==4) {
                valor_pago += Number(pagamentos[pag].valor);
                valor_pendente -= Number(pagamentos[pag].valor);
                value.pagamentos++;
            }
        });
        
        if (valor_pendente-desconto<=0) {
            estado_pagamento = getTextoPagamento(1);
            pago=true;
            tempEstado = 1;
        } else if (value.id in historico_pagamento && historico_pagamento[value.id].length>0 && value.pagamentos>0) {
            estado_pagamento = getTextoPagamento(2);
            tempEstado = 2;
            if (new Date((value.mes==12?value.ano+1:value.ano), (value.mes==12?0:value.mes), value.dia_vencimento)<novaData) {
                estado_pagamento = getTextoPagamento(3);
                tempEstado = 3;
            }
        } else {
            if (new Date((value.mes==12?value.ano+1:value.ano), (value.mes==12?0:value.mes), value.dia_vencimento)<novaData) {
                estado_pagamento = getTextoPagamento(3);
                tempEstado = 3;
            } else if ((value.mes>novaData.getMonth()+1 && value.ano==novaData.getFullYear()) || (value.mes<novaData.getMonth()+1 && value.ano>novaData.getFullYear())) {
                estado_pagamento = getTextoPagamento(7);
                tempEstado = 2;
            } else if (value.mes!=novaData.getMonth()+1) {
                estado_pagamento = getTextoPagamento(6);
                tempEstado = 2;
            } else {
                estado_pagamento = getTextoPagamento(5);
                tempEstado = 2;
            }
        }
    } 
    else {
        if (new Date((value.mes==12?value.ano+1:value.ano), value.mes, value.dia_vencimento)<novaData) {
            estado_pagamento = getTextoPagamento(3);
            tempEstado = 3;
        } else if ((value.mes>novaData.getMonth()+1 && value.ano==novaData.getFullYear()) || (value.mes<novaData.getMonth()+1 && value.ano>novaData.getFullYear())) {
            estado_pagamento = getTextoPagamento(7);
            tempEstado = 2;
        } else if (value.mes!=novaData.getMonth()+1) {
            estado_pagamento = getTextoPagamento(6);
            tempEstado = 2;
        }  else {
            estado_pagamento = getTextoPagamento(5);
            tempEstado = 2;
        }
    }
    
    return {estado_pagamento: estado_pagamento, valor_pendente: valor_pendente, tempEstado: (value.estado==3?3:tempEstado), pago: pago, valor_pago: valor_pago};
}

function getTextoPagamento(estado) {
    if (estado==1) {
        return "<img src='../img/input-mark.png' class='pago'>Pago";
    } 
    else if (estado==2) {
        return "<img src='../img/pontos.png' class='pendente'>Parcial";
    } else if (estado==3) {
        return "<img src='../img/pare.png' class='cancelado'>Vencido";
    } else if (estado==4) {
        return "<img src='../img/exclamacao.png' class='pendente'>Aguardando Confirmação";
    } else if (estado==5) {
        return "<img src='../img/exclamacao.png' class='pendente'>Mês Atual";
    } else if (estado==6) {
        return "<img src='../img/exclamacao.png' class='pendente'>Aguardando Pagamento";
    } else if (estado==7) {
        return "<img src='../img/input-mark.png' class='pago'>Proximo mês!";
    } else {
        return "<img src='../img/exclamacao.png' class='pendente'>Pendente";
    }
}

function atualizarAcesso(tipo) {
    tipo = tipo || 0;
    
    if (tipo!=0) {
        acesso = [0, 0, 0, 0];
        $.each(historico, function(i, value) {
            value.contar=0;
            if (pesquisa.length!=0) {
                if (value.razao_social.toLowerCase().indexOf(pesquisa.toLowerCase()) != -1) {
                    value.contar+=1;
                }
            } else {
                value.contar+=1;
            }

            if (periodo!="0-0") {
                tempPeriodo = {ano: periodo.split("-")[1], mes: periodo.split("-")[0]}
                if (value.ano==tempPeriodo.ano && value.mes==tempPeriodo.mes) {
                    value.contar+=1;
                }
            } else {
                value.contar+=1;
            }
            
            if (value.contar==2) {
                acesso[0]++;
                acesso[value.estado]++;
            }
        });
    }
   // console.log(acesso);
    $("#acesso-rapido li:nth-child(1) h4").text(acesso[0]+(acesso[0]<2?" Histórco":" Históricos"));
    $("#acesso-rapido li:nth-child(2) h4").text(acesso[1]+(acesso[1]<2?" Histórco":" Históricos"));
    $("#acesso-rapido li:nth-child(3) h4").text(acesso[2]+(acesso[2]<2?" Histórco":" Históricos"));
    $("#acesso-rapido li:nth-child(4) h4").text(acesso[3]+(acesso[3]<2?" Histórco":" Históricos"));
}

function pesquisarHistorico(valor, teste) { 
    total_periodo = 0;
    total_pendente = 0;
    
    teste = teste || 0;
    
    $("tr.geral[data-aberto=1]").click();
    if (valor.length==0) {
        //mostrar todas;
       // $("tr.geral").show();
        $("tr.geral").attr("data-show", 1);
    } 
    id_hide = [];
    $.each(historico, function(i, value) {
        value.aparecer=true;
        
        if (filtro!=0) {
            if (value.estado==filtro) {
                $("tr.geral[data-refer="+value.id+"]").attr("data-show", 1);
               // $("tr.geral[data-refer="+value.id+"]").show();
                value.aparecer=true;
            } else {
                $("tr.geral[data-refer="+value.id+"]").attr("data-show", 0);
               // $("tr[data-refer="+value.id+"]").hide();
               // $("tr.geral[data-refer="+value.id+"]").attr("data-aberto", 0);
               // $("tr.geral[data-refer="+value.id+"] td:first-child span").css("transform", "rotate(0deg)");
                value.aparecer=false;
            }
        }
        
        if (valor.length!=0 && value.aparecer) {
            if (value.apelido.toLowerCase().indexOf(valor.toLowerCase()) != -1) {
                $("tr.geral[data-refer="+value.id+"]").attr("data-show", 1);
               // $("tr.geral[data-refer="+value.id+"]").show();
                value.aparecer=true;
            } else {
                $("tr.geral[data-refer="+value.id+"]").attr("data-show", 0);
               // $("tr[data-id="+value.id_empresa+"]").hide();
               // $("tr.geral[data-refer="+value.id+"]").attr("data-aberto", 0);
               // $("tr.geral[data-refer="+value.id+"] td:first-child span").css("transform", "rotate(0deg)");
                value.aparecer=false;
                
            }
        }
        
        if (periodo!="0-0" && value.aparecer) {
            if ($("tr.geral[data-refer="+value.id+"]").attr("data-periodo")==periodo) {
                $("tr.geral[data-refer="+value.id+"]").attr("data-show", 1);
               // $("tr.geral[data-refer="+value.id+"]").show();
                value.aparecer=true;
            } else {
                $("tr.geral[data-refer="+value.id+"]").attr("data-show", 0);
               // $("tr[data-refer="+value.id+"]").hide();
               // $("tr.geral[data-refer="+value.id+"]").attr("data-aberto", 0);
               // $("tr.geral[data-refer="+value.id+"] td:first-child span").css("transform", "rotate(0deg)");
                value.aparecer=false;
            }
        }
        
        if (!value.aparecer) {
            return true;
        }
        
        if (typeof value.aparecer!="undefined" && value.aparecer) {
            total_periodo += Number(value.bruto);
            total_pendente += Number(value.total_pendente);
        }
    });
    if (total_pendente<0) total_pendente=0;
    
    $("#total span:nth-child(1)").text("Pendente: R$ "+toMoney(total_pendente));
    $("#total span:nth-child(2)").text("Total: R$ "+toMoney(total_periodo));
    
    atualizarAcesso(teste);
    atualizarLinhas();
}

function atualizarLinhas() {
    $("#valores table tr.geral").each(function(i, elem) {
        if ($(this).attr("data-show")==1) {
            $(this).show();
        } else {
            $(this).hide();
        }
    });
}

$("#acesso-rapido li").click(function() {
    filtro = $(this).attr("data-filtro");
    pesquisarHistorico(pesquisa);
});

$("#pesquisar input").keyup(function(e) {
   // console.log()
    if (e.keyCode==13) {
        $("#pesquisar img").click();
    }
   // valor = $(this).val();
   // pesquisa=valor;
   // pesquisarHistorico(valor, 1);
});

$("#pesquisar img").click(function() {
    valor = $("#pesquisar input").val();
    pesquisa=valor;
    pesquisarHistorico(valor, 1);
});

$("#ano-mes").change(function() {
    valor = $(this).val();
    
   // if (valor=="0-0") {
   //     $("#valores table tr.geral").show();
   // } else {
   //     $("#valores table tr:not([data-periodo='"+valor+"'])").hide();
   //     $("#valores table tr[data-periodo='"+valor+"']").show();
   // }
    periodo = valor;
    pesquisarHistorico(pesquisa, 1);
});

$("#desconto img").click(function() {
    $("#desconto").fadeOut();
});

$("#pagar img").click(function() {
    $("#pagar").fadeOut();
});

$("#pagar select").change(function() {
    valor = $(this).val();
    
    if (valor==0) {
        $("#pagar input").val(historico[tempId].total_pendente).attr("disabled", true);
    } else {
        $("#pagar input").val("").attr("disabled", false).focus();
    }
});

$(document).on("click","tr.geral",function() {
    aberto = $(this).attr("data-aberto");
    dataId = $(this).attr("data-refer");
    
    if (aberto==0) {
        $(".servicos[data-refer="+dataId+"]").show();
        $(this).find("td span").css({transform: "rotate(90deg)"});
    } else {
        $(".servicos[data-refer="+dataId+"]").hide();
        $(this).find("td span").css({transform: "rotate(0deg)"});
    }
    
    aberto = aberto==0?1:0;
    $(this).attr("data-aberto", aberto);
});

$(document).on("click","#periodo .ano",function() {
    ano = $(this).text();
    total_periodo = 0;
    total_pendente = 0;
    console.log(ano);
    
    if (ano=="Geral") {
        $.each(historico, function(i, value) {
            if (id_hide.indexOf(value.id_empresa)!=-1) return true;
            $("tr.geral[data-refer="+value.id+"]").show();
            total_periodo+=value.total;
            total_pendente+=value.total_pendente;
        });
    } else {
        $.each(historico, function(i, value) {
            if (id_hide.indexOf(value.id_empresa)!=-1) return true;
            if (value.ano==ano) {
                $("tr.geral[data-refer="+value.id+"]").show();
                total_periodo+=value.total;
                total_pendente+=value.total_pendente;
            } else {
                $("tr[data-id="+value.id_empresa+"]").hide();
                $("tr.geral[data-refer="+value.id+"]").attr("data-aberto", 0);
                $("tr.geral[data-refer="+value.id+"] td:first-child span").css("transform", "rotate(0deg)");
            }
        });
    }
    $("#periodo .ano").removeClass("selecionado");
    $(this).addClass("selecionado");
    $("#total span:nth-child(1)").text("Pendente: R$ "+total_pendente.toFixed(2));
    $("#total span:nth-child(2)").text("Total: R$ "+total_periodo.toFixed(2));
});

$(document).on("click", ".pagar", function() {
    tempId = $(this).parent().parent().attr("data-index");
    
    $("#pagar select").val(0);
    $("#pagar input").val(historico[tempId].total_pendente).attr("disabled", true);
    
    $("#pagar").fadeIn().css({display: "flex"});
});

$("#pagar form").submit(function(e) {
    e.preventDefault();
    data = {};
    
    data.historico = historico[tempId];
    data.funcao = "pagar";
    data.tipo = $("#tipo").val();
    data.valor = $("[name=valor]").val(); 
    
    if (data.valor<1) {
        chamarPopupInfo("O valor deve ser superior a R$ 1,00 real!");
        return;
    }
    
    if (data.valor>historico[tempId].total_pendente) {
        chamarPopupInfo("O valor deve ser menor ou igual ao que está pendente: R$ "+toMoney(historico[tempId].total_pendente)+"!");
        return;
    }
   // return;
    botao = $(this);
    botao.attr("disabled", true);
    $.ajax({
        url: "../php/contabilidade/manterPagamentos.php",
        type: "post",
        data: data,
        success: function(result) {
            result = JSON.parse(result);
           // console.log(result);
           // return;
            if(result.estado==1) {
                chamarPopupConf(result.mensagem);
                setTimeout(function() {
                    location.reload();
                }, 5000)
            } else if (result.estado==2) {
                chamarPopupInfo(result.mensagem);
                botao.attr("disabled", false);
            } else {
                console.log(result)
                chamarPopupErro("Houve um erro, tente atualizar a página!");
            }
        }, 
        error: function(result) {
            console.log(result);
            chamarPopupErro("Houve um erro, tente atualizar a página!");
        }
    });
});

$(document).on("click", ".desfazer", function() {
    return;
    data = {};
    
    data.historico = historico[$(this).parent().parent().attr("data-index")];
    data.funcao = "desfazer";
    botao = $(this);
    botao.attr("disabled", true);
    $.ajax({
        url: "../php/contabilidade/manterPagamentos.php",
        type: "post",
        data: data,
        success: function(result) {
            result = JSON.parse(result);
           // console.log(result);
           // return;
            if(result.estado==1) {
                chamarPopupConf(result.mensagem);
                setTimeout(function() {
                    location.reload();
                }, 5000)
            } else if (result.estado==2) {
                chamarPopupInfo(result.mensagem);
                botao.attr("disabled", false);
            } else {
                console.log(result)
                chamarPopupErro("Houve um erro, tente atualizar a página!");
            }
        }, 
        error: function(result) {
            console.log(result);
            chamarPopupErro("Houve um erro, tente atualizar a página!");
        }
    });
});

$(document).on("click", ".desconto", function() {
    tempId = $(this).parent().parent().attr("data-index");
    
    if (historico[tempId].desconto>0) {
        $("#desconto input").val(historico[tempId].desconto);
    } else $("#desconto input").val("")
    
    $("#desconto").fadeIn().css({display: "flex"});
});

$("#desconto form").submit(function(e) {
    e.preventDefault();
    
    data = {};
    
    data.id = historico[tempId].id;
    data.funcao = "desconto";
    data.desconto = Number($("[name=desconto]").val());
    
    if (typeof data.desconto != "number") {
        chamarPopupInfo("Digite um valor válido!");
        $("[name=desconto]").focus();
        return;
    } else if (data.desconto>historico[tempId].total_pendente+historico[tempId].desconto) {
        chamarPopupInfo("Desconto maior que o valor a ser pago!");
        $("[name=desconto]").focus();
        return;
    }
    
    botao = $(this);
    botao.attr("disabled", true);
    
    $.ajax({
        url: "../php/contabilidade/manterPagamentos.php",
        type: "post",
        data: data,
        success: function(result) {
            result = JSON.parse(result);
           // console.log(result);
           // return;
            if(result.estado==1) {
                chamarPopupConf(result.mensagem);
                setTimeout(function() {
                    location.reload();
                }, 5000)
            } else if (result.estado==2) {
                chamarPopupInfo(result.mensagem);
                botao.attr("disabled", false);
            } else {
                console.log(result)
                chamarPopupErro("Houve um erro, tente atualizar a página!");
            }
        }, 
        error: function(result) {
            console.log(result);
            chamarPopupErro("Houve um erro, tente atualizar a página!");
        }
    });
});

$(document).on("click", "tr.geral td .excluir", function() {
    tempId = $(this).parent().parent().attr("data-index");
    
    data = {historico: historico[tempId]};
   // console.log(data);
    
    $.ajax({
        url: "../php/contabilidade/manterHistorico.php",
        type: "post",
        data: data,
        success: function(result) {
            result = JSON.parse(result);
            console.log(result);
           // return;
            if(result.estado==1) {
                chamarPopupConf(result.mensagem);
                setTimeout(function() {
                    location.reload();
                }, 5000)
            } else if (result.estado==2) {
                chamarPopupInfo(result.mensagem);
            } else {
                console.log(result)
                chamarPopupErro("Houve um erro, tente atualizar a página!");
            }
        }, 
        error: function(result) {
            console.log(result);
            chamarPopupErro("Houve um erro, tente atualizar a página!");
        }
    });
}); 

$(document).on("click", "tr.servicos td .excluir", function() {
    if (ocupado) return;
    ocupado = true;
    data = {};
    
    data.id = $(this).parent().attr("data-id");
    data.funcao = "desfazerNovo";
    botao = $(this);
    botao.attr("disabled", true);
    $.ajax({
        url: "../php/contabilidade/manterPagamentos.php",
        type: "post",
        data: data,
        success: function(result) {
            result = JSON.parse(result);
            
            if(result.estado==1) {
                chamarPopupConf(result.mensagem);
                setTimeout(function() {
                    location.reload();
                }, 5000)
            } else if (result.estado==2) {
                chamarPopupInfo(result.mensagem);
                botao.attr("disabled", false);
            } else {
                console.log(result)
                chamarPopupErro("Houve um erro, tente atualizar a página!");
            }
        }, 
        error: function(result) {
            console.log(result);
            chamarPopupErro("Houve um erro, tente atualizar a página!");
        }
    });
});