<?
session_start();
?>
<!DOCTYPE HTML>
<html>
    <head>
        <title>KT Prime - Contato</title>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <link rel="stylesheet" href="css/index.css" media="(min-width: 1000px)">
        <link rel="stylesheet" href="cssmobile/index.css" media="(max-width: 999px)">
        <link rel="stylesheet" href="css/geral.css">
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
        <link href="img/favicon.png" rel="shortcut icon" type="image/x-icon" />
    </head>
    
    <body>
       <?
        include("html/header.html");
        ?>
        
        <section id="contato">
            <div id="titulo">
                <div id="shadow"></div>
                <h1>Contato</h1>
            </div>
            <div>
                <form>
                <h2>Formulário de Contato</h2>
                    <div class="input">
                        <label for="nome">Nome</label>
                        <input type="text" name="nome" id="nome" placeholder="Nome" autofocus>
                    </div>

                    <div class="input">
                        <label for="email">Email</label>
                        <input type="email" name="email" id="email" placeholder="Email">
                    </div>

                    <div class="input">
                        <label for="nome">Mensagem</label>
                        <textarea name="mensagem" id="mensagem" placeholder="Escreva sua mensagem."></textarea>
                    </div>

                    <button class="botao">Enviar</button>
                </form>

                <div id="informacoes">
                    <div>
                        <h3>Endereço</h3>

                        <ul>
                            <li>Rua Afonso Desiderio, 140</li>
                            <li>Jardim Sadie, Sala 1</li>
                            <li>Embu das Artes - SP</li>
                            <li>Agende um horário conosco!</li>
                        </ul>
                    </div>

                    <div>
                        <h3>Fale Conosco</h3>

                        <ul>
                            <li><a href="tel:1147812596">(11) 4781-2596 - Escritório</a></li>
                            <li><a href="tel:11980621949">(11) 9.8062-1949 - Whatsapp</a></li>
                            <li><a href="#">atendimento@ktprime.com.br</a></li>
                            <li><a href="#">Contadora - karina@ktprime.com.br</a></li>
                        </ul>
                    </div>
                    
                    <div>
                        <h3>Horário de Atendimento</h3>
                        
                        <ul>
                            <li>Segunda a Sexta-feira das 8:00h às 17:00h</li>
                        </ul>
                    </div>
                </div>
            </div>
        </section>

        <? include("html/rodape.html"); ?>
        <?
        include("html/popup.html");
        ?>
    </body>
    
    <script src="js/contato.js"></script>
</html>