<?php

// Egilea: Iker Ibarguren
// E-Posta: ikerib@gmail.com
// Web: ikerib.github.io

// Naiz Tour jokoa erabiliz Web Scraper adibidea Goutte bidez. 


require_once __DIR__.'/vendor/autoload.php';

use Goutte\Client;

$client = new Client();

$crawler = $client->request('GET', 'http://tourjokoa.naiz.eus/');

// Login formularioa aukeratua
$form = $crawler->selectButton('entrar')->form();

// Formularioa bidali
$crawler = $client->submit($form, array(
    'data[User][username]' => 'ZURE NAIZ TOUR JOKOA ERABILTZAILEA',
    'data[User][password]' => 'ZURE NAIZ TOUR JOKOA PASAHITZA',
));

// taula irakurri
$taula = $crawler->filter('#table  tr  td');

$nirepos ="";
$nirepuntuk="";
$index = 0;

foreach ($taula as $domElement) {

    if ( $index == 0 ) {
        $nirepos = $domElement->nodeValue;
    }
    if ( $index == 5 ) {
        $nirepuntuk = $domElement->nodeValue;
    }
    $index++;
}


// e-posta bidali
$smtp = new Swift_SmtpTransport('SMTP ZERBITZARIA');
// G-Mail erabiltzeko
// $transport = Swift_SmtpTransport::newInstance('smtp.googlemail.com', 465, 'ssl')
//             ->setUsername('G-Mail erabiltzailea')
//             ->setPassword($'G-Mail pasahitza');

$smtp->setUsername('E-posta erabiltzailea');
$smtp->setPassword('E-posta pasahitza');

$mailer = new Swift_Mailer($smtp);

$mezua = sprintf("Nire sailkapen orokorra: %s, puntuak: %s\n", $nirepos, $nirepuntuk);

$message = Swift_Message::newInstance('Naiz Tour Jokoa', $mezua)
    ->setFrom(array('Remite E-posta' => 'Remite izena'))
    ->setTo(array('Helbide E-posta' => 'Helbide E-posta izena'));


if($mailer->send($message) == 1){
    echo 'E-posta ongi bidali da.';
}
else {
    echo 'Arazorenbat egon da e-posta bidaltzerakoan';
}
