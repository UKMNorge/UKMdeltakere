<?php

require_once('UKM/write_innslag.class.php');

// TODO: Sjekk for påkrevd data:
#var_dump($_POST);
#var_dump($DATA);
if(null == $DATA['kontakt']) {
	$kontaktperson = write_person::create($DATA['fornavn'], $DATA['etternavn'], $DATA['mobil'], write_person::fodselsdatoFraAlder($DATA['alder']), $DATA['kommune'] );
	$kontaktperson->setEpost($DATA['epost']);
	$kontaktperson->save();
} else {
	$kontaktperson = new write_person($DATA['kontakt']);
}

$kommune = new kommune( $DATA['kommune'] );
$monstring = new monstring_v2( get_option('pl_id') );
$type = innslag_typer::getByName($DATA['type']);

if( $type->harTitler() ) {
	$navn = $DATA['navn'];
	$kontaktpersonSomDeltaker = false;
	if( isset($DATA['kontaktpersonErMed']) && 'on' == $DATA['kontaktpersonErMed']) {
		$kontaktpersonSomDeltaker = true;
	}
	$sjanger = $DATA['sjanger'];
	$beskrivelse = $DATA['beskrivelse'];
} 
else {
	$navn = $kontaktperson->getNavn();
	$beskrivelse = $DATA['erfaring'];
}

$innslag = write_innslag::create($kommune, $monstring, $type, $navn, $kontaktperson );
$innslag->setBeskrivelse($beskrivelse);

if( $type->harTitler() ) {
	$innslag->setSjanger($sjanger);
	
	if( true == $kontaktpersonSomDeltaker ) {
		if( !$innslag->getPersoner()->leggTil( $kontaktperson ) ) {
			// Lagre innslaget før feilmelding, sånn at sjanger og beskrivelse ikke forsvinner.
			$innslag->save();
			throw new Exception("Klarte ikke å legge til kontaktpersonen i innslaget!");
		}
		$innslag->setRolle($kontaktperson, $DATA['rolle']);
	}
}
// Tittelløs, ie. konferansier, arrangør eller UKM Media:
else {
	if( 'konferansier' == $type->getKey() ) {
		$innslag->setRolle( $kontaktperson, $type->getNavn() );
	} 
	else {
		// UKM Media eller arrangør:
		// STYGG HACK: DATA BURDE SENDE MED ARRAYET.
		$funksjoner = array();
		$mulige = $innslag->getType()->getFunksjoner();
		foreach($_POST['formData'] as $element) {
			if($element['name'] == 'funksjoner[]') {
				$funksjoner[$element['value']] = $mulige[$element['value']];
			}
		}

		$innslag->setRolle($kontaktperson, $funksjoner);
	}
	
	$innslag->getPersoner()->leggTil( $kontaktperson );
}

$innslag->save();

$JSON->innslag_id = $id;
$JSON->type = $type;