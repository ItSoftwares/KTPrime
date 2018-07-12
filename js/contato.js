$("#contato form").submit(function(e) {
    e.preventDefault();
    
    data = $(this).serialize();
    $("#contato button").attr("disabled", true);
    $.ajax({
        url: "php/contatar.php",
        type: "post",
        data: data,
        success: function(result) {
            console.log(result);
            result = JSON.parse(result);
            
//            return;
            if(result.estado==1) {
                chamarPopupConf("Mensagem enviada com sucesso, em breve entraremos em contato com você!");
                $("#contato form")[0].reset();
                $("#nome").focus();
            } else if (result.estado==2) {
                chamarPopupInfo("Mensagem enviada com sucesso, em breve entraremos em contato com você!");
                $("#email").focus();
            } else {
                console.log(result);
                chamarPopupErro("Houve um erro, tente atualizar a página!");
            }
            $("#contato button").attr("disabled", false);
        }, 
        error: function(result) {
            console.log(result);
            chamarPopupErro("Houve um erro, tente atualizar a página!");
        }
    })
});