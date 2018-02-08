<?php
require_once('UKM/write_monstring.class.php');
require_once('UKM/write_innslag.class.php');
require_once('UKM/write_person.class.php');

// Opprett eller velg kontaktperson
if(null == $DATA['kontakt']) {
	$kontaktperson = write_person::create(
		$DATA['fornavn'],
		$DATA['etternavn'],
		$DATA['mobil'],
		write_person::fodselsdatoFraAlder($DATA['alder']),
		$DATA['kommune']
	);
	$kontaktperson->setEpost($DATA['epost']);
	write_person::save( $kontaktperson );
} else {
	$kontaktperson = new person_v2($DATA['kontakt']);
}
$kontaktpersonSomDeltaker = isset($DATA['kontaktpersonErMed']) && 'on' == $DATA['kontaktpersonErMed'];

// Hvilken kommune er innslaget fra
$kommune = new kommune( $DATA['kommune'] );
$fra_monstring = new kommune_monstring_v2( $kommune->getId(), $monstring->getSesong() );
$fra_monstring = $fra_monstring->monstring_get();
$type = innslag_typer::getByName($DATA['type']);

// Innslag med titler
if( $type->harTitler() ) {
	$navn = $DATA['navn'];
	$sjanger = $DATA['sjanger'];
	$beskrivelse = $DATA['beskrivelse'];
	$kategori = isset( $DATA['kategori'] ) ? $DATA['kategori'] : '';
} 
// Tittelløse innslag (jobbe med UKM)
else {
	$navn = $kontaktperson->getNavn();
	$beskrivelse = $DATA['erfaring'];
	$sjanger = '';
}

// Opprett innslaget
$innslag = write_innslag::create($kommune, $fra_monstring, $type, $navn, $kontaktperson );
$innslag->setBeskrivelse($beskrivelse);
$innslag->setSjanger($sjanger);
$innslag->setStatus(8);
write_innslag::save( $innslag );

// Legg til kontaktperson og håndter evt feilmelding
// For tittel-innslag hvor kontaktpersonen deltar, eller tittelløse innslag (hvor kontaktpersonen alltid deltar)
if( $kontaktpersonSomDeltaker || !$type->harTitler() ) {
	if( !$innslag->getPersoner()->leggTil( $kontaktperson ) ) {
		throw new Exception("Klarte ikke å legge til kontaktpersonen i innslaget!");
	}
	write_innslag::savePersoner( $innslag );
}


// Innslag med titler
if( $type->harTitler() ) {
	$kontaktperson->setRolle( $DATA['rolle'] );
}
// Tittelløse innslag (jobbe med UKM)
else {
	if( 'konferansier' == $type->getKey() ) {
		$kontaktperson->setRolle( $type->getNavn() );
	} 
	else {
		// UKM Media eller arrangør:
		$funksjoner = array();
		$mulige = $innslag->getType()->getFunksjoner();
		foreach($_POST['formData'] as $element) {
			if($element['name'] == 'funksjoner[]') {
				$funksjoner[$element['value']] = $mulige[$element['value']];
			}
		}
		$kontaktperson->setRolle($funksjoner);
	}
	
}

write_innslag::save( $innslag );
write_person::save( $kontaktperson );
if( $kontaktpersonSomDeltaker ) {
	write_person::saveRolle( $kontaktperson );
}

// Hvis vi legger til innslaget på fylkesmønstring - videresend det!
if( $monstring->getType() == 'fylke' ) {
	$monstring->getInnslag()->leggTil( $innslag );
	write_innslag::leggTil( $innslag );
}

$JSON->innslag_id = $innslag->getId();
$JSON->type = $type;

$JSON->redirect = '?page=UKMdeltakere&list=fullstendig&edit='. $JSON->innslag_id .'#innslag_'. $JSON->innslag_id;