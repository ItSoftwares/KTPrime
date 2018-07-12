<?
session_start();
?>
<!DOCTYPE HTML>
<html>
    <head>
        <title>KT Prime - Clientes</title>
        <meta charset="utf-8">
        <meta name="description" content="KTPrime, estamos preparados para atender as obrigatoriedades de sua empresa, prestando serviço para empresas do ramo de comércio, prestação de serviços e indústria. O escritório conta com equipamentos informatizados para agilizar e atender melhor as necessidades de cada empresa, e com funcionário capacitado a área fiscal, contábil, trabalhista e financeira.       O nosso atendimento é personalizado para cada cliente, verificando as normas e as leis diariamente para que possamos deixar os clientes atualizados e examinando no dia-a-dia os procedimentos legais da atividade de cada empresa, dispondo de relatórios mensais, de verificação e regularização dos recolhimentos efetuados pelo cliente">
        <meta name="keywords" content="Contabilidade, escritório contabilidade, escritório contábil, contador, contadora, abertura de empresa, abertura de CNPJ, RH empresa, trocar contador, encerramento de empresa, imposto de renda, declaração de imposto de renda, contabilidade para médico, contabilidade para advogado, contabilidade para engenheiro, contabilidade para pequena empresa , reduzir imposto empresa, simples nacional, CNPJ, contabilidade barata, contabilidade cara, contabilidade de qualidade, contabilidade online , contabilizei, agilize , contabilidade Taboão da Serra, contabilidade Embu das artes , contabilidade São Paulo , contabilidade Itapecerica da Serra , contabilidade Osasco">
        <meta name="author" content="itsoftwares">
        <meta name="viewport" content="width=device-width, initial-scale=1"> 
        <link rel="stylesheet" href="css/index.css" media="(min-width: 1000px)">
        <link rel="stylesheet" href="cssmobile/index.css?<? echo time(); ?>" media="(max-width: 999px)">
        <link rel="stylesheet" href="css/geral.css">
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
        <link href="img/favicon.png" rel="shortcut icon" type="image/x-icon" />
    </head>
    
    <body>
        <?
        include("html/header.html");
        ?>

        <section id="lista-clientes">
            <div id="titulo">
                <h1>Clientes</h1>
            </div>
            
            <div>
                <div class="empresa">
                    <header><img src="img/clientes/cliente%20(1).jpg" alt="">QViagem</header>
                    
                    <p>Lorem ipsum dolor sit amet, consectetur adipisicing elit. Voluptatum reprehenderit deleniti provident facilis modi neque qui labore, facere fugiat quas magnam, quisquam, iste ab sequi delectus est enim! Mollitia, iure?</p>
                </div>
                <div class="empresa">
                    <header><img src="img/clientes/cliente%20(2).jpg" alt="">Esmalteria Nacional</header>
                    
                    <p>Lorem ipsum dolor sit amet, consectetur adipisicing elit. Voluptatum reprehenderit deleniti provident facilis modi neque qui labore, facere fugiat quas magnam, quisquam, iste ab sequi delectus est enim! Mollitia, iure?</p>
                </div>
                <div class="empresa">
                    <header><img src="img/clientes/cliente%20(3).jpg" alt="">Imagine Colortistas</header>
                    
                    <p>Lorem ipsum dolor sit amet, consectetur adipisicing elit. Voluptatum reprehenderit deleniti provident facilis modi neque qui labore, facere fugiat quas magnam, quisquam, iste ab sequi delectus est enim! Mollitia, iure?</p>
                </div>
                <div class="empresa">
                    <header><img src="img/clientes/cliente%20(4).jpg" alt="">Campos Esttruturas Metálicas</header>
                    
                    <p>Lorem ipsum dolor sit amet, consectetur adipisicing elit. Voluptatum reprehenderit deleniti provident facilis modi neque qui labore, facere fugiat quas magnam, quisquam, iste ab sequi delectus est enim! Mollitia, iure?</p>
                </div>
                <div class="empresa">
                    <header><img src="img/clientes/cliente%20(5).jpg" alt="">RCK</header>
                    
                    <p>Lorem ipsum dolor sit amet, consectetur adipisicing elit. Voluptatum reprehenderit deleniti provident facilis modi neque qui labore, facere fugiat quas magnam, quisquam, iste ab sequi delectus est enim! Mollitia, iure?</p>
                </div>
            </div> 
        </section>
           
       <section id="mais">
            <div>
                <div>
                    <h2>Precisa de mais informações?</h2>
                    <p>Estamos aqui para ajudá-lo. Entre em contato por telefone, email ou whatsapp</p>
                </div>

                <div>
                    <a href="contato" class="botao">Contato</a>
                </div>
            </div>
        </section>
            
        <? include("html/rodape.html"); ?>
    </body>
    
    <script src="js/index.js"></script>
</html>