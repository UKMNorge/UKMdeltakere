<?php

use UKMNorge\Samtykke\Person;

$samtykke = Person::getById( $_GET['id'] );

if( $samtykke->getStatus()->getId() == 'ikke_godkjent' ) { // && !$samtykke->getKommunikasjon()->har('ombestemt') ) {
    UKMdeltakere::getFlash()->success(
        'Meldingen ble sendt: '.
        '<div class="card">'.
        nl2br( 
            $samtykke->getKommunikasjon()->sendMelding('ombestemt') 
        ).
        '</div>'
    );
} else {
    UKMdeltakere::getFlash()->error('SMS ble ikke sendt, da samme melding har blitt sendt til mottakeren tidligere.');
}