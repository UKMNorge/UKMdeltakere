<?php

use UKMNorge\Geografi\Kommune;
use UKMNorge\Innslag\Personer\Person;
use UKMNorge\Innslag\Personer\Write as WritePerson;
use UKMNorge\Innslag\Typer\Typer;
use UKMNorge\Innslag\Write as WriteInnslag;

require_once('UKM/Autoloader.php');

// Opprett eller velg kontaktperson
if(null == $DATA['kontakt']) {
	$kontaktperson = WritePerson::create(
		$DATA['fornavn'],
		$DATA['etternavn'],
		(Int) $DATA['mobil'],
		new Kommune($DATA['kommune'])
    );
    $kontaktperson->setFodselsdato(
        WritePerson::fodselsdatoFraAlder($DATA['alder'])
    );
	$kontaktperson->setEpost($DATA['epost']);
    
    WritePerson::save( $kontaktperson );
} else {
	$kontaktperson = new Person($DATA['kontakt']);
}
$kontaktpersonSomDeltaker = isset($DATA['kontaktpersonErMed']) && 'on' == $DATA['kontaktpersonErMed'];

// Hvilken kommune er innslaget fra
$kommune = new Kommune( $DATA['kommune'] );
$type = Typer::getByName($DATA['type']);

// Innslag med titler
if( $type->erGruppe() ) {
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
$innslag = WriteInnslag::create($kommune, $monstring, $type, $navn, $kontaktperson );
$innslag->setBeskrivelse($beskrivelse);
$innslag->setSjanger($sjanger);
$innslag->setStatus(8);
WriteInnslag::save( $innslag );

// Legg til kontaktperson og håndter evt feilmelding
// For tittel-innslag hvor kontaktpersonen deltar, eller tittelløse innslag (hvor kontaktpersonen alltid deltar)
if( $kontaktpersonSomDeltaker || !$type->harTitler() ) {
	if( !$innslag->getPersoner()->leggTil( $kontaktperson ) ) {
		throw new Exception("Klarte ikke å legge til kontaktpersonen i innslaget!");
	}
	WriteInnslag::savePersoner( $innslag );
}

// Innslag med titler
if( $type->erGruppe() ) {
	$kontaktperson->setRolle( $DATA['rolle'] );
}
// Enkeltperson-innslag med funksjoner (jobbe med UKM)
elseif( $type->harFunksjoner() ) {
    $funksjoner = array();
    $mulige = $innslag->getType()->getFunksjoner();
    foreach($_POST['formData'] as $element) {
        if($element['name'] == 'funksjoner[]') {
            $funksjoner[$element['value']] = $mulige[$element['value']];
        }
    }
    $kontaktperson->setRolle($funksjoner);
}
// Enkeltperson-innslag uten funksjoner (konferansier, f.eks)
else {
    $kontaktperson->setRolle( $type->getNavn() );
}

WriteInnslag::save( $innslag );
WritePerson::save( $kontaktperson );
if( $kontaktpersonSomDeltaker || !$type->harTitler() ) {
	WritePerson::saveRolle( $kontaktperson );
}
$innslag->setStatus(8);
WriteInnslag::saveStatus( $innslag );

$JSON->innslag_id = $innslag->getId();
$JSON->type = $type;

$JSON->redirect = '?page=UKMdeltakere&list=fullstendig&edit='. $JSON->innslag_id .'#innslag_'. $JSON->innslag_id;