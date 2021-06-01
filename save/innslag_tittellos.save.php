<?php

use UKMNorge\Innslag\Write;
use UKMNorge\Innslag\Personer\Write as WritePerson;

require_once('UKM/Autoloader.php');

$innslag = $monstring->getInnslag()->get( $_POST['innslag'], true );

/** OPPDATER PERSONDATA **/
$person = $innslag->getPersoner()->getSingle();
$person->setFornavn( $DATA['fornavn'] );
$person->setEtternavn( $DATA['etternavn'] );
$person->setFodselsdato( WritePerson::fodselsdatoFraAlder( $DATA['alder'] ) );
$person->setMobil( $DATA['mobil'] );
$person->setEpost( $DATA['epost'] );

// UKM Media eller arrangør:
if( $innslag->getType()->harFunksjoner() ) {
	$funksjoner = array();
	foreach($_POST['formData'] as $element) {
		if($element['name'] == 'funksjoner[]') {
			$funksjoner[$element['value']] = $innslag->getType()->getTekst( $element['value'] );
		}
	}

	$person->setRolle( $funksjoner );
}

/** OPPDATER INNSLAGSDATA **/
$innslag->setNavn( $DATA['fornavn'] . ' ' . $DATA['etternavn'] );
if( $innslag->getType()->harBeskrivelse() ) {
    $innslag->setBeskrivelse( $DATA['erfaring'] );
}
$innslag->setKommune( $DATA['kommune'] );

$innslag->setArrangorKommentar($DATA['arrangor_kommentar']);

WritePerson::save( $person );
WritePerson::saveRolle( $person );
Write::save( $innslag );