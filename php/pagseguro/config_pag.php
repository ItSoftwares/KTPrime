<?php 

//Config SANDBOX or PRODUCTION environment
$SANDBOX_ENVIRONMENT = false;

$PAGSEGURO_API_URL = 'https://ws.pagseguro.uol.com.br/v2';
$PAGSEGURO_TOKEN = '05A1553542694AF9863647D064858863';

if($SANDBOX_ENVIRONMENT){
    $PAGSEGURO_API_URL = 'https://ws.sandbox.pagseguro.uol.com.br/v2';
    $PAGSEGURO_TOKEN = 'CD61676F213B4BCC9DEC4810278FE05A';
}

$PAGSEGURO_EMAIL = 'karina@ktprime.com.br';
?>