<?php

use UKMNorge\Samtykke\Person;

$samtykke = Person::getById( $_GET['id'] );
$message =  new stdClass();

/**
 * Vi dealer med foresatte
**/
if( isset( $_GET['foresatt'] ) && $_GET['foresatt'] == 'true' ) {
	if( in_array( $samtykke->getForesatt()->getStatus()->getId(), ['ikke_sett', 'ikke_svart'] ) ) {
		$message->level = 'success';
		$message->header = 'Purre-SMS sendt! <img src="//ico.ukm.no/leek-32.png" height="32" />';
        $message->body = 
            'Meldingen som ble sendt: '.
            '<div class="card">'.
            nl2br( 
                $samtykke->getKommunikasjon()->sendMelding('purring_foresatt') 
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
	if( in_array( $samtykke->getStatus()->getId(), ['ikke_sett', 'ikke_svart'] ) ) {
		$message->level = 'success';
		$message->header = 'Purre-SMS sendt! <img src="//ico.ukm.no/leek-32.png" height="32" />';
        $message->body = 
            'Meldingen som ble sendt: '.
            '<div class="card">'.
            nl2br( 
                $samtykke->getKommunikasjon()->sendMelding('purring_deltaker') 
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