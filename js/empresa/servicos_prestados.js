$(document).ready(function() {
    atualizarServicosPrestados();
    servicosPrestados = servicosPrestados==null?[]:servicosPrestados;
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

function atualizarServicosPrestados(estado) {
    estado = estado || 0;
    $("table tr:not(:first-child)").remove();
    
    $.each(servicosPrestados, function(i, value) {
        if (estado==1) {
            if (value.realizado==1) return true;
        } else if (estado==2) {
            if (value.realizado==0) return true;
        }
        
        $("#servicos-prestados table").append("<tr class='spacer'><td></td></tr>");
        time = toDate(value.data);
        
        temp = "<tr data-id='"+i+"'>";
        temp += "<td data-nome='id'><img src='../img/input-mark.png'></td>";
        temp += "<td data-nome='Serviço'>"+value.nome_servico+"</td>";
        temp += "<td data-nome='Descrição'>"+value.descricao+"</td>";
        temp += "<td data-nome='Data'>"+time+"</td>";
        temp += "<td data-nome='Anexo' class='anexo'>"+(value.tem_anexo==1?"<a href='../servidor/empresa/"+empresa.id+"/anexos/"+(new Date(value.data * 1000).getFullYear())+"/"+value.id+"."+value.extensao+"' download>":"")+"<img src='../img/anexo.png' "+(value.tem_anexo==0?"class='sem-anexo' title='Não possui anexos'":"title='Possui Anexos'")+">"+(value.tem_anexo==1?"</a>":"")+"</td>";
        
        temp += "</tr>";
        
        $("#servicos-prestados table").append(temp);
    });
}