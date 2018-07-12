var tipoPagamento = "cartao";
var ladoCartao = "frente";
var installments = [];
var padrao;
var idsHistorico = {};
var idsPagamentos = {};
$(document).ready(function() {
    $("tr.geral").attr("data-aberto", 0);
    $("#cpf").val(usuario.cpf)
    $("#telefone").val(usuario.telefone)
    $("#numero").mask("0000 0000 0000 0000")
    $("#validade").mask("00/0000");
    $("#cvc").mask("000");
    $("#cpf").mask("000.000.000-00");
    $("#aniversario").mask("00/00/0000");
    $("#telefone").mask("(00) 0000-0000");
    PagSeguroDirectPayment.setSessionId(id_sessao);
    $("#fundo-pagar button:not(#pagar-box)").attr("disabled", true);
    PagSeguroDirectPayment.getPaymentMethods({
        success: function(json) {
            //            if (valor!=0) $("#pagar button").attr("disabled", false); 
            console.log(json);
            getInstallments();
        },
        error: function(json) {
            console.log(json);
            var erro = "";
            for (i in json.errors) {
                erro = erro + json.errors[i];
            }
            chamarPopupErro("Houve algum erro, por favor atualize a página!")
        },
        complete: function(json) {
        }
    });
    atualizarFinanceiro();
    $.each(ids, function(i, value) {
        idsHistorico[value] = [];
        idsPagamentos[value] = [];
    });
    $.each(servicosPrestados, function(i, value) {
        if (value.id_historico in idsHistorico) {
            idsHistorico[value.id_historico].push(value.id);
        }
    });
    //    $.each(historico, function(i, value) {
    //        if ((value.id in idsPagamentos) && value.id_pagamento!=null) {
    ////            console.log(value)
    //            idsPagamentos[value.id].push(value.id_pagamento);
    //        }
    //    });
})

function pegarMes(i) {
    meses = ["Janeiro", "Fevereiro", "Marco", "Abril", "Maio", "Junho", "Julho", "Agosto", "Setembro", "Outubro", "Novembro", "Dezembro"];
    return meses[i - 1];
}

function colocarZero(numero) {
    if (numero < 10) return "0" + numero;
    else return numero
}

function atualizarFinanceiroAntigo(filtro) {
    filtro = filtro || 0;
    $("#valores tr").remove();
    total_periodo = 0;
    $.each(historico, function(i, value) {
        total = 0;
        temp = "";
        novaData = new Date();
        value.dia_vencimento = usuario.dia_vencimento;
        historico[i] = value;
        // LINHA DA MENSALIDADE
        estado_pagamento = getEstadoPagamentoMensalidade(value);
        total += Number(value.mensalidade);
        temp += "<tr class='servicos' data-id='" + value.id_empresa + "' data-refer='" + value.id + "'>";
        temp += "<td></td>";
        temp += "<td colspan='2'>Mensalidade</td>";
        temp += "<td>" + estado_pagamento + "</td>";
        temp += "<td>R$ " + Number(value.mensalidade).toFixed(2) + "</td></tr>";
        $.each(servicosPrestados, function(j, refer) {
            if (refer.id_historico != value.id) return true;
            refer.dia_vencimento = value.dia_vencimento;
            refer.mes = value.mes;
            refer.ano = value.ano;
            servicosPrestados[j] = refer;
            // LINHA DA MENSALIDADE
            estado_pagamento = getEstadoPagamentoServicos(value);
            total += Number(refer.valor);
            temp += "<tr class='servicos' data-id='" + value.id_empresa + "' data-refer='" + value.id + "'>";
            temp += "<td></td>";
            temp += "<td colspan='2'>" + refer.nome_servico + "</td>";
            temp += "<td>" + estado_pagamento + "</td>";
            temp += "<td>R$ " + Number(refer.valor).toFixed(2) + "</td></tr>";
        });
        // LINHA DO DESCONTO
        if (value.desconto > 0) {
            temp += "<tr class='servicos' data-id='" + value.id_empresa + "' data-refer='" + value.id + "'>";
            temp += "<td></td>";
            temp += "<td colspan='2'>Desconto</td>";
            temp += "<td></td>";
            temp += "<td>R$ -" + Number(value.desconto).toFixed(2) + "</td>";
            temp += "<td colspan='2'></td></tr>";
            total -= value.desconto;
        }
        // LINHA DO(S) PAGAMENTOS
        if (value.id_pagamento != null) {
            temp = imprimirPagamentos(temp, value, value.id_pagamento, total);
            value = temp.value;
            total = temp.total;
            temp = temp.temp;
        }
        // LINHA GERAL
        estado_pagamento = getEstadoPagamento(value, total);
        if (total == 0) {
            estado_pagamento = "<img src='../img/input-mark.png' class='pago'>Pago";
        }
        geral = "<tr class='geral' data-aberto=0 data-id='" + value.id_empresa + "' data-refer='" + value.id + "'>";
        geral += "<td><span></span></td>";
        geral += "<td>" + usuario.razao_social + "</td>";
        geral += "<td>" + pegarMes(value.mes) + " - " + value.ano + "</td>";
        geral += "<td>" + estado_pagamento + "</td>";
        geral += "<td> R$ " + total.toFixed(2) + "</td></tr>";
        total_periodo += total;
        value.total = total;
        $("#valores table").append(geral + temp);
    });
    //    $("#total span").text("R$ "+total_periodo.toFixed(2));
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
        value.pagamentos = 0;
        teste = total_pendente;
        pago = false;
        pendente = false;
        total = 0;
        temp = "";
        // LINHA DA MENSALIDADE
        valor_pendente += Number(value.mensalidade);
        value.bruto += Number(value.mensalidade);
        total += Number(value.mensalidade);
        temp += "<tr class='servicos' data-id='" + value.id_empresa + "' data-refer='" + value.id + "' data-img='1'>";
        temp += "<td></td>";
        temp += "<td colspan='3'>Mensalidade</td>";
        //        temp += "<td>ESTADO_MENSALIDADE</td>";
        temp += "<td>R$ " + toMoney(value.mensalidade) + "</td></tr>";
        // LINHA DOS SERVIÇOS PRESTADOS
        $.each(servicosPrestados, function(j, refer) {
            if (refer.id_historico != value.id) return true;
            refer.dia_vencimento = value.dia_vencimento;
            refer.mes = value.mes;
            refer.ano = value.ano;
            servicosPrestados[j] = refer;
            valor_pendente += Number(refer.valor);
            value.bruto += Number(refer.valor);;
            total += Number(refer.valor);
            temp += "<tr class='servicos' data-id='" + value.id_empresa + "' data-refer='" + value.id + "' data-img='1'>";
            temp += "<td></td>";
            temp += "<td colspan='2'>" + refer.nome_servico + "</td>";
            temp += "<td>R$ " + toMoney(refer.valor) + "</td></tr>";
        });
        // LINHA DO DESCONTO
        desconto = false;
        if (value.desconto > 0) {
            temp += "<tr class='servicos' data-id='" + value.id_empresa + "' data-refer='" + value.id + "'>";
            temp += "<td></td>";
            temp += "<td colspan='4'>Desconto</td>";
            temp += "<td>R$ -" + toMoney(value.desconto) + "</td></tr>";
            total -= value.desconto;
            total_pendente -= value.desconto;
            valor_pendente -= value.desconto;
            desconto = true;
        }
        // LINHA DO(S) PAGAMENTOS
        if (value.id_pagamento != null) {
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
        valor_pendente = value.bruto - value.desconto - value.valor_pago;
        value.total = total;
        value.total_pendente = valor_pendente;
        if (total == 0) {
            estado_pagamento = "<img src='../img/input-mark.png' class='pago'>Pago";
            value.estado = 1;
            value.pago = true;
        }
        if (value.total_pendente < 0) value.total_pendente = 0;
        if (Number(value.mes) + 1 == 13) value.vencimento = colocarZero(usuario.dia_vencimento) + "/01/" + (Number(value.ano) + 1);
        else value.vencimento = colocarZero(usuario.dia_vencimento) + "/" + colocarZero(Number(value.mes) + 1) + "/" + value.ano;
        geral = "<tr class='geral' data-aberto=0 data-id='" + value.id_empresa + "' data-refer='" + value.id + "' data-index='" + i + "'>";
        geral += "<td><span></span></td>";
        //        geral += "<td>"+usuario.razao_social.substr(0, 20)+".</td>";
        geral += "<td><b " + (value.estado == 3 ? "style='color: #ff5722'" : "") + ">" + value.vencimento + "</b></td>"
        geral += "<td>" + pegarMes(value.mes).substr(0, 3) + " - " + value.ano + "</td>";
        geral += "<td>" + estado_pagamento + "</td>";
        geral += "<td> R$ " + toMoney(value.total_pendente) + "</td></tr>";
        $("#valores table").append(geral + temp);
    });
}

function toMoney(valor) {
    valor = Number(valor);
    return valor.toFixed(2).replace(".", ",").replace(/(\d)(?=(\d{3})+\,)/g, '$1.');
}

function imprimirPagamentos(temp, value, id_pagamento, total, valor_pendente) {
    $.each(historico_pagamento[value.id], function(index, pag) {
        tipo = "Cartão de crédito";
        if (pagamentos[pag].tipo == 2) tipo = "Boleto Bancário";
        else if (pagamentos[pag].tipo > 2) tipo = "Manual";
        temp += "<tr class='servicos' data-id='" + value.id_empresa + "' data-refer='" + value.id + "'>";
        temp += "<td></td>";
        temp += "<td colspan='2'>Pagamento " + tipo + "</td>";
        temp += "<td></td>";
        temp += "<td>R$ -" + toMoney(pagamentos[pag].valor) + "</td></tr>";
        if (pagamentos[pag].estado == 3 || pagamentos[pag].estado == 4) {
            total -= pagamentos[pag].valor;
            valor_pendente -= pagamentos[pag].valor;
        }
    });
    return {
        temp: temp,
        value: value,
        total: total,
        valor_pendente: valor_pendente
    };
}

function getEstadoPagamento(value, valor_pendente, pago, desconto) {
    novaData = new Date();
    novaData = new Date(novaData.getFullYear(), novaData.getMonth(), novaData.getDate());
    estado_pagamento = getTextoPagamento(0);
    tempEstado = 0;
    valor_pago = 0;
    if (value.id in historico_pagamento) {
        $.each(historico_pagamento[value.id], function(index, pag) {
            if (pagamentos[pag].estado == 3 || pagamentos[pag].estado == 4) {
                valor_pago += Number(pagamentos[pag].valor);
                valor_pendente -= Number(pagamentos[pag].valor);
                value.pagamentos++;
            }
        });
        if (valor_pendente - desconto <= 0) {
            estado_pagamento = getTextoPagamento(1);
            pago = true;
            tempEstado = 1;
        } else if (value.id in historico_pagamento && historico_pagamento[value.id].length > 0 && value.pagamentos > 0) {
            estado_pagamento = getTextoPagamento(2);
            tempEstado = 2;
            if (new Date((value.mes == 12 ? value.ano + 1 : value.ano), (value.mes == 12 ? 0 : value.mes), value.dia_vencimento) < novaData) {
                estado_pagamento = getTextoPagamento(3);
                tempEstado = 3;
            }
        } else {
            if (new Date((value.mes == 12 ? value.ano + 1 : value.ano), (value.mes == 12 ? 0 : value.mes), value.dia_vencimento) < novaData) {
                estado_pagamento = getTextoPagamento(3);
                tempEstado = 3;
            } else if (value.mes != novaData.getMonth() + 1) {
                estado_pagamento = getTextoPagamento(6);
                tempEstado = 2;
            } else {
                estado_pagamento = getTextoPagamento(5);
                tempEstado = 2;
            }
        }
        //        if (pagamentos[value.id_pagamento].estado==3 || pagamentos[value.id_pagamento].estado==4) {
        //            valor_pago += Number(pagamentos[value.id_pagamento].valor);
        ////            pago=true;
        //            if (pagamentos[value.id_pagamento].tipo==4) {
        //                estado_pagamento = getTextoPagamento(2);
        //                valor_pendente -= pagamentos[value.id_pagamento].valor;
        //                tempEstado = 2;
        //                if (desconto+pagamentos[value.id_pagamento].valor>=valor_pendente) {
        //                    pago=true;
        //                } else if (new Date(value.ano, value.mes-1, value.dia_vencimento)<novaData) {
        //                    estado_pagamento = getTextoPagamento(3);
        //                    tempEstado = 3;
        //                }
        //                if (pagamentos[value.id_pagamento].id_proximo!=null) {
        //                     estadoProximo = pagamentos[pagamentos[value.id_pagamento].id_proximo].estado;
        //                    if (estadoProximo==3 || estadoProximo==4) {
        //                        estado_pagamento = getTextoPagamento(1);
        //                        valor_pendente -= pagamentos[pagamentos[value.id_pagamento].id_proximo].valor;
        //                        valor_pago += Number(pagamentos[pagamentos[value.id_pagamento].id_proximo].valor);
        //                        tempEstado = 1;
        //                        pago = true;
        //                    }
        //                }
        //            } else {
        //                estado_pagamento = getTextoPagamento(1);
        //                tempEstado = 1;
        //                pago = true;
        //            }
        //        } 
        //        else if (pagamentos[value.id_pagamento].estado==1 || pagamentos[value.id_pagamento].estado==2) {
        //            estado_pagamento = getTextoPagamento(4);
        //            tempEstado = 2;
        //        } 
        //        else {
        //            if (new Date(value.ano, value.mes-1, value.dia_vencimento)<novaData) {
        //                estado_pagamento = getTextoPagamento(3);
        //                tempEstado = 3;
        //            } else {
        //                estado_pagamento = getTextoPagamento(5);
        //                tempEstado = 2;
        //            }
        //        }
    } else {
        if (new Date((value.mes == 12 ? value.ano + 1 : value.ano), value.mes, value.dia_vencimento) < novaData) {
            estado_pagamento = getTextoPagamento(3);
            tempEstado = 3;
        } else if (value.mes != novaData.getMonth() + 1) {
            estado_pagamento = getTextoPagamento(6);
            tempEstado = 2;
        } else {
            estado_pagamento = getTextoPagamento(5);
            tempEstado = 2;
        }
    }
    return {
        estado_pagamento: estado_pagamento,
        valor_pendente: valor_pendente,
        tempEstado: (value.estado == 3 ? 3 : tempEstado),
        pago: pago,
        valor_pago: valor_pago
    };
}

function getTextoPagamento(estado) {
    if (estado == 1) {
        return "<img src='../img/input-mark.png' class='pago'>Pago";
    } else if (estado == 2) {
        return "<img src='../img/pontos.png' class='pendente'>Parcial";
    } else if (estado == 3) {
        return "<img src='../img/pare.png' class='cancelado'>Vencido";
    } else if (estado == 4) {
        return "<img src='../img/exclamacao.png' class='pendente'>Aguardando Confirmação";
    } else if (estado == 5) {
        return "<img src='../img/exclamacao.png' class='pendente'>Mês Atual";
    } else if (estado == 6) {
        return "<img src='../img/exclamacao.png' class='pendente'>Aguardando Pagamento";
    } else {
        return "<img src='../img/exclamacao.png' class='pendente'>Pendente";
    }
}

function verificarSelecionados() {
    if (ids.length == 0) {
        chamarPopupInfo("Escolha pelo menos um periodo para realizar o pagamento!");
        return true;
    }
}

$(document).on("click", "tr.geral", function() {
    aberto = $(this).attr("data-aberto");
    dataId = $(this).attr("data-refer");
    if (aberto == 0) {
        $(".servicos[data-refer=" + dataId + "]").show();
        $(this).find("td span").css({
            transform: "rotate(90deg)"
        });
    } else {
        $(".servicos[data-refer=" + dataId + "]").hide();
        $(this).find("td span").css({
            transform: "rotate(0deg)"
        });
    }
    aberto = aberto == 0 ? 1 : 0;
    $(this).attr("data-aberto", aberto);
});

$("input[name='pagamento']").change(function() {
    tipoPagamento = $(this).attr("id");
    if (tipoPagamento == "cartao") {
        $("#tipo").css({
            background: "#f4b35a"
        }).find("img").attr("src", "../img/pagamento/cartao-credito.png");
        $("#pagar-cartao").show();
        $("#pagar-boleto").hide();
        $("#pagar-deposito").hide();
        $("#titular").focus();
    } else if (tipoPagamento == "boleto") {
        $("#tipo").css({
            background: "#65c288"
        }).find("img").attr("src", "../img/pagamento/boleto.png");
        $("#pagar-cartao").hide();
        $("#pagar-deposito").hide();
        $("#pagar-boleto").show();
        $("#pagar-boleto button").focus();
    } else {
        $("#tipo").css({
            background: "#2980b9"
        }).find("img").attr("src", "../img/pagamento/deposito.png");
        $("#pagar-cartao").hide();
        $("#pagar-boleto").hide();
        $("#pagar-deposito").show();
    }
});

$("#pagar-cartao input").focusin(function() {
    $(".marcado").removeClass("marcado");
    id = "." + $(this).attr("data-id");
    $(id).addClass("marcado");
    if (id == ".cvc" && ladoCartao == "frente") {
        $("#cartao-credito").css({
            transform: "rotateY(180deg)"
        });
        ladoCartao = "verso";
    } else if (ladoCartao == "verso") {
        $("#cartao-credito").css({
            transform: "rotateY(00deg)"
        });
        ladoCartao = "frente";
    }
});

$("#pagar-cartao input").focusout(function() {
    $(".marcado").removeClass("marcado");
    if (ladoCartao == "verso") {
        $("#cartao-credito").css({
            transform: "rotateY(00deg)"
        });
        ladoCartao = "frente";
    }
});

$("#confirmar > img").click(function() {
    $("#confirmar").fadeOut();
});

$("#tabela input[type=checkbox]").change(function() {
    //    console.log($(this).attr("data-id"));
    ids = [];
    ids2 = [];
    ids3 = [];
    valor = 0;
    $("#tabela input[type=checkbox]").each(function(i, elem) {
        if ($(elem).is(":checked")) {
            idHistorico = $(this).attr("data-id");
            ids.push(idHistorico);
            $.each(idsHistorico[idHistorico], function(j, value) {
                ids2.push(value);
            });
            $.each(idsPagamentos[idHistorico], function(j, value) {
                ids3.push(value);
            });
            valor += Number(historico[$(this).attr("data-index")].total);
        }
    });
    if (valor != 0) {
        $("#fundo-pagar button:not(#pagar-box)").attr("disabled", false);
    } else {
        $("#fundo-pagar button:not(#pagar-box)").attr("disabled", true);
    }
    $("#valor-total").text("R$ " + (valor).toFixed(2).replace(".", ","));
});

$("#pagar-box").click(function() {
    if (verificarSelecionados()) return;
    $("#fundo-pagar").fadeIn().css({
        display: "flex"
    });
});

$("#fundo-pagar > img").click(function() {
    $("#fundo-pagar").fadeOut();
});

// PARTE DO PAGSEGURO
$("#numero").keyup(function() {
    getInstallments();
});

$("#pagar-cartao form button").click(function() {
    if (verificarSelecionados()) return;
    if ($("#titular").val().length == 0) {
        chamarPopupInfo("Digite o Nome do titular!");
        $("#titular").focus().select();
        return;
    }
    if ($("#numero").cleanVal().length != 16) {
        chamarPopupInfo("Digite um número de cartão válido!");
        $("#numero").focus().select();
        return;
    }
    if ($("#validade").cleanVal().length != 6) {
        chamarPopupInfo("Digite um validade válida!");
        $("#validade").focus().select();
        return;
    }
    if ($("#cvc").cleanVal().length != 3) {
        chamarPopupInfo("Digite um código cvc de 3 dígitos!");
        $("#cvc").focus().select();
        return;
    }
    $("#confirmar").fadeIn().css({
        display: "flex"
    });
});

$("#comprovante .fechar").click(function() {
    chamarPopupConf("Iremos atualizar a página aguarde...");
    setTimeout(function() {
        location.reload();
    }, 5000);
});

$("#pagar-cartao form").submit(function(e) {
    e.preventDefault();
    $("#pagar button").attr("disabled", true);
    data = $(this).serializeArray();
    temp = {};
    $.each(data, function(i, value) {
        temp[value.name] = value.value;
    });
    data = temp;
    data['telefone'] = $("#telefone").cleanVal();
    data['cpf'] = $("#cpf").cleanVal();
    if ($("[name=brand]").val() == "") {
        chamarPopupInfo("Informe um número de cartão válido!");
        $("#confirmar").fadeOut();
        $("#numero").focus().select();
    }
    var param = {
        cardNumber: $("#numero").cleanVal(),
        brand: $("#brand").text(),
        cvv: $("#cvc").val(),
        expirationMonth: $("#validade").val().split('/')[0],
        expirationYear: $("#validade").val().split('/')[1],
        success: function(json) {
            var token = json.card.token;
            data['token'] = token;
            var senderHash = PagSeguroDirectPayment.getSenderHash();
            data['senderHash'] = senderHash;
            data['ids'] = ids.join();
            //            // GERAR CONDIÇÃO 1
            //            temp = "where";
            //            $.each(ids, function(i, value) {
            //                temp += " id="+value;
            //                if (i!=ids.length-1) {
            //                    temp += " or";
            //                }
            //            });
            //            data['condicao'] = temp;
            //            
            //            // GERAR CONDIÇÃO 2
            //            temp = "where";
            //            $.each(ids2, function(i, value) {
            //                temp += " id="+value;
            //                if (i!=ids2.length-1) {
            //                    temp += " or";
            //                }
            //            });
            //            data['condicao2'] = temp;
            //            
            //            // GERAR CONDIÇÃO 3
            //            temp = "where";
            //            $.each(ids3, function(i, value) {
            //                temp += " id="+value;
            //                if (i!=ids3.length-1) {
            //                    temp += " or";
            //                }
            //            });
            //            data['condicao3'] = temp;
            // AJAX
            $("#pagar button").attr("disabled", true);
            $("#confirmar").hide();
            $("#comprovante").show().css({
                display: "flex"
            });
            $.ajax({
                url: "../php/pagseguro/pagarComCartao.php",
                type: "post",
                data: data,
                success: function(result) {
                    padrao = result;
                    result = JSON.parse(result);
                    result = result;
                    console.log(result);
                    $("#pagar button").attr("disabled", false);
                    if (result.status == 1) {
                        chamarPopupConf("Pagamento realizado com sucesso!");
                        $("#comprovante .valor").text(result.grossAmount.replace(".", ","));
                        $("#comprovante .descricao").text(decodeURIComponent(escape(result.items.item.description)));
                        $("#comprovante .codigo").text(result.code);
                        $("#comprovante .loading").hide();
                        $("#comprovante .fechar").show();
                        $("#comprovante > div").fadeIn();
                    } else {
                        console.log(result);
                        chamarPopupErro("Houve algum problema, tente novamente! Atualizando a página...");
                        setTimeout(function() {
                            location.reload();
                        }, 5000);
                    }
                },
                error: function(result) {
                    console.log(result);
                    chamarPopupErro("Houve um erro, tente atualizar a página!");
                    $("#pagar button").attr("disabled", false);
                }
            });
        },
        error: function(json) {
            console.log(json);
            $("#pagar button").attr("disabled", false);
        },
        complete: function(json) {
        }
    }
    console.log(data);
    PagSeguroDirectPayment.createCardToken(param);
})

$("#pagar-boleto form").submit(function(e) {
    e.preventDefault();
    if (verificarSelecionados()) return;
    $("#pagar button").attr("disabled", true);
    data = $(this).serializeArray();
    temp = {};
    $.each(data, function(i, value) {
        temp[value.name] = value.value;
    });
    data = temp;
    var senderHash = PagSeguroDirectPayment.getSenderHash();
    data['senderHash'] = senderHash;
    data['ids'] = ids.join();
    //    temp = "where";
    //    // GERAR CONDIÇÃO 1
    //    temp = "where";
    //    $.each(ids, function(i, value) {
    //        temp += " id="+value;
    //        if (i!=ids.length-1) {
    //            temp += " or";
    //        }
    //    });
    //    data['condicao'] = temp;
    //
    //    // GERAR CONDIÇÃO 2
    //    temp = "where";
    //    $.each(ids2, function(i, value) {
    //        temp += " id="+value;
    //        if (i!=ids2.length-1) {
    //            temp += " or";
    //        }
    //    });
    //    data['condicao2'] = temp;
    //
    //    // GERAR CONDIÇÃO 3
    //    temp = "where";
    //    $.each(ids3, function(i, value) {
    //        temp += " id="+value;
    //        if (i!=ids3.length-1) {
    //            temp += " or";
    //        }
    //    });
    //    data['condicao3'] = temp;
    console.log(data);
    //    return;
    // AJAX
    $("#confirmar").hide();
    $("#comprovante").show().css({
        display: "flex"
    });
    $("#pagar button").attr("disabled", true);
    $.ajax({
        url: "../php/pagseguro/pagarComBoleto.php",
        type: "post",
        data: data,
        success: function(result) {
            result = JSON.parse(result);
            console.log(result);
            $("#pagar button").attr("disabled", false);
            var win = window.open(result.paymentLink, "popUpWindow");
            if (result.status == 1) {
                chamarPopupConf("Pagamento realizado com sucesso, aguarde!");
                //                setTimeout(function() {
                //                    location.reload();
                //                },5000);
                $("#comprovante h2").html("Boleto de R$ <span class='valor'>0,00</span> gerado com sucesso!")
                $("#comprovante .valor").text(result.grossAmount.replace(".", ","));
                $("#comprovante .descricao").html(decodeURIComponent(escape(result.items.item.description)) + "<br><a href='" + result.paymentLink + "' target='_blank'>Link do Boleto</a>");
                $("#comprovante .codigo").text(result.code);
                $("#comprovante .loading").hide();
                $("#comprovante .fechar").show();
                $("#comprovante > div").fadeIn();
            }
        },
        error: function(result) {
            console.log(result);
            chamarPopupErro("Houve um erro, tente atualizar a página!");
            $("#pagar button").attr("disabled", false);
        }
    });
});


function getInstallments() {
    var cardNumber = $("#numero").cleanVal();
    //if creditcard number is finished, get installments
    if (cardNumber.length != 16) {
        return;
    }
    PagSeguroDirectPayment.getBrand({
        cardBin: cardNumber,
        success: function(json) {
            console.log(json);
            var brand = json.brand.name;
            $("#brand").text(brand);
            $("[name=brand]").val("brand")
        },
        error: function(json) {
            console.log(json);
            $("#brand").text("");
        },
        complete: function(json) {
            console.log(json);
            $("#brand").text("");
        }
    });
}