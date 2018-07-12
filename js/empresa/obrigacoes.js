$(document).ready(function() {
    atualizarObrigacoes();
    obrigacoes = obrigacoes==null?[]:obrigacoes;
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

function atualizarObrigacoes(estado) {
    estado = estado || 0;
    $("table tr:not(:first-child)").remove();
    
    $.each(obrigacoes, function(i, value) {
        if (estado==1) {
            if (value.realizado==1) return true;
        } else if (estado==2) {
            if (value.realizado==0) return true;
        }
        
        $("#obrigacoes table").append("<tr class='spacer'><td></td></tr>");
        time = toDate(value.data_realizar);
        
        temp = "<tr data-id='"+i+"'>";
        temp += "<td data-nome='Estado'>"+(value.realizado==1?"<img src='../img/input-mark.png'>":"<img src='../img/exclamacao.png' class='pendente'>")+"</td>";
        temp += "<td data-nome='Periodo'>"+getPeriodo(value.periodo)+"</td>";
        temp += "<td data-nome='Descrição'>"+value.descricao+"</td>";
        temp += "<td data-nome='Data'>"+time+"</td>";
        temp += "<td data-nome='Anexo' class='anexo'>"+(value.tem_anexo==1?"<a href='../servidor/empresa/"+empresa.id+"/anexos/"+(new Date(value.data_realizar * 1000).getFullYear())+"/"+value.id+"."+value.extensao+"' download>":"")+"<img src='../img/anexo.png' "+(value.tem_anexo==0?"class='sem-anexo' title='Não possui anexos'":"title='Possui Anexos'")+">"+(value.tem_anexo==1?"</a>":"")+"</td>";
        
        temp += "</tr>";
        
        $("#obrigacoes table").append(temp);
    });
}

function getPeriodo(p) {
    if (p==1) return "Mensal";
    else if (p==2) return "Bimestral";
    else if (p==3) return "Trimestral";
    else if (p==4) return "Semestral";
    else if (p==5) return "Anual";
    
    return "error";
}