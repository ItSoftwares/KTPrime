<?
session_start();
?>
<!DOCTYPE HTML>
<html>
    <head>
        <title>KT Prime - Quem somos</title>
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
        
        <section id="quem">

            <div id="titulo">
               <div id="shadow"></div>
                <h1>Sobre</h1> 
            </div>
            <div id="especificacoes">
                <div id="quem-somos">
                    <div>
                        <h2>Quem somos</h2>

                        <p>Com sede própria, a KT Prime Contabilidade está no mercado desde 2009 trabalhando para manter a tranquilidade de você empresário/a;  O nosso atendimento é personalizado atendendo a necessidade de cada cliente, verificando as normas e as leis diariamente para que possamos deixar-los/as atualizados;  Contamos com o diferencial de controle total da sua contabilidade na Área do Cliente onde você pode fazer solicitações e acompanhar o andamento da execução, o escritório disponibiliza os documentos contábeis para download, alem de poder acompanhar relatórios mensais, para verificação e regularização da empresa.</p>
                    </div>
                    <img src="img/logo.png" alt="">
                </div> 
            
                <div id="trio">
                    <div>
                       <img src="img/missao.png" alt="">
                        <h2>Missão</h2>

                        <p>Atender as necessidades dos clientes com tecnologia, qualidade e rapidez dos serviços; Buscando sempre a satisfação dos nossos parceiros, clientes, fornecedores e funcionários com responsabilidade e respeito.</p>
                    </div>
                    <div>
                       <img src="img/visao.png" alt="">
                        <h2>Visão</h2>

                        <p>Ser reconhecida em seu ramo de atuação, por sua competência, e confiabilidade com preço justo. </p>
                    </div>
                    <div>
                       <img src="img/valores.png" alt="">
                        <h2>Valores</h2>
                         <ul>
                             <li>Ética</li>
                             <li>Qualidade</li>
                             <li>Respeito ao meio ambiente</li>
                             <li>Responsabildiade Social</li>
                         </ul> 
                    </div>
                </div>
                
                <div id="equipe">
                    <div>
                        <h2>Contador Responsável</h2>
                        
                        <div>
                            <div class="funcionario">
                                <header>
                                    <img src="img/karina.jpg">
                                    <div>
                                        <h3>Karina Campos</h3>
                                        <div><span class="email">CRC: 1SP266639</span></div>
                                    </div>
                                </header>
                                <p>Lorem ipsum dolor sit amet, consectetur adipisicing elit. Libero eveniet ullam eos voluptatem, aliquid? Aperiam ad placeat deserunt dicta iure fugit sit doloribus corporis illum inventore tenetur ab, consequuntur distinctio!</p>
                            </div>
                            
<!--
                            <div class="funcionario">
                                <header>
                                    <img src="servidor/contador/padrao.png">
                                    <div>
                                        <h3>Priscila da Silva Santana, Auxiliar Contábil</h3>
                                        <div><span class="email">atendimento@ktprime.com.br</span></div>
                                    </div>
                                </header>
                                <p>Lorem ipsum dolor sit amet, consectetur adipisicing elit. Libero eveniet ullam eos voluptatem, aliquid? Aperiam ad placeat deserunt dicta iure fugit sit doloribus corporis illum inventore tenetur ab, consequuntur distinctio!</p>
                            </div>
-->
                        </div>
                        
                        <a href="contato" class="botao">Faça parte da nossa equipe</a>
                    </div>
                </div>
            </div>
        </section>
           
       <section id="mais" class="branco">
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