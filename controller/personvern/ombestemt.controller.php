<?php

use UKMNorge\Samtykke\Person;

$samtykkePerson = Person::getById( $_GET['id'] );

echo '<pre>';
var_dump($samtykkePerson);
echo '</pre>';
// die;

if( $samtykkePerson->getStatus()->getId() == 'ikke_godkjent' ) { // && !$samtykkePerson->getKommunikasjon()->har('ombestemt') ) {
    $kode = $samtykkePerson->getSvarSamtykke() ? 'ombestemtsamtykkeskjemadelta' : 'ombestemt';

    UKMdeltakere::getFlash()->success(
        'Meldingen ble sendt: '.
        '<div class="card">'.
        nl2br( 
            $samtykkePerson->getKommunikasjon()->sendMelding($kode) 
        ).
        '</div>'
    );
} else {
    UKMdeltakere::getFlash()->error('SMS ble ikke sendt, da samme melding har blitt sendt til mottakeren tidligere.');
}