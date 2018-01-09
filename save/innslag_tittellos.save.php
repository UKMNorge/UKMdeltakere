<?php

require_once('UKM/write_innslag.class.php');
require_once('UKM/write_person.class.php');

/**
 * Lagrer et tittelløst innslag
 * 
 *
 *
 */

# Konferansier: Fornavn, etternavn, alder, mobil, epost, kommune, erfaring.
# UKM Media: Fornavn, etternavn, alder, mobil, epost, kommune, erfaring, rolle.
# Arrangører: Fornavn, etternavn, alder, mobil, epost, kommune, erfaring, rolle.

#var_dump($_POST);

$innslag = new write_innslag($_POST['innslag']);

/** OPPDATER PERSONDATA **/
$person = $innslag->getKontaktperson(); // Vi får en write_person fra write_innslag.
$person->setFornavn( $DATA['fornavn'] );
$person->setEtternavn( $DATA['etternavn'] );
#var_dump($DATA['alder']);
#var_dump( write_person::fodselsdatoFraAlder( $DATA['alder'] ) );
$person->setFodselsdato( write_person::fodselsdatoFraAlder( $DATA['alder'] ) );
$person->setMobil( $DATA['mobil'] );
$person->setEpost( $DATA['epost'] );

$person->save();

/** OPPDATER INNSLAGSDATA **/
$innslag->setNavn( $DATA['fornavn'] . ' ' . $DATA['etternavn'] );
$innslag->setBeskrivelse( $DATA['erfaring'] );

// UKM Media eller arrangør:
if( in_array($innslag->getType()->getKey(), array('nettredaksjon', 'arrangor') ) ) {
	// STYGG HACK: DATA BURDE SENDE MED ARRAYET.
	$funksjoner = array();
	$mulige = $innslag->getType()->getFunksjoner();
	foreach($_POST['formData'] as $element) {
		if($element['name'] == 'funksjoner[]') {
			$funksjoner[$element['value']] = $mulige[$element['value']];
		}
	}

	$innslag->setRolle($person, $funksjoner);
}

$innslag->setKommune( $DATA['kommune'] );
$innslag->save();
#var_dump($innslag);
