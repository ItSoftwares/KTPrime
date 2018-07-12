var anos = {};
var id_hide = [];
var tempId = 0;
var periodo = {ano: 0, mes: 0};
var pesquisa = "";
var filtro = 0;
var acesso = [0, 0, 0, 0];
var queMassa = 3;

$(document).ready(function() {
    temp = {};
    $.each(servicos, function(i, value) {
         temp[value.id] = value;
    });
    servicos = temp;
    
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
        teste = total_pendente;
        pago = false;
        pendente = false;
        total = 0;
        temp = "";
        // LINHA DA MENSALIDADE
        estado_pagamento = getEstadoPagamentoMensalidade(value, valor_pendente, pago, pendente);
        valor_pendente = estado_pagamento.valor_pendente;
        pago = estado_pagamento.pago;
        pendente = estado_pagamento.pendente;
        estado_pagamento = estado_pagamento.estado_pagamento;
        value.bruto += Number(value.mensalidade);
        total += Number(value.mensalidade);
        temp += "<tr class='servicos' data-id='"+value.id_empresa+"' data-refer='"+value.id+"' data-img='1'>";
        temp += "<td></td>";
        temp += "<td colspan='3'>Mensalidade</td>";
        temp += "<td>"+estado_pagamento+"</td>";
        temp += "<td>R$ "+Number(value.mensalidade).toFixed(2)+"</td>";
        temp += "<td colspan='3'></td</tr>";
        
        // LINHA DOS SERVIÇOS PRESTADOS
        $.each(servicosPrestados, function(j, refer) {
            refer.dia_vencimento = value.dia_vencimento;
            refer.mes = value.mes;
            refer.ano = value.ano;
            servicosPrestados[j] = refer;
            if (refer.id_historico!=value.id) return true;
            estado_pagamento = getEstadoPagamentoServicos(refer, valor_pendente, pago, pendente);
            valor_pendente = estado_pagamento.valor_pendente;
            pago = estado_pagamento.pago;
            pendente = estado_pagamento.pendente;
            if (estado_pagamento.estado == "Vencido") value.estado=3;
            estado_pagamento = estado_pagamento.estado_pagamento;
            value.bruto += Number(refer.valor);;
            total += Number(refer.valor);
            temp += "<tr class='servicos' data-id='"+value.id_empresa+"' data-refer='"+value.id+"' data-img='1'>";
            temp += "<td></td>";
            temp += "<td colspan='3'>"+refer.nome_servico+"</td>";
            temp += "<td>"+estado_pagamento+"</td>";
            temp += "<td>R$ "+Number(refer.valor).toFixed(2)+"</td>"; 
            temp += "<td colspan='3'></td</tr>";
        });
        // LINHA DO DESCONTO
        if (value.desconto>0) {
            temp += "<tr class='servicos' data-id='"+value.id_empresa+"' data-refer='"+value.id+"'>";
            temp += "<td></td>";
            temp += "<td colspan='3'>Desconto</td>"; 
            temp += "<td></td>";
            temp += "<td>R$ -"+Number(value.desconto).toFixed(2)+"</td>";
            temp += "<td colspan='3'></td></tr>";
            total -= value.desconto;
            total_pendente -= value.desconto;
            valor_pendente -= value.desconto;
        }
        
        // LINHA DO(S) PAGAMENTOS
        if (value.id_pagamento!=null) {
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
        
        if (value.total_pendente<0) value.total_pendente = 0;
        
        if (total==0) {
            estado_pagamento="<img src='../img/input-mark.png' class='pago'>Pago";
            value.estado=1;
        }
        
        if (value.estado==3) {
            estado_pagamento = "<img src='../img/pare.png' class='cancelado'>Vencido";
        }
        
        geral = "<tr class='geral' data-aberto=0 data-id='"+value.id_empresa+"' data-refer='"+value.id+"' data-index='"+i+"'>";
        geral += "<td><span></span></td>";
        geral += "<td>"+value.razao_social.substr(0, 20)+".</td>";
        geral += "<td><b>"+(colocarZero(value.dia_vencimento)+"/"+colocarZero(value.mes)+"/"+value.ano)+"</b></td>"
        geral += "<td>"+pegarMes(value.mes).substr(0, 3)+" - "+value.ano+"</td>";
        geral += "<td>"+estado_pagamento+"</td>";
        geral += "<td> R$ "+value.total_pendente.toFixed(2)+"</td>";
        geral += "<td><button class='desconto' "+(value.total_pendente==0?"disabled":"")+">Desconto</button></td>";
        if (pago) {
            if (pagamentos[value.id_pagamento].tipo==3 || pagamentos[value.id_pagamento].tipo==4) {
                geral += "<td><button class='desfazer'>Desfazer</button></td>";
            } else {
                geral += "<td><button disabled>Pago</button></td>"; 
            }
        } else {
            geral += "<td><button title='Clique para informar ao sistema que a conta foi paga.' class='pagar' "+(value.total_pendente-Number(value.desconto)==0?"disabled":"")+">Pagar</button></td>";
        } 
        geral+="<td><img src='../img/fechar.png' class='excluir' title='Clique para excluir esse periodo!'></td></tr>";
         
        total_periodo += Number(value.bruto);
        total_periodo += Number(value.desconto);
        total_pendente += Number(valor_pendente);
        $("#valores table").append(geral+temp); 
        
        if (total==0) {
            $("tr.servicos[data-refer='"+value.id+"'][data-img=1] td:nth-child(3)").html("<img src='../img/input-mark.png' class='pago'>Pago");
        }
        
        if (!anos.hasOwnProperty(value.ano)) {
            anos[value.ano] = [];
            grupo = $("<optgroup label='"+value.ano+"' data-id='"+value.ano+"'></optgroup>");
        }
        else grupo = $("#ano-mes optgroup[data-id="+value.ano+"]");

        if (anos[value.ano].indexOf(value.mes) > -1) {
            
        } else {
            anos[value.ano].push(value.mes);
            grupo.append("<option value='"+value.mes+"-"+value.ano+"'>"+pegarMes(value.mes).substr(0, 3)+"-"+value.ano+"</option>");
        }
            
        $("#ano-mes").append(grupo);
        
        acesso[0]++;
        acesso[value.estado]++;
    });
    
    $("#total span:nth-child(1)").text("Pendente: R$ "+total_pendente.toFixed(2));
    $("#total span:nth-child(2)").text("Total: R$ "+total_periodo.toFixed(2));
    
    atualizarAcesso();
}

function imprimirPagamentos(temp, value, id_pagamento, total, valor_pendente) {
    temp += "<tr class='servicos' data-id='"+value.id_empresa+"' data-refer='"+value.id+"'>";
    temp += "<td></td>";
    temp += "<td colspan='3'>Pagamento</td>";
    temp += "<td></td>";
    temp += "<td>R$ -"+Number(pagamentos[id_pagamento].valor).toFixed(2)+"</td>";
    temp += "<td colspan='3'></td></tr>";
    
    if (pagamentos[id_pagamento].estado==3 || pagamentos[id_pagamento].estado==4) {
        total -= pagamentos[id_pagamento].valor;
        valor_pendente -= pagamentos[id_pagamento].valor;
    }
    
    if (pagamentos[id_pagamento].tipo==4 && pagamentos[id_pagamento].id_proximo!=null) {
        temp = imprimirPagamentos(temp, value, pagamentos[id_pagamento].id_proximo, total);
        
        value = temp.value;
        total = temp.total;
        total_pendente = temp.total_pendente;
        temp = temp.temp;
    }
    
    return {temp: temp, value: value, total: total, valor_pendente: valor_pendente};
}

function getEstadoPagamento(value, valor_pendente, pago, desconto) {
    novaData = new Date();
    novaData = new Date(novaData.getFullYear(), novaData.getMonth(), novaData.getDate());
    estado_pagamento = "<img src='../img/exclamacao.png' class='pendente'>Pendente";
    tempEstado=0;
    valor_pago = 0;
    if (value.id_pagamento!=null) {
        if (pagamentos[value.id_pagamento].estado==3 || pagamentos[value.id_pagamento].estado==4) {
            valor_pago += Number(pagamentos[value.id_pagamento].valor);
//            pago=true;
            if (pagamentos[value.id_pagamento].tipo==4) {
                estado_pagamento="<img src='../img/input-mark.png' class='pago'>Parcial";
                valor_pendente -= pagamentos[value.id_pagamento].valor;
                tempEstado = 2;
                if (desconto+pagamentos[value.id_pagamento].valor>=valor_pendente) {
                    pago=true;
                } else if (new Date(value.ano, value.mes-1, value.dia_vencimento)<novaData) {
                    estado_pagamento = "<img src='../img/pare.png' class='cancelado'>Vencido";
                    tempEstado = 3;
                }
                if (pagamentos[value.id_pagamento].id_proximo!=null) {
                     estadoProximo = pagamentos[pagamentos[value.id_pagamento].id_proximo].estado;
                    if (estadoProximo==3 || estadoProximo==4) {
                        estado_pagamento="<img src='../img/input-mark.png' class='pago'>Pago";
                        valor_pendente -= pagamentos[pagamentos[value.id_pagamento].id_proximo].valor;
                        valor_pago += Number(pagamentos[pagamentos[value.id_pagamento].id_proximo].valor);
                        tempEstado = 1;
                        pago = true;
                    }
                }
            } else {
                estado_pagamento="<img src='../img/input-mark.png' class='pago'>Pago";
                tempEstado = 1;
                pago = true;
            }
        } else if (pagamentos[value.id_pagamento].estado==1 || pagamentos[value.id_pagamento].estado==2) {
            estado_pagamento = "<img src='../img/exclamacao.png' class='pendente'>Aguardando Confirmação";
            tempEstado = 2;
        } else {
            if (new Date(value.ano, value.mes-1, value.dia_vencimento)<novaData) {
                estado_pagamento = "<img src='../img/pare.png' class='cancelado'>Vencido";
                tempEstado = 3;
            } else {
                estado_pagamento = "<img src='../img/exclamacao.png' class='pendente'>Mês Atual";
                tempEstado = 2;
            }
        }
    } 
    else {
        if (new Date(value.ano, value.mes-1, value.dia_vencimento)<novaData) {
            estado_pagamento = "<img src='../img/pare.png' class='cancelado'>Vencido";
            tempEstado = 3;
        } else {
            estado_pagamento = "<img src='../img/exclamacao.png' class='pendente'>Mês Atual";
            tempEstado = 2;
        }
    }
    
//    if (value.id==8) console.log({estado_pagamento: estado_pagamento, valor_pendente: valor_pendente, tempEstado: (value.estado==3?3:tempEstado), pago: pago});
    return {estado_pagamento: estado_pagamento, valor_pendente: valor_pendente, tempEstado: (value.estado==3?3:tempEstado), pago: pago, valor_pago: valor_pago};
}

function getEstadoPagamentoMensalidade(value, valor_pendente, pago, pendente) {
    novaData = new Date();
    novaData = new Date(novaData.getFullYear(), novaData.getMonth(), novaData.getDate());
//    console.log(Number(value.mensalidade || value.valor));
    estado_pagamento = "<img src='../img/exclamacao.png' class='pendente'>Pendente";
    tempEstado = "Pendente";
    if (value.id_pagamento!=null) {
        if (pagamentos[value.id_pagamento].estado==3 || pagamentos[value.id_pagamento].estado==4) {
            if (pagamentos[value.id_pagamento].tipo==4) {
                estado_pagamento="<img src='../img/input-mark.png' class='pago'>Parcial";
                tempEstado = "Parcial";
                valor_pendente += Number(value.mensalidade);
                if (pagamentos[value.id_pagamento].id_proximo!=null) {
                     estadoProximo = pagamentos[pagamentos[value.id_pagamento].id_proximo].estado;
                    if (estadoProximo==3 || estadoProximo==4) {
                        estado_pagamento="<img src='../img/input-mark.png' class='pago'>Pago";
                        tempEstado = "Pago";
                        valor_pendente -= pagamentos[pagamentos[value.id_pagamento].id_proximo].valor;
                        pago = true;
                    }
                }
            } else {
                estado_pagamento="<img src='../img/input-mark.png' class='pago'>Pago";
                tempEstado = "Pago";
                pago = true;
            }
        } else if (pagamentos[value.id_pagamento].estado==1 || pagamentos[value.id_pagamento].estado==2) {
            estado_pagamento = "<img src='../img/exclamacao.png' class='pendente'>Aguardando Confirmação";
            tempEstado = "Aguardando Confirmação";
            valor_pendente += Number(value.mensalidade);
        }  else {
            if (new Date(value.ano, value.mes-1, value.dia_vencimento)<novaData) {
                estado_pagamento = "<img src='../img/pare.png' class='cancelado'>Vencido";
                tempEstado = "Vencido";
                valor_pendente += Number(value.mensalidade);
                pendente=true;
            } else {
                estado_pagamento = "<img src='../img/exclamacao.png' class='pendente'>Mês Atual";
                tempEstado = "Mês Atual";
                valor_pendente += Number(value.mensalidade);
            }
        }
    } 
    else {
        if (new Date(value.ano, value.mes-1, value.dia_vencimento)<novaData) {
            estado_pagamento = "<img src='../img/pare.png' class='cancelado'>Vencido";
            tempEstado = "Vencido";
            valor_pendente += Number(value.mensalidade);
            pendente=true;
        } else {
            estado_pagamento = "<img src='../img/exclamacao.png' class='pendente'>Mês Atual";
            tempEstado = "Mês Atual";
            valor_pendente += Number(value.mensalidade);
        }
    }
    
    return {estado_pagamento: estado_pagamento, valor_pendente: valor_pendente, estado: tempEstado, pago: pago, pendente: pendente};
}

function getEstadoPagamentoServicos(value, valor_pendente, pago, pendente) {
    novaData = new Date();
    novaData = new Date(novaData.getFullYear(), novaData.getMonth(), novaData.getDate());
//    console.log(Number(value.mensalidade || value.valor));
    estado_pagamento = "<img src='../img/exclamacao.png' class='pendente'>Pendente";
    tempEstado = "Pendente";
    if (value.id_pagamento!=null) {
        if (pagamentos[value.id_pagamento].estado==3 || pagamentos[value.id_pagamento].estado==4) {
            if (pagamentos[value.id_pagamento].tipo==4) {
                estado_pagamento="<img src='../img/input-mark.png' class='pago'>Parcial";
                tempEstado = "Parcial";
                valor_pendente += Number(value.valor);
                if (pagamentos[value.id_pagamento].id_proximo!=null) {
                     estadoProximo = pagamentos[pagamentos[value.id_pagamento].id_proximo].estado;
                    if (estadoProximo==3 || estadoProximo==4) { 
                        estado_pagamento="<img src='../img/input-mark.png' class='pago'>Pago";
                        tempEstado = "Pago";
                        valor_pendente -= pagamentos[pagamentos[value.id_pagamento].id_proximo].valor;
                        pago = true;
                    }
                }
            } else {
                estado_pagamento="<img src='../img/input-mark.png' class='pago'>Pago";
                tempEstado = "Pago";
                pago = true;
            }
        } else if (pagamentos[value.id_pagamento].estado==1 || pagamentos[value.id_pagamento].estado==2) {
            estado_pagamento = "<img src='../img/exclamacao.png' class='pendente'>Aguardando Confirmação";
            tempEstado = "Aguardando Confirmação";
            valor_pendente += Number(value.valor);
        }  else {
            if (new Date(value.ano, value.mes-1, value.dia_vencimento)<novaData) {
                estado_pagamento = "<img src='../img/pare.png' class='cancelado'>Vencido";
                tempEstado = "Vencido";
                valor_pendente += Number(value.valor);
                pendente=true;
            } else {
                estado_pagamento = "<img src='../img/exclamacao.png' class='pendente'>Mês Atual";
                tempEstado = "Mês Atual";
                valor_pendente += Number(value.valor);
            }
        }
    } 
    else {
        if (new Date(value.ano, value.mes-1, value.dia_vencimento)<novaData) {
            estado_pagamento = "<img src='../img/pare.png' class='cancelado'>Vencido";
            tempEstado = "Vencido";
            valor_pendente += Number(value.valor);
            pendente=true;
        } else {
            estado_pagamento = "<img src='../img/exclamacao.png' class='pendente'>Mês Atual";
            tempEstado = "Mês Atual";
            valor_pendente += Number(value.valor);
        }
    }
    retorno = {estado_pagamento: estado_pagamento, valor_pendente: valor_pendente, estado: tempEstado, pago: pago, pendente: pendente};
    if (value.id==82) console.log(retorno);
    return retorno;
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

            if (periodo.ano!=0) {
                if (value.ano==periodo.ano && value.mes==periodo.mes) {
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
    
    $("#acesso-rapido li:nth-child(1) h4").text(acesso[0]+(acesso[0]<2?" Histórco":" Históricos"));
    $("#acesso-rapido li:nth-child(2) h4").text(acesso[1]+(acesso[1]<2?" Histórco":" Históricos"));
    $("#acesso-rapido li:nth-child(3) h4").text(acesso[2]+(acesso[2]<2?" Histórco":" Históricos"));
    $("#acesso-rapido li:nth-child(4) h4").text(acesso[3]+(acesso[3]<2?" Histórco":" Históricos"));
}

function pesquisarHistorico(valor, teste) { 
    total_periodo = 0;
    total_pendente = 0;
    
    teste = teste || 0;
    
    if (valor.length==0) {
        //mostrar todas;
        $("tr.geral").show();
    } 
    id_hide = [];

    $.each(historico, function(i, value) {
        value.aparecer=true;
        
        if (filtro!=0) {
            if (value.estado==filtro) {
                $("tr.geral[data-refer="+value.id+"]").show();
                value.aparecer=true;
            } else {
                $("tr[data-refer="+value.id+"]").hide();
                $("tr.geral[data-refer="+value.id+"]").attr("data-aberto", 0);
                $("tr.geral[data-refer="+value.id+"] td:first-child span").css("transform", "rotate(0deg)");
                value.aparecer=false;
            }
        }
        
        if (valor.length!=0 && value.aparecer) {
            if (value.razao_social.toLowerCase().indexOf(valor.toLowerCase()) != -1) {
                $("tr.geral[data-refer="+value.id+"]").show();
                value.aparecer=true;
            } else {
                $("tr[data-id="+value.id_empresa+"]").hide();
                $("tr.geral[data-refer="+value.id+"]").attr("data-aberto", 0);
                $("tr.geral[data-refer="+value.id+"] td:first-child span").css("transform", "rotate(0deg)");
                value.aparecer=false;
                
            }
        }
        
        if (periodo.ano!=0 && value.aparecer) {
            if (value.ano==periodo.ano && value.mes==periodo.mes) {
                $("tr.geral[data-refer="+value.id+"]").show();
                value.aparecer=true;
            } else {
                $("tr[data-refer="+value.id+"]").hide();
                $("tr.geral[data-refer="+value.id+"]").attr("data-aberto", 0);
                $("tr.geral[data-refer="+value.id+"] td:first-child span").css("transform", "rotate(0deg)");
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
    if (total_pendente<0) total_pendente=0
    $("#total span:nth-child(1)").text("Pendente: R$ "+total_pendente.toFixed(2));
    $("#total span:nth-child(2)").text("Total: R$ "+total_periodo.toFixed(2));
    
    atualizarAcesso(teste);
}

$("#acesso-rapido li").click(function() {
    filtro = $(this).attr("data-filtro");
    pesquisarHistorico(pesquisa);
});

$("#pesquisar input").keyup(function(e) {
    valor = $(this).val();
    pesquisa=valor;
    pesquisarHistorico(valor, 1);
});

$("#ano-mes").change(function() {
    valor = $(this).val();
    
    valor = valor.split("-");
    periodo.mes = valor[0];
    periodo.ano = valor[1];
    
    pesquisarHistorico(pesquisa, 1);
//    console.log(periodo);
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
    
    if (historico[tempId].id_pagamento!=null) {
        if (pagamentos[historico[tempId].id_pagamento].tipo==4) {
            $("#pagar select").val(1).attr("disabled", true);
            $("#pagar input").val(historico[tempId].total_pendente-historico[tempId].desconto).attr("disabled", true);
        }
    } else {
        $("#pagar select").val(0);
        $("#pagar input").val(historico[tempId].total_pendente).attr("disabled", true);
    }
    
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
        chamarPopupInfo("Ovalor deve ser superior a R$ 1,00 real!");
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
//            console.log(result);
//            return;
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
//            console.log(result);
//            return;
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
    
    if (typeof data.desconto != "number" || data.desconto<=0) {
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
//            console.log(result);
//            return;
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

$(document).on("click", "td .excluir", function() {
    tempId = $(this).parent().parent().attr("data-index");
    
    data = {historico: historico[tempId]};
//    console.log(data);
    
    $.ajax({
        url: "../php/contabilidade/manterHistorico.php",
        type: "post",
        data: data,
        success: function(result) {
            result = JSON.parse(result);
            console.log(result);
//            return;
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