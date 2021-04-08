<?php

use UKMNorge\Samtykke\Person;

$samtykke = Person::getById($_GET['id']);
$message =  new stdClass();

/**
 * Vi dealer med foresatte
 **/
if (isset($_GET['foresatt']) && $_GET['foresatt'] == 'true') {
    if ($samtykke->getForesatt()->getStatus()->getId() == 'ikke_sendt') {
        UKMdeltakere::getFlash()->success(
            'Meldingen ble sendt: ' .
                '<div class="card">' .
                nl2br(
                    $samtykke->getKommunikasjon()->sendMelding('samtykke_foresatt')
                ) .
                '</div>'
        );
    } else {
        UKMdeltakere::getFlash()->error(
            'SMS ble ikke sendt, da samme melding har blitt sendt til mottakeren tidligere.'
        );
    }
}
/**
 * Vi dealer med deltakere
**/
else {
    if ($samtykke->getStatus()->getId() == 'ikke_sendt') {
        UKMdeltakere::getFlash()->success(
            'Meldingen som ble sendt: ' .
                '<div class="card">' .
                nl2br(
                    $samtykke->getKommunikasjon()->sendMelding('samtykke')
                ) .
                '</div>'
        );
    } else {
        UKMdeltakere::getFlash()->error(
            'SMS ble ikke sendt, da samme melding har blitt sendt til mottakeren tidligere.'
        );
    }
}