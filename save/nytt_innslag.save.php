<?php

require_once('UKM/write_innslag.class.php');

// TODO: Sjekk for påkrevd data:
#var_dump($_POST);
#var_dump($DATA);
if(null == $DATA['kontakt']) {
	throw new Exception("Oppretting av kontaktperson er ikke implementert. Data:" . var_export($DATA, true) );
} else {
	$kontaktperson = new write_person($DATA['kontakt']);
}

$kommune = new kommune( $DATA['kommune'] );
$monstring = new monstring_v2( get_option('pl_id') );
$type = innslag_typer::getByName($DATA['type']);
$navn = $DATA['navn'];
$sjanger = $DATA['sjanger'];
$beskrivelse = $DATA['beskrivelse'];

$kontaktpersonSomDeltaker = false;
if( isset($DATA['kontaktpersonErMed']) && 'on' == $DATA['kontaktpersonErMed']) {
	$kontaktpersonSomDeltaker = true;
}

$id = write_innslag::create($kommune, $monstring, $type, $navn, $kontaktperson );
if( !is_numeric($id) ) {
	throw new Exception("Klarte ikke å opprette nytt innslag.");
}

$innslag = new write_innslag($id);
$innslag->setSjanger($sjanger);
$innslag->setBeskrivelse($beskrivelse);
if( true == $kontaktpersonSomDeltaker ) {
	if( !$innslag->getPersoner()->leggTil( $kontaktperson ) ) {
		// Lagre innslaget før feilmelding, sånn at sjanger og beskrivelse ikke forsvinner.
		$innslag->save();
		throw new Exception("Klarte ikke å legge til kontaktpersonen i innslaget!");
	}
}
$innslag->save();

$JSON->innslag_id = $id;
$JSON->type = $type;