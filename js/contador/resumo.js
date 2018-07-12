var menu = 0;
var novo_lembrete = 0;
var novo_editar = "novo";
var meses = ["Janeiro","Fevereiro","Março","Abril","Maio","Junho","Julho","Agosto","Setembro","Outubro","Novembro","Dezembro"];
var id_editar = 0;
var especifico = false;
var dia_selecionado = 0;

$(document).ready(function() {
    dataGeral = new Date();
    mesCalendario = dataGeral.getMonth();
    anoCalendario = dataGeral.getFullYear();
    
    $("#calendario").css({opacity: 1});
    
    $("[name=data_inicio]").mask("00/00/0000");
    $("[name=data_validade]").mask("00/00/0000");
    
    $("[name=data_validade]").attr("disabled", true);
    $("[name=id_empresa]").attr("disabled", true);
    
    if (lembretes==null) lembretes=[];
    
    atualizarCalendario(mesCalendario, anoCalendario);
    
    $(".lembrete.empresas").hide();
    $(".lembrete").css({opacity: 1});
});

$("#listar nav label").click(function() {
    clicado = $(this).attr("data-id");
    
    if (menu != clicado) {
        menu = clicado;
        
        $("#listar nav label").removeClass();
        $(this).addClass("selecionado");
        
        if (menu==0) {
            $(".lembrete.pessoal").show();
            $(".lembrete.empresas").hide();
        } else {
            $(".lembrete.empresas").show();
            $(".lembrete.pessoal").hide();
        }
    }
});

$("#listar header label, #novo-lembrete img").click(function() {
    if (novo_lembrete==0) {
        $("#novo-lembrete button").text("Criar Lembrete")
        $("[name=data_inicio]").val(colocarZero(dataGeral.getDate())+"/"+colocarZero(dataGeral.getMonth()+1)+"/"+dataGeral.getFullYear());
        $("#novo-lembrete").fadeIn().css({display: "flex"});
        novo_editar = "novo";
        $("[name=tipo]").change();
        if (especifico==true) {
            $("[name=data_inicio]").val(dia_selecionado+"/"+colocarZero(mesCalendario+1)+"/"+colocarZero(anoCalendario));
        }
    } else {
        $("#novo-lembrete").fadeOut();
        $("#novo-lembrete form")[0].reset();
    }
    
    novo_lembrete = novo_lembrete==0?1:0;
});

$("#voltar-mes").click(function() {
    $(".hoje").removeClass();
    if (mesCalendario==0) {
        mesCalendario = 11;
        anoCalendario--;
    } else {
        mesCalendario--;
    }

    atualizarCalendario(mesCalendario, anoCalendario);
    
    $("#mes").text(meses[mesCalendario]);
    $("#ano").text(anoCalendario);
});

$("#avancar-mes").click(function() {
    $(".hoje").removeClass();
    if (mesCalendario==11) {
        mesCalendario = 0;
        anoCalendario++;
    } else {
        mesCalendario++;
    }

//         console.log(mesCalendario+"/"+anoCalendario);

    atualizarCalendario(mesCalendario, anoCalendario);
});

$("[name=tipo]").change(function() {
    valor = $(this).val();
    
    if (valor==0) {
        $("[name=data_validade]").attr("disabled", true);
        $("[name=id_empresa]").attr("disabled", true);
    } else {
        $("[name=data_validade]").attr("disabled", false);
        $("[name=id_empresa]").attr("disabled", false);
    }
});

$("#listar footer").click(function() {
    atualizarLista(anoCalendario, mesCalendario);
    $("#listar header h4").text(colocarZero(dataGeral.getDate())+"/"+colocarZero(mesCalendario+1)+"/"+anoCalendario);
    $("#listar footer").hide();
    especifico = false;
});

$(document).on("change", ".lembrete input[type=checkbox]", function() {
    realizado = $(this).is(":checked")?1:0;
    id_editar = $(this).parent().attr("data-id");
    
    $.ajax({
        url: "../php/contabilidade/manterLembrete.php",
        type: "post",
        data: "realizado="+realizado+"&funcao=realizar&id_editar="+id_editar+"&id="+lembretes[id_editar].id,
        success: function(result) {
            result = JSON.parse(result);

            if (result.estado==1) {
                console.log(result.mensagem);
                lembretes[id_editar].realizado = realizado;
            } else if (result.estado==2) {
                chamarPopupInfo(result.mensagem);
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

$(document).on("click",".semana span[data-nada=0]", function() {
    $("#listar header h4").text($(this).attr("data-dia")+"/"+colocarZero(mesCalendario+1)+"/"+anoCalendario);
    atualizarLista(anoCalendario, mesCalendario, $(this).attr("data-dia"));
    dia_selecionado = $(this).attr("data-dia");
    $("#listar footer").show();
    especifico = true;
});

$(document).on("click",".lembrete", function(e) {
    if (!$(e.target).hasClass("lembrete") && !$(e.target).hasClass("titulo")) return;
    
    id_editar = $(this).attr("data-id");
    temp = lembretes[id_editar];
    
    novo_editar = "editar";
    $("[name=titulo]").val(temp.titulo);
    $("[name=tipo]").val(temp.tipo);
    
    $("[name=tipo]").change();
    
    if (temp.tipo!=0) {
        $("[name=id_empresa]").val(temp.id_empresa);
        $("[name=data_validade]").val(colocarZero(temp.dia)+"/"+colocarZero(temp.mes)+"/"+temp.ano);
        dataInicio = new Date(temp.data_inicio*1000);
        $("[name=data_inicio]").val(colocarZero(dataInicio.getDate())+"/"+colocarZero(dataInicio.getMonth()+1)+"/"+dataInicio.getFullYear());
    } else {
        $("[name=data_inicio]").val(colocarZero(temp.dia)+"/"+colocarZero(temp.mes)+"/"+temp.ano);
    }
    novo_lembrete=1;
    $("#novo-lembrete button").text("Atualizar")
    $("#novo-lembrete").fadeIn().css({display: "flex"});
});

function getDiaMes(mes, ano) {
//        mes--;
    var dias = [];
    var data = new Date(ano, mes, 1);

    while (data.getMonth() === mes) {
        dia = data.getDate();
        dias.push(dia<10?"0"+dia:dia);
        data.setDate(data.getDate()+1);
    }

    return dias;
}

function atualizarCalendario(mes, ano) {
    semana = 0;
    dias = getDiaMes(mes, ano);

    diaSemana = new Date(ano, mes, 1).getDay();

    if ($(".semana").length<6) {
        while ($(".semana").length<6) {
            $(".semana:last-child").clone().appendTo("#calendario article");
        }
    }

    while (dias.length>0) {
        temp = 1;
        semana++;
        while (temp<8) {
            if (temp<=diaSemana) {
                $(".semana:nth-child("+semana+") span:nth-child("+temp+")").text("").attr("data-nada", 1).attr("data-dia",0);
            } else if (dias.length>0) {
                if (dataGeral.getDate()==dias[0] && mes===dataGeral.getMonth() && ano==dataGeral.getFullYear()) {
                    $(".semana:nth-child("+semana+") span:nth-child("+temp+")").addClass("hoje");
                    $("#dia").text(dias[0]);
                }
                $(".semana:nth-child("+semana+") span:nth-child("+temp+")").text(dias[0]).attr("data-nada",0).attr("data-dia",dias[0]);
                dias.splice(0,1);
            } else {
                $(".semana:nth-child("+semana+") span:nth-child("+temp+")").text("").attr("data-nada",1).attr("data-dia",0);
            }

            temp++;
        }
        diaSemana=0;
    }

    while (semana<7) {
        semana++;
        $(".semana:nth-child("+semana+")").remove();
    }
    
    colocarBolinhaDia();
    atualizarLista(ano, mes);
    $("#mes").text(meses[mesCalendario]);
    $("#ano").text(anoCalendario);
}

function atualizarLista(ano, mes, dia) {
    dia = dia || 0;
    $(".lembrete").remove();
    contador = [0,0];
    $.each(lembretes, function(i, value) {
        if (value.ano == anoCalendario && value.mes-1==mesCalendario && (dia==0 || value.dia==dia)) {
            realizado = "";
            classe = "empresas";
            if (value.tipo==0) {
                classe = "pessoal";   
                contador[0]++;
            } else {
                contador[1]++;
            }
            
            if (value.realizado==1) realizado="checked";
            temp = "<li class='lembrete "+classe+"' data-id='"+i+"'>"
            temp += "<input type='checkbox' name='lembrete"+value.id+"' id='lembrete"+value.id+"' "+realizado+">"
            temp += "<label for='lembrete"+value.id+"'><img src='../img/input-mark.png'></label>"
            temp += "<h3 class='titulo'>"+value.titulo+"</h3>"
            temp += "<div class='excluir'><img src='../img/fechar.png' alt=''></div>"
            temp += "<span class='data'>"+colocarZero(value.dia)+"/"+colocarZero(value.mes)+"/"+value.ano+"</span>"
            temp += "</li>"

            $("#listar article ul").append(temp);
        }
    });
    $("#listar nav label:first-child span").text(contador[0]);
    $("#listar nav label:last-child span").text(contador[1]);
    
    $(".lembrete").css("opacity",1)
    if (menu==0) {
        $(".pessoal").show();
        $(".empresas").hide();
    }  else {
        $(".pessoal").hide();
        $(".empresas").show();
    }
}

function colocarBolinhaDia() {
    dia = getDiaMes(mesCalendario,anoCalendario);
    $(".semana span").attr("data-lembrete", 0)
    $.each(lembretes, function(i, value) {
        
        value.ano = new Date(value.data_inicio*1000).getFullYear();
        timeTemp = value.tipo==0?value.data_inicio:value.data_validade;
        value.mes = new Date(timeTemp*1000).getMonth()+1;
        value.dia = new Date(value.data_inicio*1000).getDate();
        
        if (value.ano == anoCalendario && value.mes-1==mesCalendario) {
            $(".semana span[data-dia='"+(value.dia<10?"0"+value.dia:value.dia)+"']").attr("data-lembrete",1);
        }
    });
}

function colocarZero(numero) {
    if (numero<10) {
        numero = "0"+numero;
    }
    
    return numero;
}

function ValidarData() {
    var aAr = typeof (arguments[0]) == "string" ? arguments[0].split("/") : arguments,
        lDay = parseInt(aAr[0]), lMon = parseInt(aAr[1]), lYear = parseInt(aAr[2]),
        BiY = (lYear % 4 == 0 && lYear % 100 != 0) || lYear % 400 == 0,
        MT = [1, BiY ? -1 : -2, 1, 0, 1, 0, 1, 1, 0, 1, 0, 1];
    return lMon <= 12 && lMon > 0 && lDay <= MT[lMon - 1] + 30 && lDay > 0;
}

// ajax
$("#novo-lembrete form").submit(function(e) {
    e.preventDefault();
    
    data = $(this).serialize();
    
    if (ValidarData($("[name=data_inicio]").val())==false) {
        chamarPopupInfo("Escolha uma data valida!");
        $("[name=data_inicio]").focus();
        return;
    }
    
    if ($("[name=tipo]").val()!=0) {
        if (ValidarData($("[name=data_validade]").val())==false) {
            chamarPopupInfo("Escolha uma data valida!");
            $("[name=data_validade]").focus();
            return;    
        }
        
        if ($("[name=id_empresa]").val()==0) {
            chamarPopupInfo("Escolha uma empresa para o tipo alvará!");
            $("[name=id_empresa]").focus();
            return;  
        }
    }
    
    if (novo_editar=="novo") {
        $.ajax({
            url: "../php/contabilidade/manterLembrete.php",
            type: "post",
            data: data+"&funcao=novo",
            success: function(result) {
                result = JSON.parse(result);

                if (result.estado==1) {
                    chamarPopupConf(result.mensagem);
                    lembretes.push(result.lembrete);
                    atualizarCalendario(mesCalendario, anoCalendario);
                    $("#novo-lembrete img").click();
                } else if (result.estado==2) {
                    chamarPopupInfo(result.mensagem);
                } else {
                    chamarPopupErro(result);
                }
            }, 
            error: function(result) {
                console.log(result);
                chamarPopupErro("Houve um erro, tente atualizar a página!");
            }
        });
    } else if (novo_editar=="editar") {
        $.ajax({
            url: "../php/contabilidade/manterLembrete.php",
            type: "post",
            data: data+"&funcao=editar&id_editar="+id_editar+"&id="+lembretes[id_editar].id,
            success: function(result) {
                result = JSON.parse(result);

                if (result.estado==1) {
                    console.log(result.mensagem);
                    lembretes[id_editar] = result.lembrete;
                    atualizarCalendario(mesCalendario, anoCalendario);
                    $("#novo-lembrete img").click();
                } else if (result.estado==2) {
                    chamarPopupInfo(result.mensagem);
                } else {
                    chamarPopupErro(result);
                }
            }, 
            error: function(result) {
                console.log(result);
                chamarPopupErro("Houve um erro, tente atualizar a página!");
            }
        });
    }
});

$(document).on("click",".lembrete .excluir", function() {
    id_excluir=$(this).parent().attr("data-id");
    
    $.ajax({
        url: "../php/contabilidade/manterLembrete.php",
        type: "post",
        data: "funcao=excluir&id_excluir="+id_excluir+"&id="+lembretes[id_excluir].id,
        success: function(result) {
            result = JSON.parse(result);

            if (result.estado==1) {
                chamarPopupConf("Lembrete excluido!")
                lembretes.splice(id_excluir, 1);
                atualizarCalendario(mesCalendario, anoCalendario);
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
