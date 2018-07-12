<?
require "conexao.php";
require "classes/usuario.class.php";
require_once('vendor/autoload.php');
date_default_timezone_set("America/Sao_Paulo");

$dados = DBescape($_POST);

$result = DBselect("usuario", "where email='{$dados['email']}'", "id, email, nome");

if (count($result)==0) {
    echo json_encode(array('estado'=>0, 'mensagem'=>"Email incorreto!"));
    exit;
}

$result = $result[0];

$novaSenha = gerarsenha(8);

$mensagem = file_get_contents("../html/emailGeral.html");

$mensagem2 = "Olá segue abaixo sua senha temporária solicitada em ".date("d/m/Y, H:i", time()).":";
$mensagem2 .= "<br>";
$mensagem2 .= "Senha: {$novaSenha}";

$mensagem = str_replace("--MENSAGEM--", $mensagem2, $mensagem);

$mail = new PHPMailer;

$mail->SMTPOptions = array(
    'ssl' => array(
        'verify_peer' => false,
        'verify_peer_name' => false,
        'allow_self_signed' => true
    )
);

$mail->addAddress($result['email'], $result['nome']);

$mail->SMTPDebug = 0;                            // Enable verbose debug output

$mail->isSMTP();                                      // Set mailer to use SMTP
$mail->Host = 'br342.hostgator.com.br';  // Specify main and backup SMTP servers
$mail->SMTPAuth = true;                               // Enable SMTP authentication
$mail->Username = 'recuperasenha@damacash.com';                 // SMTP username
$mail->Password = 'tinhofiel500500';                           // SMTP password
$mail->SMTPSecure = 'ssl';                            // Enable TLS encryption, `ssl` also accepted
$mail->Port = 465;                                    // TCP port to connect to

$mail->setFrom('recuperasenha@damacash.com', 'Dama Cash');

$mail->DEBUG = 0;
$mail->Subject = 'Recuperar Senha - Dama Cash';
$mail->isHTML(true);
$mail->Body = $mensagem;
$mail->CharSet = 'UTF-8';

if (!$mail->send()) {
    echo 'Message could not be sent.<pre>';
    echo $mail->ErrorInfo;
} else {
    $usuario = new Usuario();
    $usuario->id=$result['id'];
    $usuario->senhaTemporaria = $novaSenha;
    $usuario->trocarSenha = 1;
    $usuario->atualizar();
    
    $mail->ClearAllRecipients();
}

echo json_encode(array('estado'=>1, 'mensagem'=>"Verifique seu email, enviamos uma senha temporária para que você possa alterar sua senha!"));
exit;

function gerarSenha ($num_caracteres = 8 ) {
 
    $password = "";

    // variável para definir quais o caractéres possíveis para a password
    $possiveis = "12346789abcdfghjkmnpqrtvwxyzABCDFGHJKLMNPQRTVWXYZ";

    // para verificar quantos caractéres diferentes existem para gerar uma password
    $max = strlen($possiveis);

    // a password não pode ser ter mais caractéres do que os que foram predefinidos para $possiveis    
    if ($num_caracteres> $max) {

        $num_caracteres= $max;

    }

    // variável de incrementação para saber quantos caratéres já tem a password enquanto está a ser gerada
    $i = 0; 

    // adiciona caracteres a $password até $num_caracteres estar completo    
    while ($i < $num_caracteres) { 

        // escolhe um caracter dos possiveis
        $char = substr($possiveis, mt_rand(0, $max-1), 1);

        // verificar se o caracter escolhido já está na $password?
        if (!strstr($password, $char)) { 

            // se não estiver incluido adiciona o novo caracter...         
            $password .= $char;

            // ... e incrementa a variável $i        
            $i++;

        }

    }

    // Feito!
    return $password;
}
?>