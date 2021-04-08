<?php

use UKMNorge\Samtykke\Person;

$samtykke = Person::getById( $_GET['id'] );

/**
 * Vi dealer med foresatte
**/
if( isset( $_GET['foresatt'] ) && $_GET['foresatt'] == 'true' ) {
	if( in_array( $samtykke->getForesatt()->getStatus()->getId(), ['ikke_sett', 'ikke_svart'] ) ) {
		UKMdeltakere::getFlash()->success(
            'Purre-melding ble sendt: '.
            '<div class="card">'.
            nl2br( 
                $samtykke->getKommunikasjon()->sendMelding('purring_foresatt') 
            ).
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
	if( in_array( $samtykke->getStatus()->getId(), ['ikke_sett', 'ikke_svart'] ) ) {
		UKMdeltakere::getFlash()->success( 
            'Purre-melding ble sendt: '.
            '<div class="card">'.
            nl2br( 
                $samtykke->getKommunikasjon()->sendMelding('purring_deltaker') 
            ).
            '</div>'
        );
    } else {
        UKMdeltakere::getFlash()->error(
            'SMS ble ikke sendt, da samme melding har blitt sendt til mottakeren tidligere.'
        );
	}
}