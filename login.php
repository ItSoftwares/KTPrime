<?
session_start();
$logado = 0;

if (isset($_SESSION['usuario_logado'])) {
    $logado = $_SESSION['usuario_logado'];
    
    if ($logado==1) {
        header("Location: contador/resumo");
    } else if ($logado==2) {
        header("Location: empresa/resumo");
    } else if ($logado==3) {
        header("Location: funcionario/empresas");
    }
} else {
    unset($_SESSION);
}
?>
<!DOCTYPE HTML>
<html>
    <head>
        <title>KT Prime - LOGIN</title>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <link rel="stylesheet" href="css/login.css" media="(min-width: 1000px)">
        <link rel="stylesheet" href="cssmobile/login.css?<? echo time(); ?>" media="(max-width: 999px)">
        <link rel="stylesheet" href="css/geral.css">
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
        <link href="img/favicon.png" rel="shortcut icon" type="image/x-icon" />
    </head>
    
    <body>
       <div id="fundo"></div>
        <div id="geral">
            <div id="login">
                <section id="esquerda">
                    <div>
                        <a href="/" id="logo"><img src="img/logo.png"></a>
                        <h2>Bem Vindo(a)</h2>

                        <a href="contato">Contato</a>
                    </div>
                </section>
                
                <section id="direita">
                    <h2>LOGIN</h2>
                    <form>
                        <div class="input">
                            <label>Email</label>
                            <input type="email" placeholder="Informe seu Email" name="email" required>
                        </div>
                        
                        <div class="input">
                            <label>Senha</label>
                            <input type="password" placeholder="Informe sua Senha" name="senha" required>
                        </div>
                        
                        <a href="#">Recuperar Senha</a> 
                        
                        <button class="botao">Entrar</button>
                    </form>
                </section>
            </div>
        </div>
        
        <section id="esqueceu">
            <div>
                <h3>Recuperar Senha</h3>
                <img src="img/fechar.png">
                <form>
                    <div class="input">
<!--                        <label>Email</label>-->
                        <input type="email" placeholder="Informe seu Email" name="email" required>
                    </div>
                    
                    <button class="botao">Recuperar</button>
                </form>
            </div>
        </section>
        
        <?
        include("html/popup.html");
        ?>
        <p>Copyright © Todos os direitos reservados, desenvolvido por ItSoftwares.</p>
    </body>
    
    <script src="js/login.js?<? echo time() ?>"></script>
</html>