<?php

use UKMNorge\Innslag\Innslag;
use UKMNorge\OAuth2\HandleAPICall;
use UKMNorge\Innslag\Nominasjon\Nominasjon;
use UKMNorge\Innslag\Nominasjon\Write as WriteNominasjon;


$handleCall = new HandleAPICall(['innslag_id', 'nominasjon_id', 'godkjent_value'], [], ['GET', 'POST'], false);

$innslagId = $handleCall->getArgument('innslag_id');
$nominasjonId = $handleCall->getArgument('nominasjon_id');
$godkjentValue = $handleCall->getArgument('godkjent_value');

try{
    $innslag = Innslag::getById($innslagId);
    $nominasjon = Nominasjon::getById($nominasjonId, $innslag->getType());
    $nominasjon->setGodkjent($godkjentValue == 'true');

    WriteNominasjon::saveGodkjent($nominasjon);

}catch(Exception $e) {
    throw $e;
}

$JSON->redirect = '?page=UKMdeltakere';