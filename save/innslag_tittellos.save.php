<?php

require_once('UKM/write_innslag.class.php');
require_once('UKM/write_person.class.php');

$innslag = $monstring->getInnslag()->get( $_POST['innslag'], true );

/** OPPDATER PERSONDATA **/
$person = $innslag->getPersoner()->getSingle();
$person->setFornavn( $DATA['fornavn'] );
$person->setEtternavn( $DATA['etternavn'] );
$person->setFodselsdato( write_person::fodselsdatoFraAlder( $DATA['alder'] ) );
$person->setMobil( $DATA['mobil'] );
$person->setEpost( $DATA['epost'] );

// UKM Media eller arrangør:
if( in_array($innslag->getType()->getKey(), array('nettredaksjon', 'arrangor') ) ) {
	$funksjoner = array();
	$mulige = $innslag->getType()->getFunksjoner();
	foreach($_POST['formData'] as $element) {
		if($element['name'] == 'funksjoner[]') {
			$funksjoner[$element['value']] = $mulige[$element['value']];
		}
	}

	$person->setRolle( $funksjoner );
}

/** OPPDATER INNSLAGSDATA **/
$innslag->setNavn( $DATA['fornavn'] . ' ' . $DATA['etternavn'] );
$innslag->setBeskrivelse( $DATA['erfaring'] );
$innslag->setKommune( $DATA['kommune'] );

write_person::save( $person );
write_person::saveRolle( $person );
write_innslag::save( $innslag );