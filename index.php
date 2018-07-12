<?
session_start();
?>
<!DOCTYPE HTML>
<html>
    <head>
        <title>KT Prime - Inicio</title>
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
       <? include("html/header.html"); ?>
       
       <section id="inicio">
           <div id="fundo"></div>
           <div>
               <h1>KT Prime Contabilidade</h1>
           
               <h3>Somos um escritório de contabilidade especializado na abertura e manutenção de pequenas e médias empresas no regime tributário do Simples Nacional!</h3>

               <a href="#servicos" class="botao">Nossos Serviços</a>
           </div>
       </section>
       
       <section id="servicos">
           <div>
               <div>
                   <img src="img/simples-nacional1.png" alt="">
<!--                   <img src="img/simples-nacional.png" alt="" id="simular">-->
                  <div id="simular">
                      <img src="img/cifrao.png">
                      Simule aqui sua mensalidade
                  </div>
               </div>
               <div>
                   <h2>Nossos Serviços</h2>
                   <p>Somos um escritório de contabilidade especializado na abertura e manutenção de pequenas e médias empresa no regime tributário do Simples Nacional</p>
                   <p>Não cobramos 13 mensalidade como forma de gratidão aos clientes adimplentes;</p>
                   <p>Contamos com a área do cliente, com acesso on line através do nosso site para você obter tudo o que precisar.</p>
                   <p>Abrimos sua empresa em até 24 horas!</p>

                   <a href="servicos" class="botao">Saiba Mais</a>
               </div>
           </div>
       </section>
       
       <section id="sobre">
           <div>
               <div>
                   <h2>Nosso padrão de atendimento</h2>

                   <p>Com todo nosso sistema online a interação entre cliente e contador é super eficaz, através de nosso sistema onde toda documentação da sua empresa estará disponível 24 horas por dia via nuvem, todos os documentos dos seus funcionários bem como todas guias para pagamento dos tributos estarão disponíveis na sua área de cliente.</p>

                   <p>Em nosso escritório você fica conectado diretamente com contador através do Whatsapp, E-mail, Telefone fixo para te atender quantas vezes achar necessário.</p>

                   <a href="quem-somos" class="botao">Saiba mais</a>
               </div>
               <div>
                   <a href="whatsapp://send?text=Olá KTprime!&phone=+5511980621949"><img src="img/whats-atendimento.jpg" alt="Whatsapp"></a>
               </div> 
           </div>
       </section>
       
            
        <section id="clientes">
            <h2>Alguns de nossos clientes</h2>
            
            <div>
                <div class="container">
                    <a href="contato"><img src="img/sua-empresa.jpg" alt=""></a>
                    <a href=""><img src="img/clientes/cliente%20(1).jpg" alt=""></a>
                    <a href=""><img src="img/clientes/cliente%20(2).jpg" alt=""></a> 
                    <a href=""><img src="img/clientes/cliente%20(3).jpg" alt=""></a>
                    <a href=""><img src="img/clientes/cliente%20(4).jpg" alt=""></a>
                    <a href=""><img src="img/clientes/cliente%20(5).jpg" alt=""></a>
                    <a href=""><img src="img/clientes/cliente%20(6).jpg" alt=""></a>
                    <a href=""><img src="img/clientes/cliente%20(7).jpg" alt=""></a>
                    <a href=""><img src="img/clientes/cliente%20(8).jpg" alt=""></a>
                    <a href="contato"><img src="img/sua-empresa.jpg" alt=""></a>
<!--                    <a href="contato"><h3>Sua empresa pode estar aqui! Saiba como: Solicite mais infomações.</h3></a>-->
                </div>
            </div>
            
            <a href="contato" class="botao">Saiba Mais</a>
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
        
        <nav id="lateral">
            <ul>
                <li data-id='inicio' class="selecionado"><span>Inicio</span></li>
                <li data-id='servicos'><span>Serviços</span></li>
                <li data-id='sobre'><span>Equipe</span></li>
                <li data-id='clientes'><span>Clientes</span></li>
                <li data-id='mais'><span>Contato</span></li>
            </ul>
        </nav>
        
        <? 
        include("html/simulador.html"); 
        include("html/rodape.html"); 
        ?>
    </body>
    <script type="text/javascript">
        page = true;
    </script>
    <script src="js/index.js"></script>
</html>