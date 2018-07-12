$(document).ready(function() {
    if (alvaras==null) return;
    
    $.each(alvaras, function(i, value) {
        if (value.tipo_alvara_2!=null || value.tipo_alvara_2!="") {
            temp = {tipo_alvara_1: value.tipo_alvara_2, vencimento_alvara_1: value.vencimento_alvara_2, id: value.id, razao_social: value.razao_social}
            alvaras.push(temp);
        }
    })
    
    $.each(alvaras, function(i, value) {
        if (value.tipo_alvara_1==null || value.tipo_alvara_1=="") value.vencimento_alvara_1=999999999999999;    
    });
    
    alvaras.sort(function(a, b) {
        return a.vencimento_alvara_1 - b.vencimento_alvara_1;
    });
    
    atualizarAlvaras();
});

function toDate(time) {
    data = new Date(time*1000);
    
    temp = "";
    if (time==null || time=="") {
        temp = "__/__/__";
    } else {
        temp =colocarZero(data.getDate())+"/"+colocarZero(data.getMonth()+1)+"/"+data.getFullYear();
    }
    
    return temp;
}

function colocarZero(numero) {
    if (numero<10) {
        numero = "0"+numero;
    }
    
    return numero;
}

function atualizarAlvaras(filtro) {
    filtro = filtro || 0;
    
    $("#certificados table tr:not(:first-child)").remove();
    
    $.each(alvaras, function(i, value) {
        $("#certificados table").append("<tr class='spacer'><td></td></tr>");
        exclamacao = "";
        if (new Date().getTime()/1000 > value.vencimento_alvara_1) exclamacao = "<img src='../img/exclamacao.png' title='Certificado Digital Vencido'>";
        else exclamacao = "Ok";
        
        data = toDate(value.vencimento_alvara_1);
        
        if (value.vencimento_alvara_1==999999999999999) data="__/__/__"; 
        
        temp = "<tr>";
        if (typeof funcionario=="undefined")
            temp += "<td data-nome='Empresa'><a href='/empresaResumo/"+value.id+"'>"+value.razao_social+"<img src='../img/link.png'></a></td>";
        else
            temp += "<td data-nome='Empresa'><a href='/funcionarioEmpresaResumo/"+value.id+"'>"+value.razao_social+"<img src='../img/link.png'></a></td>";
        temp += "<td data-nome='Tipo'>"+value.tipo_alvara_1+"</td>";
        temp += "<td data-nome='Validade'>"+data+"</td>";
        temp += "<td data-nome='Estado' class='exclamacao center'>"+exclamacao+"</td>";
        
        $("#certificados table").append(temp);
    });
}