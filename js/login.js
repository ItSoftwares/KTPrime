var esqueceu = 0;

$("#direita a, #esqueceu img").click(function() {
    if (esqueceu == 0) {
        $("#esqueceu").fadeIn().css({display: "flex"});
    } else {
        $("#esqueceu").fadeOut();
    }
    
    esqueceu = esqueceu==0?1:0;
});

$("#login form").submit(function(e) {
    e.preventDefault();
    
    data = $(this).serialize();
   // console.log(data);
    $.ajax({
        url: "php/testeLogin.php",
        type: "post",
        data: data,
        success: function(result) {
            result = JSON.parse(result);
            
            console.log(result);
           // return;
            if(result.estado==1 || result.estado==3) {
                location.href="contador/resumo";
            } else if(result.estado==2 || result.estado==6) {
                chamarPopupErro(result.mensagem);
            } else if (result.estado==4) {
                location.href="empresa/inicio";
            } else if (result.estado==5) {
                location.href="contabilidade/empresas";
            }
        }, 
        error: function(result) {
            console.log(result);
            chamarPopupErro("Houve um erro, tente atualizar a página!");
        }
    })
});

$("#esqueceu form").submit(function(e) {
    e.preventDefault();
     
    data = $(this).serialize();
    
    $.ajax({
        url: "php/recuperarSenha.php",
        type: "post",
        data: data,
        success: function(result) {
            result = JSON.parse(result);
            console.log(result);
           // return;
            if(result.estado==1) {
                chamarPopupConf(result.mensagem);
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
    })
});