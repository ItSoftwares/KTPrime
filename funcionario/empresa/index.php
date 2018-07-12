<?if (!isset($_SESSION)) session_start();
require "../../php/conexao.php";
//require(dirname(__DIR__)."/php/sessao.php");
//
//verificarSeSessaoExpirou();
$titulo = "Resumo Empresa"; 
$menu = 1;
?>
<!DOCTYPE HTML>
<html>
    <head>
        <title>KT Prime - Resumo</title>
        <link rel="stylesheet" href="../css/empresa/resumo.css" media="(min-width: 1000px)"> 
        <link rel="stylesheet" href="../cssmobile/empresa/resumo.css" media="(max-width: 999px)">
        <link rel="stylesheet" href="../css/geral.css" media="(min-width: 1000px)">
        <link rel="stylesheet" href="../cssmobile/geral.css" media="(max-width: 999px)">
        <link href="../img/favicon.png" rel="shortcut icon" type="image/x-icon" />
    </head>
    
    <body>
        <?
        include("../menus.php");
        $temp = $empresa->toArray();
//        echo $temp['dados_banc_1'];
        for ($i=1; $i<5; $i++) {
            if ($temp['dados_banc_'.$i]==null || $temp['dados_banc_'.$i]=="" || isset($temp['dados_banc_'.$i.'_convertido'])) continue;
            $hash = $temp['hash'];
            $bancarios = $temp['dados_banc_'.$i];
            $bancarios = openssl_decrypt($bancarios, "AES-256-CBC", $hash, 0, $hash);
            $temp['dados_banc_'.$i] = $bancarios;
            $bancarios = explode("..", $bancarios);
            $temp['banco_'.$i] = $bancarios[0];
            $temp['agencia_'.$i] = $bancarios[1];
            $temp['conta_'.$i] = $bancarios[2];
            $temp['favorecido_'.$i] = $bancarios[3];
            $temp['dados_banc_'.$i.'_convertido'] = true;
        }
        $empresa->fromArray($temp);
        ?>
        
        <div id="container">
            <div>
            
                <section id="informacoes">
                    <h3>Informações - <span><? echo $empresa->estado_conta==1?"Ativa":"Inativa"; ?></span></h3>
                    <div id="editar" title="Editar Informações">
                        <img src="../img/editar-branco.png">
                    </div>
                    
                    <div id="lembrar" title="Enviar Email com lembrete de senha">
                        <img src="../img/mensagem.png">
                    </div>
                   <form>
                       <h4>Pessoa Física<span>1</span></h4>
                       <div id="cliente">
                           <div class="input metade">
                                <label>Nome do Responsável Legal (sócio 1)</label>
                                <input type="text" name="nome_cliente" placeholder="Nome do Responsável" required />
                            </div>
                            <div class="input metade">
                                <label>PIS</label>
                                <input type="text" name="pis_1" placeholder="PIS Responsável">
                            </div>
                            <div class="input metade">
                                <label>CPF</label>
                                <input type="text" name="cpf" placeholder="CPF" required />
                            </div>
                            <div class="input metade">
                                <label>Celular</label>
                                <input type="text" name="celular_1" placeholder="Celular" required />
                            </div>
                            <div class="input">
                                <label>Email</label>
                                <input type="email" name="email_1" placeholder="Email" required />
                            </div>
                            <div class="input metade">
                                <label>Código E-CAC</label>
                                <input type="text" name="codigo_ecac_1" placeholder="Código E-CAC">
                            </div>
                            <div class="input metade">
                                <label>Senha E-CAC</label>
                                <input type="text" name="senha_ecac_1" placeholder="Senha E-CAC">
                            </div>
                            <div class="input metade">
                                <label>% Participação</label>
                                <input type="text" name="percentual_socio_1" placeholder="% Participação no capital">
                            </div>
                            <div class="input metade">
                                <label>Valor Participação</label>
                                <input type="text" name="valor_socio_1" placeholder="Valor Participação no capital">
                            </div>
                            <hr>
                            <!--                              -->
                            <!--Sócio 1 renomeado para socio 2 -->
                            --------------------->
                            <div class="input metade">
                                <label>Nome do 2º Sócio</label>
                                <input type="text" name="nome_socio_1" placeholder="Nome do 1º Sócio">
                            </div>
                            <div class="input metade">
                                <label>PIS do 2º Sócio</label>
                                <input type="text" name="pis_2" placeholder="PIS">
                            </div>
                            <div class="input metade">
                                <label>CPF do 2º Sócio</label>
                                <input type="text" name="cpf_2" placeholder="CPF">
                            </div>
                            <div class="input metade">
                                <label>Celular do 2º Sócio</label>
                                <input type="text" name="celular_2" placeholder="Celular">
                            </div>
                            <div class="input">
                                <label>Email do 2º Sócio</label>
                                <input type="email" name="email_2" placeholder="Email">
                            </div>
                            <div class="input metade">
                                <label>Código E-CAC do 2º Sócio</label>
                                <input type="text" name="codigo_ecac_2" placeholder="Código E-CAC">
                            </div>
                            <div class="input metade">
                                <label>Senha E-CAC do 2º Sócio</label>
                                <input type="text" name="senha_ecac_2" placeholder="Senha E-CAC">
                            </div>
                            <div class="input metade">
                                <label>% Participação</label>
                                <input type="text" name="percentual_socio_2" placeholder="% Participação no capital">
                            </div>
                            <div class="input metade">
                                <label>Valor Participação</label>
                                <input type="text" name="valor_socio_2" placeholder="Valor Participação no capital">
                            </div>
                            <hr>
                            <!--                              -->
                            <!--Sócio 2 renomeado para socio 3 
                            <!--                              -->
                            <div class="input metade">
                                <label>Nome do 3º Sócio</label>
                                <input type="text" name="nome_socio_2" placeholder="Nome do 2º Sócio">
                            </div>
                            <div class="input metade">
                                <label>PIS do 3º Sócio</label>
                                <input type="text" name="pis_3" placeholder="PIS">
                            </div>
                            <div class="input metade">
                                <label>CPF do 3º Sócio</label>
                                <input type="text" name="cpf_3" placeholder="CPF">
                            </div>
                            <div class="input metade">
                                <label>Celular do 3º Sócio</label>
                                <input type="text" name="celular_3" placeholder="Celular">
                            </div>
                            <div class="input">
                                <label>Email do 3º Sócio</label>
                                <input type="email" name="email_3" placeholder="Email">
                            </div>
                            <div class="input metade">
                                <label>Código E-CAC do 3º Sócio</label>
                                <input type="text" name="codigo_ecac_3" placeholder="Código E-CAC">
                            </div>
                            <div class="input metade">
                                <label>Senha E-CAC do 3º Sócio</label>
                                <input type="text" name="senha_ecac_3" placeholder="Senha E-CAC">
                            </div>
                            <div class="input metade">
                                <label>% Participação</label>
                                <input type="text" name="percentual_socio_3" placeholder="% Participação no capital">
                            </div>
                            <div class="input metade">
                                <label>Valor Participação</label>
                                <input type="text" name="valor_socio_3" placeholder="Valor Participação no capital">
                            </div>
                            <hr>
                            <!--                              -->
                            <!--REPRESENTANTE LEGAL-->
                            <!--                              -->
                            <div class="input metade">
                                <label>Nome do Procurador/Representante</label>
                                <input type="text" name="nome_procurador" placeholder="Nome do Procurador">
                            </div>
                            <div class="input metade">
                                <label>PIS do Procurador/Representante</label>
                                <input type="text" name="pis_4" placeholder="PIS">
                            </div>
                            <div class="input metade">
                                <label>CPF do Procurador/Representante</label>
                                <input type="text" name="cpf_4" placeholder="CPF">
                            </div>
                            <div class="input metade">
                                <label>Celular do Procurador/Representante</label>
                                <input type="text" name="celular_4" placeholder="Celular">
                            </div>
                            <div class="input">
                                <label>Email do Procurador/Representante</label>
                                <input type="email" name="email_4" placeholder="Email">
                            </div>
                            <div class="input metade">
                                <label>Código E-CAC do Procurador/Representante</label>
                                <input type="text" name="codigo_ecac_4" placeholder="Código E-CAC">
                            </div>
                            <div class="input metade">
                                <label>Senha E-CAC do Procurador/Representante</label>
                                <input type="text" name="senha_ecac_4" placeholder="Senha E-CAC">
                            </div>
                            <div class="input">
                                <label>Tipo de vínculo</label>
                                <input type="text" name="tipo_vinculo_procurador" placeholder="Descreva o tipo de vínculo">
                            </div>
                            <div class="input">
                            </div>
                       </div>

                       <h4>Pessoa Jurídica<span>2</span></h4>

                       <div id="empresa">
                            <div class="input">
                                <label>Razão Social</label>
                                <input type="text" name="razao_social" placeholder="Razão Social" required>
                            </div>
                            <div class="input metade">
                                <label>CNPJ</label>
                                <input type="text" name="cnpj" placeholder="CNPJ">
                            </div>
                            <div class="input metade">
                                <label>Data de Abertura</label>
                                <input type="text" name="data_abertura" placeholder="Data de Abertura" required>
                            </div>
                            <div class="input">
                                <label>Email Acesso Site</label>
                                <input type="email" name="email" placeholder="Email" required>
                            </div>
                            <div class="input">
                                <label>Senha Acesso Site</label>
                                <input type="text" name="senha" placeholder="Senha" required>
                            </div>
                            <div class="input metade">
                                <label>Código Cliente</label>
                                <input type="text" name="codigo_cliente" placeholder="Código Cliente">
                            </div>
                            <div class="input metade">
                                <label>Caixa Arquivo</label>
                                <input type="text" name="caixa_arquivo" placeholder="Caixa Arquivo">
                            </div>
                            <div class="input metade">
                                <label>Inscrição Municipal - CCM</label>
                                <input type="text" name="inscricao_municipal" placeholder="Inscrição Municipal">
                            </div>
                            <div class="input metade">
                                <label>Inscrição Estadual</label>
                                <input type="text" name="inscricao_estadual" placeholder="Inscrição Estadual">
                            </div>
                            <div class="input metade">
                                <label>NIRE / Registro</label>
                                <input type="text" name="nire" placeholder="NIRE / Registro">
                            </div>
                            <div class="input metade">
                                <label>Tipo de Cliente/Regime Tributário</label>
                                <select name="simples_nacional">
                                    <option value="1">Simples nacional</option>
                                    <option value="2">Lucro presumido</option>
                                    <option value="3">Lucro real</option>
                                    <option value="5">MEI</option>
                                    <option value="6">Pessoa Física</option>
                                    <option value="4">Outros</option>
                                </select>
                            </div>
                            <div class="input metade">
                                <label>Tipo de Empresa</label>
                                <select name="data_simples_nacional">
                                    <option value="1">Prestação de serviços</option>
                                    <option value="2">Prestação de serviços e comércio</option>
                                    <option value="3">Comercio</option>
                                    <option value="4">Outros</option>
                                </select>
                            </div>
                            <div class="input metade">
                                <label>Cliente desde</label>
                                <input type="text" name="cliente_desde" placeholder="Data de inicio das atividades" data-mask="00/00/0000">
                            </div>

                            <!-- <div class="input metade">
                                <label>Valor da Mensalidade (R$)</label> 
                                <input type="number" name="mensalidade" placeholder="Mensalidade R$" required="" min="0" step="any">
                            </div> -->

                            <div class="input metade">
                                <label>Dia de vencimento</label>
                                <select name="dia_vencimento">
                                </select>
                            </div>
                            <div class="input">
                                <label>Ramo de Atividades</label>
                                <input type="text" name="ramo_atividades" placeholder="Ramo de Atividades">
                            </div>
                            <div class="input metade">
                                <label>CEP</label>
                                <input type="text" name="cep" placeholder="CEP">
                            </div>
                            <div class="input metade">
                                <label>Estado</label>
                                <input type="text" name="estado" placeholder="Estado">
                            </div>
                            <div class="input">
                                <label>Cidade</label>
                                <input type="text" name="cidade" placeholder="Cidade">
                            </div>
                            <div class="input metade">
                                <label>Número</label>
                                <input type="text" name="numero" placeholder="Numero">
                            </div>
                            <div class="input">
                                <label>Rua</label>
                                <input type="text" name="rua" placeholder="Rua">
                            </div>
                            <div class="input metade">
                                <label>Complemento</label>
                                <input type="text" name="complemento" placeholder="Complemento">
                            </div>
                            <div class="input metade">
                                <label>Bairro</label>
                                <input type="text" name="bairro" placeholder="Bairro">
                            </div>

                            <div class="input metade">
                                <label>Telefone 1</label>
                                <input type="text" name="telefone_1" placeholder="Telefone 1">
                            </div>
                            <div class="input metade">
                                <label>Telefone 2</label>
                                <input type="text" name="telefone_2" placeholder="Telefone 2">
                            </div>
                            <div class="input metade">
                                <label>Telefone 3</label>
                                <input type="text" name="telefone_3" placeholder="Telefone 3">
                            </div>
                            <div class="input metade">
                                <label>Celular Whatsapp</label>
                                <input type="text" name="celular_whatsapp" placeholder="Celular Whatsapp">
                            </div>
                            <div class="input metade">
                                <label>Email 2</label>
                                <input type="email" name="email_reserva" placeholder="Email">
                            </div>
                            <div class="input metade">
                                <label>Login Seguro Desemprego</label>
                                <input type="text" name="login_seguro_desemprego" placeholder="Login Seguro Desemprego">
                            </div>
                            <div class="input metade">
                                <label>Senha Seguro Desemprego</label>
                                <input type="text" name="senha_seguro_desemprego" placeholder="Senha Seguro Desemprego">
                            </div>
                            <div class="input metade">
                                <label>Prolabore</label>
                                <select name="prolabore">
                                    <option value="0">Não</option>
                                    <option value="1">Sim</option>
                                </select>
                            </div>
                            <div class="input metade">
                                <label>Funcionarios</label>
                                <select name="funcionarios">
                                    <option value="0">Não</option>
                                    <option value="1">Sim</option>
                                </select> 
                            </div>
                       </div>

                       <h4>Dados Bancários<span>3</span></h4>

                       <div id="bancarios">
                           <div class="input metade">
                               <label>Banco 1</label>
                               <input type="text" name="banco_1" placeholder="Banco 1">
                           </div>
                           <div class="input metade">
                               <label>Agência 1</label>
                               <input type="text" name="agencia_1" placeholder="Agência 1">
                           </div>
                           <div class="input metade">
                               <label>Conta 1</label>
                               <input type="text" name="conta_1" placeholder="Conta 1">
                           </div>
                           <div class="input metade">
                               <label>Favorecido 1</label>
                               <input type="text" name="favorecido_1" placeholder="Favorecido 1">
                           </div>
                           <hr>

                           <div class="input metade">
                               <label>Banco 2</label>
                               <input type="text" name="banco_2" placeholder="Banco 2">
                           </div>
                           <div class="input metade">
                               <label>Agência 2</label>
                               <input type="text" name="agencia_2" placeholder="Agência 2">
                           </div>
                           <div class="input metade">
                               <label>Conta 2</label>
                               <input type="text" name="conta_2" placeholder="Conta 2">
                           </div>
                           <div class="input metade">
                               <label>Favorecido 2</label>
                               <input type="text" name="favorecido_2" placeholder="Favorecido 2">
                           </div>
                           <hr>

                           <div class="input metade">
                               <label>Banco 3</label>
                               <input type="text" name="banco_3" placeholder="Banco 3">
                           </div>
                           <div class="input metade">
                               <label>Agência 3</label>
                               <input type="text" name="agencia_3" placeholder="Agência 3">
                           </div>
                           <div class="input metade">
                               <label>Conta 3</label>
                               <input type="text" name="conta_3" placeholder="Conta 3">
                           </div>
                           <div class="input metade">
                               <label>Favorecido 3</label>
                               <input type="text" name="favorecido_3" placeholder="Favorecido 3">
                           </div>
                           <hr>

                           <div class="input metade">
                               <label>Banco 4</label>
                               <input type="text" name="banco_4" placeholder="Banco 4">
                           </div>
                           <div class="input metade">
                               <label>Agência 4</label>
                               <input type="text" name="agencia_4" placeholder="Agência 4">
                           </div>
                           <div class="input metade">
                               <label>Conta 4</label>
                               <input type="text" name="conta_4" placeholder="Conta 4">
                           </div>
                           <div class="input metade">
                               <label>Favorecido 4</label>
                               <input type="text" name="favorecido_4" placeholder="Favorecido 4">
                           </div>
                           <hr>
                       </div>

                       <h4>Cadastro de Senhas<span>4</span></h4>

                       <div id="cadastro_senhas">
                           <div class="input metade">
                               <label>Login Municipal</label>
                               <input type="text" name="login_prefeitura" placeholder="Login Municipal - Prefeitura">
                           </div>
                           <div class="input metade">
                               <label>Senha Municipal</label>
                               <input type="text" name="senha_prefeitura" placeholder="Senha Municipal - Prefeitura">
                           </div>
                           <div class="input metade">
                               <label>Login Estadual - Posto Fiscal</label>
                               <input type="text" name="login_sefaz" placeholder="Login Estadual - SEFAZ">
                           </div>
                           <div class="input metade">
                               <label>Senha Estadual - Senha Online</label>
                               <input type="text" name="senha_sefaz" placeholder="Senha Estadual - SEFAZ">
                           </div>
                            <div class="input">
                               <label>Acesso Federal - Código simples nacional</label>
                               <input type="text" name="login_simples_nacional" placeholder="Acesso Federal - Simples Nacional">
                           </div>
                           <div class="input metade">
                               <label>Login ECAC Pessoa Jurídica</label>
                               <input type="text" name="login_ecac_pessoa_juridica" placeholder="Login ECAC Pessoa Jurídica">
                           </div>
                           <div class="input metade">
                               <label>Senha ECAC Pessoa Jurídica</label>
                               <input type="text" name="senha_ecac_pessoa_juridica" placeholder="Senha ECAC Pessoa Jurídica">
                           </div>
                       </div>

                       <h4>Outras Informações<span>5</span></h4>

                       <div id="outros">
                            <div class="input">
                                <label>Link para emissão de NFs de Serviço</label>
                                <input type="text" name="link_nf_servico" placeholder="Cole o link aqui">
                            </div>

                            <div class="input">
                                <label>Link para emissão de NFs de Venda</label>
                                <input type="text" name="link_nf_venda" placeholder="Cole o link aqui">
                            </div>
                            <div class="input metade">
                                <label>Tipo de Certificado Digital e Senha</label>
                                <input type="text" name="tipo_cd" placeholder="Tipo de Certificado Digital e Senha">
                            </div>
                            <div class="input metade">
                                <label>Validade Certificado Digital</label>
                                <input type="text" name="validade_cd" placeholder="Validade Cerificado Digital">
                            </div>
                            <div class="input metade">
                                <label>Senha para link de NFs de Venda</label>
                                <input type="text" name="senha_nf_venda" placeholder="Digite a senha aqui">
                            </div>
                            
                            <div class="input metade">
                                <label>Apelido (para buscas)</label>
                                <input type="text" name="apelido" placeholder="Informe palavras chave">
                            </div>
                            
                            <div class="input metade">
                                <label>Tipo do 1º Alvará</label>
                                <input type="text" name="tipo_alvara_1" placeholder="Informe o tipo do 1º alvará">
                            </div>

                            <div class="input metade">
                                <label>Vencimento do 1º Alvará</label>
                                <input type="text" name="vencimento_alvara_1" placeholder="Informe o vencimento do 1º alvará" data-mask="00/00/0000">
                            </div>

                            <div class="input metade">
                                <label>Tipo do 2º Alvará</label>
                                <input type="text" name="tipo_alvara_2" placeholder="Informe o tipo do 2º alvará">
                            </div>

                            <div class="input metade">
                                <label>Vencimento do 2º Alvará</label>
                                <input type="text" name="vencimento_alvara_2" placeholder="Informe o vencimento do 2º alvará" data-mask="00/00/0000">
                            </div>

                            <div class="input tudo">
                                <label>Observações</label>
                                <textarea name="observacoes" placeholder="Observações"></textarea>
                            </div>

                            <div class="input"></div>
                       </div>
                   </form>
                </section>
            
            </div>
        </div>
    </body>
    <script type="text/javascript">
        
    </script>
    <script src="../js/jquery.mask.js"></script>
    <script type="text/javascript">
        var empresa = <? echo json_encode($empresa->toArray()) ?>;
    </script>
    <script src="../js/contador/empresa/resumo.js?<? echo time(); ?>"></script>
</html>