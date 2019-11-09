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

WriteInnslag::save( $innslag );
WritePerson::save( $kontaktperson );
if( $kontaktpersonSomDeltaker || !$type->harTitler() ) {
	WritePerson::saveRolle( $kontaktperson );
}

// Hvis vi legger til innslaget på fylkesmønstring eller festival - videresend det!
if( $monstring->getType() != 'kommune' ) {

	// HVIS LAND, LEGG TIL PÅ FYLKESNIVÅ FØRST
	if( $monstring->getType() == 'land' ) {
		require_once('UKM/monstringer.collection.php');
		$monstring_fylke = monstringer_v2::fylke( $innslag->getFylke(), $monstring->getSesong() );
		// Videresend innslaget til nåværende mønstring
		$monstring_fylke->getInnslag()->leggTil( $innslag );
		WriteInnslag::leggTil( $innslag );
		
		if( $kontaktpersonSomDeltaker ) {
			// Hent ut innslaget på nytt (ettersom personen er på lokalnivå vil vedkommende hentes av get())
			$innslag_fylke = $monstring_fylke->getInnslag()->get( $innslag->getId() );
			// Reset personerCollection for å nullstille context-objektet
			$innslag_fylke->resetPersonerCollection();
			// Legg til personen i collection
			$kontaktperson_fylke = $innslag_fylke->getPersoner()->get( $kontaktperson->getId() );
			// Legg til personen på fylkesnivå (litt knotete, men må gjøres for videresendingen)
			WritePerson::leggTil( $kontaktperson_fylke );
		}
	}
	
	// HVIS LAND: VIDERESEND FRA FYLKE TIL LAND
	// HVIS FYLKE: VIDERESEND FRA KOMMUNE TIL LAND
	
	// Videresend innslaget til nåværende mønstring
	$monstring->getInnslag()->leggTil( $innslag );
	WriteInnslag::leggTil( $innslag );
	
	if( $kontaktpersonSomDeltaker ) {
		// Hent ut innslaget på nytt (ettersom personen er på lokalnivå vil vedkommende hentes av get())
		$innslag_fylke = $monstring->getInnslag()->get( $innslag->getId() );
		// Reset personerCollection for å nullstille context-objektet
		$innslag_fylke->resetPersonerCollection();
		// Legg til personen i collection
		$kontaktperson_fylke = $innslag_fylke->getPersoner()->get( $kontaktperson->getId() );
		// Legg til personen på fylke-/landsnivå (litt knotete, men må gjøres for videresendingen)
		WritePerson::leggTil( $kontaktperson_fylke );
	}
}

$JSON->innslag_id = $innslag->getId();
$JSON->type = $type;

$JSON->redirect = '?page=UKMdeltakere&list=fullstendig&edit='. $JSON->innslag_id .'#innslag_'. $JSON->innslag_id;