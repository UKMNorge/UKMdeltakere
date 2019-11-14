<?php

use UKMNorge\Samtykke\Person;

$samtykke = Person::getById( $_GET['id'] );
$message =  new stdClass();

/**
 * Vi dealer med foresatte
**/
if( isset( $_GET['foresatt'] ) && $_GET['foresatt'] == 'true' ) {
	if( $samtykke->getForesatt()->getStatus()->getId() == 'ikke_sendt' ) {
		$message->level = 'success';
		$message->header = 'SMS sendt';
        $message->body = 
            'Meldingen som ble sendt: '.
            '<div class="card">'.
            nl2br( 
                $samtykke->getKommunikasjon()->sendMelding('samtykke_foresatt') 
            ).
            '</div>';
    } else {
        $message->level = 'warning';
        $message->header = 'SMS sendt tidligere';
        $message->body = 'SMS ble ikke sendt, da samme melding har blitt sendt til mottakeren tidligere.';
    }
}
/**
 * Vi dealer med deltakere
**/
else {
    if( $samtykke->getStatus()->getId() == 'ikke_sendt' ) {
        $message->level = 'success';
        $message->header = 'SMS sendt!';
        $message->body = 
            'Meldingen som ble sendt: '.
            '<div class="card">'.
            nl2br( 
                $samtykke->getKommunikasjon()->sendMelding('samtykke') 
            ).
            '</div>';
    } else {
        $message->level = 'warning';
        $message->header = 'SMS sendt tidligere';
        $message->body = 'SMS ble ikke sendt, da samme melding har blitt sendt til mottakeren tidligere.';
    }
}

$TWIGdata['message'] = $message;
$VIEW = 'personvern/liste';