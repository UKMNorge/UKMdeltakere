<?php

use UKMNorge\Samtykke\Person;

$samtykke = Person::getById( $_GET['id'] );
$message =  new stdClass();

if( $samtykke->getStatus()->getId() == 'ikke_godkjent' && !$samtykke->getKommunikasjon()->har('ombestemt') ) {
    $message->level = 'success';
    $message->header = 'SMS sendt!';
    $message->body = 
        'Meldingen som ble sendt: '.
        '<div class="card">'.
        nl2br( 
            $samtykke->getKommunikasjon()->sendMelding('ombestemt') 
        ).
        '</div>';
} else {
    $message->level = 'warning';
    $message->header = 'SMS sendt tidligere';
    $message->body = 'SMS ble ikke sendt, da samme melding har blitt sendt til mottakeren tidligere.';
}

$TWIGdata['message'] = $message;
$VIEW = 'personvern/liste';