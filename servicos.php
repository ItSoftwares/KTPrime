<?
session_start();

$selecao = $_GET['selecao'];
?>
<!DOCTYPE HTML>
<html>
    <head>
        <title>KT Prime - Serviços</title>
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
        <div id="titulo" class="servicos">
           <div id="shadow"></div>
            <h1>Serviços</h1>
        </div>
        
        <section id="servicos" class="pagina">
           <h2>Nossos Serviços</h2>

            <ul>
                <? $style = $selecao==1?"class='selecionado'":"" ?>
                <li <? echo $style; ?>><a href="servicos?selecao=1#descricao"><img src="img/servicos/empresa.png">Abertura de Empresa</a></li>
                <? $style = $selecao==2?"class='selecionado'":"" ?>
                <li <? echo $style; ?>><a href="servicos?selecao=2#descricao"><img src="img/servicos/mei.png">Abertura de MEI</a></li>
                <? $style = $selecao==3?"class='selecionado'":"" ?>
                <li <? echo $style; ?>><a href="servicos?selecao=3#descricao"><img src="img/servicos/encerrar.png">Encerramento de Empresa</a></li>
                <? $style = $selecao==4?"class='selecionado'":"" ?>
                <li <? echo $style; ?>><a href="servicos?selecao=4#descricao"><img src="img/servicos/manuten%C3%A7%C3%A3o.png">Manutenção Mensal</a></li>
                <? $style = $selecao==5?"class='selecionado'":"" ?>
                <li <? echo $style; ?>><a href="servicos?selecao=5#descricao"><img src="img/servicos/situacao.png">Situação Empresarial</a></li>
                <? $style = $selecao==6?"class='selecionado'":"" ?>
                <li <? echo $style; ?>><a href="servicos?selecao=6#descricao"><img src="img/servicos/pessoal.png">Departamento Pessoal</a></li>
                <? $style = $selecao==7?"class='selecionado'":"" ?>
                <li <? echo $style; ?>><a href="servicos?selecao=7#descricao"><img src="img/servicos/imposto.png">Imposto de Renda</a></li>
                <? $style = $selecao==8?"class='selecionado'":"" ?>
                <li <? echo $style; ?>><a href="servicos?selecao=8#descricao"><img src="img/servicos/calculadora.png">Cálculo Trabalhista</a></li>
                <? $style = $selecao==9?"class='selecionado'":"" ?>
                <li <? echo $style; ?>><a href="servicos?selecao=9#descricao"><img src="img/servicos/empregada.png">Profissionais Liberais</a></li>
            </ul>

            <section id="descricao">
               <? $style = $selecao==1?"display: block":"display: none" ?>
                <div id="abertura-empresa" style="<? echo $style; ?>">
                    <h2>Abertura de Empresa</h2>

                    <p>Cuidamos de todo o processo de abertura da sua empresa, você poderá ter seu CNPJ em até 48h após assinatura do pedido, consulte as opções de abertura; Oferecemos o suporte necessário para obter todas as licenças do seu estabelecimento, conforme legislação, temos conhecimento com as prefeituras da região metropolitana de São Paulo, inclusive a prefeitura de São Paulo e suas subprefeituras, também experiência no interior do estado.</p>

                    <ol>
                        <li>Para ter sua empresa aberta, basta separar 02 cópias autenticadas da documentação inicial:</li>
                        <ul>
                            <li>RG/CPF do empresário ou dos sócios; </li>
                            <li>Certidão de casamento dos sócios; se houver;</li>
                            <li>Comprovante de residência, atualizado do empresário; </li>
                            <li>Título de eleitor do empresário; </li>
                            <li>Contrato de locação se o imóvel for alugado (endereço da empresa) </li>
                            <li>IPTU do imóvel (capa e contra-capa do endereço da empresa) </li>
                            <li>Recibo de declaração de imposto de renda dos últimos 02 anos; se houver;</li>
                        </ul>
                        <li>Qualquer dúvida estamos a disposição, (11)98062-1949</li>
                    </ol>
                </div>

                <? $style = $selecao==2?"display: block":"display: none" ?>
                <div id="abertura-mei" style="<? echo $style; ?>">
                    <h2>Abertura de MEI - Apoio nos seguintes serviços:</h2>

                    <ul>
                        <li>Abertura da empresa, Obtenção do CNPJ em menos de 24h, consulte;</li>
                        <li>Orientação nos procedimentos e elaboração da declaração de faturamento;</li>
                        <li>Levantamento de pendencias e regularização;</li>
                        <li>Declaração anual do Imposto de renda pessoa jurídica;</li>
                        <li>Registro de funcionário;</li>
                        <li>Manutenção e RH;</li>

                    </ul>
                    <p>Microempreendedor Individual (MEI) é a pessoa que trabalha por conta própria e que se legaliza como pequeno empresário, contendo o seu CNPJ; Para ser um microempreendedor individual, é necessário se atentar as regras e limites de faturamento, e demais detalhes como por exemplo não ter participação em outra empresa como sócio ou titular.</p>
                    
                    <p>A Lei Complementar nº 128, de 19/12/2008, criou condições especiais para que o trabalhador conhecido como informal possa se tornar um MEI legalizado.</p>
                    <p>Entre as vantagens oferecidas por essa lei está o registro no Cadastro Nacional de Pessoas Jurídicas (CNPJ), o que facilita a abertura de conta bancária, o pedido de empréstimos e a emissão de notas fiscais.</p>
                    <p>Além disso, o MEI será enquadrado no Simples Nacional e ficará isento dos tributos federais (Imposto de Renda, PIS, Cofins, IPI e CSLL). Assim, pagará apenas o valor fixo mensal, quantias que serão atualizadas anualmente, de acordo com o salário mínimo.</p>
                    <p>Com essas contribuições, o Microempreendedor Individual tem acesso a benefícios como auxílio maternidade, auxílio doença, aposentadoria, entre outros.</p>
                    <p>Entre contato para saber todos os detalhes, (11)98062-1949</p>

                </div>

                <? $style = $selecao==3?"display: block":"display: none" ?>
                <div id="encerramento-empresa" style="<? echo $style; ?>">
                    <h2>Encerramento de Empresas</h2>

                    <p>Prestamos os serviços para encerramento de empresas efetuando o distrato social, cancelamento do registro de empresário e baixa em todos demais órgãos;</p>

                    <ol>
                        <li>Junta Comercial</li>
                        <li>Receita Federal</li>
                        <li>Prefeitura</li>
                        <li>Secretaria da Fazendo do Estado</li>
                        <li>E demais, conforme o tipo jurídico</li>
                    </ol>
                </div>

                <? $style = $selecao==4?"display: block":"display: none" ?>
                <div id="manutencao-mensal" style="<? echo $style; ?>">
                    <h2>Manutenção Mensal</h2>

                    <p>Toda empresa aberta seja ela inativa, sem movimento ou em funcionamento, precisa de uma manutenção mensal para entrega das obrigações a que fica sujeita, como exemplo as declarações junto aos órgãos federais, estaduais e municipais, além de emissão de guias de impostos referente a compulsões da empresa junto ao estado e demais obrigações acessórias todo este trabalho deve ser feito por um contador, nosso escritório de contabilidade está preparado para cuidar da sua empresa deixando-a em dia, para que você possa cuidar dos seus negócios.
                    </p>
                    
                    <p>Contamos com o diferencial de isentar os clientes adimplentes do pagamento de decimo terceiro.</p>
                    
                    <div id="simular">
                        <img src="img/cifrao.png">
                        Simule aqui sua mensalidade
                    </div>
                    
                    <p>Conheça todas as vantagens, (11)98062-1949</p>
                </div>

                <? $style = $selecao==5?"display: block":"display: none" ?>
                <div id="situacao-empresarial" style="<? echo $style; ?>">
                    <h2>Situação Empresarial</h2>

                    <p>Efetuamos o levantamento completo das pendencias no CNPJ da sua empresa levando você a descobrir qual real situação fiscal a qual ela se encontra:</p>

                    <ol>
                        <li>Levantamento de débitos nos órgãos federais, estaduais e municipais</li>
                        <li>Certidão negativa todos órgãos</li>
                        <li>Protestos cartórios</li>
                        <li>Consulta SPC/Serasa</li>
                        <li>Consulta a processos</li>
                        <li>Apresentamos todas opções de regularização;</li>
                    </ol>
                    
                    <p>Consulte-nos (11)98062-1949</p>
                </div>

                <? $style = $selecao==6?"display: block":"display: none" ?>
                <div id="departamento-pessoal" style="<? echo $style; ?>">
                    <h2>Departamento Pessoal</h2>
                    
                    <p>Fazer a gestão dos colaboradores, controlar sua frequência, arquivar toda a documentação e garantir que as normas trabalhistas estão sendo cumpridas não é fácil. Para cuidar disso tudo, existe o Departamento Pessoal, mais conhecido como DP setor que realiza a administração da mão-de-obra do negócio.</p>
                    <p>Departamento Pessoal, é uma área especializada na gestão dos funcionários da empresa. Ele gerencia a folha de pagamento, férias, benefícios, atestados, marcação de ponto e passivos trabalhistas.</p>
                    <p>Ou seja, o setor está encarregado principalmente das questões burocráticas relacionadas aos colaboradores, garantindo a correta emissão e gerenciamento de documentos. Dessa forma, há mais agilidade e eficiência no controle desses fatores.</p>


                    <ol>
                        <li>Folha de Pagamento – Funcionários da empresa (PJ)</li>
                        <p>Cuidamos da folha de pagamento e todas demais rotinas do seu(s) funcionarios(s);</p>
                        <li>Folha de Pagamento - Empregado Doméstico (PF)</li>
                        <p>Cuidamos da folha de pagamento do seu(s) empregado(s) doméstico entregando o holerite mensal juntamente com as guias de recolhimento de INSS e FGTS.</p>
                    </ol>
                    
                    <h3>Admissão / Registro de funcionários</h3>
                    
                    <p>Para que seja feito o registro é necessário providenciar para contabilidade a relação de documentos abaixo, bem como preencher o informativo de admissão; Muito importante que estes passos sejam feitos antes do candidato iniciar qualquer prestação de serviço, ou seja logo após passar na entrevista do emprego.</p>
                    
                    <ul>
                        <li>Cópia simples RG, CPF, Titulo de eleitor</li>
                        <li>Cópia simples comprovante de endereço com CEP;</li>
                        <li>Cópia certidão de nascimento, Caderneta de vacinação e Histórico escolar dos filhos menores de 14 anos ou deficientes se houver;</li>
                        <li>Cópia da reservista;</li>
                        <li>Cópia da CNH</li>
                        <li>Atestado de saúde ocupacional - Admissional</li>
                        <li>CTPS;</li>
                        <li>01 FOTO 3x4;</li>
                    </ul>
                    
                    <p>Confira todos detalhes deste serviço (11)98062-1949</p>
                </div>

                <? $style = $selecao==7?"display: block":"display: none" ?>
                <div id="imposto-renda" style="<? echo $style; ?>">
                    <h2>IRPF – Imposto de renda pessoa física</h2>
                    
                    <p>Oferecemos um trabalho diferenciado na elaboração e ajuste da sua declaração anual do Imposto de renda pessoa física e demais documentos decorrentes</p>
                    
                    <ul>
                        <li>Assessoria e consultoria para da melhor maneira elaborar a declaração anual do Imposto de renda;</li>
                        <li>Acompanhamento do processamento do IRPF, periodicamente;</li>
                        <li>Atendimento às exigências previstas em atos normativos e atendimento a eventuais procedimentos de fiscalização (Malha fina da receita);</li>
                    </ul>
                    
                    <p>Se você se enquadra em algum requisito obrigatório então deverá declarar o imposto de renda ao leão e é com total profissionalismo que estamos prontos a atendê-lo, contate-nos agora mesmo (11)98062-1949</p>
                </div>

                <? $style = $selecao==8?"display: block":"display: none" ?>
                <div id="calculo-trabalhista" style="<? echo $style; ?>">
                    <h2>Cálculo Trabalhista</h2>

                    <p>Elaboramos um relatório sobre o cálculo dos direitos trabalhistas, apresentando uma planilha com todas informações relacionadas ao vinculo de trabalho;.</p>

                    <ul>
                        <li>Patrões de empregados domésticos.</li>
                        <li>Empregados demitidos com dúvidas.</li>
                        <li>Advogados trabalhistas.</li>
                        <li>E demais interessados</li>
                    </ul>
                    
                    <h3>Recisões</h3>
                    
                    <p>Para a demissão de funcionários é necessário providenciar para a contabilidade o exame médico demissional, carteira de trabalho (CTPS) e preencher o informativo de demissão.</p>
                    
                    <p>Nos procure, estamos aptos a execução em curto prazo (11)98062-1949</p>
                </div>

                <? $style = $selecao==9?"display: block":"display: none" ?>
                <div id="profissionais-liberais" style="<? echo $style; ?>">
                    <h2>Profissionais Liberais</h2>

                    <p>Somos o auxilio necessário para profissionais liberais e autônomos, estamos prontos para lhe atender, conheças alguns de nossos serviços:</p>

                    <ul>
                        <li>Recibo de pagamento Autônomo (RPA);</li>
                        <li>Gerar a contribuição ao INSS;</li>
                        <li>Cadastro na prefeitura (CCM);</li>
                        <li>Gerar o Imposto Sobre Serviços (ISS);</li>
                        <li>Gerar o Imposto de renda retido na fonte (IRRF);</li>
                        <li>Auxilio para obter os benefícios previdenciários;</li>
                        <li>Elaboração de imposto de renda;</li>
                        <li>Consultoria Contábil;</li>
                        <li>Emissão de Nota Fiscal;</li>
                        <li>Aposentadoria;</li>
                    </ul>
                    
                    <p>Consulte agora mesmo (11)98062-1949</p>
                </div>
            </section>
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

        <? 
        include("html/simulador.html"); 
        include("html/rodape.html"); 
        ?>
    </body>
    
    <script src="js/index.js"></script>
</html>