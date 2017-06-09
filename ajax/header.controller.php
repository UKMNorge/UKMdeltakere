<?php

$JSON->twigJS = 'twigJSoverviewlistItem';
require_once('UKM/person.class.php');
// Innslag og mønstring er allerede definert her.
#$innslag = new innslag_v2( $_POST['innslag_id'] );
#$monstring = new monstring_v2( get_option('pl_id') );
#$JSON->innslag = data_innslag( $innslag, $monstring);
#$JSON->innslag_id = $innslag->getId();

// Hvis det er oppretting av innslag vil innslag være lik null
if( is_object( $innslag ) ) {
	### Bygg filter-verdier
	$data[] = $innslag->getNavn();
	
	$JSON->innslag->personer = array();
	foreach($innslag->getPersoner()->getAll() as $person) {
		$JSON->innslag->personer[] = data_person( $person );	
		$data[] = $person->getFornavn().' '.$person->getEtternavn(); 
	}
	
	$JSON->type_innslag = $innslag->getType()->getKey();
	$JSON->innslag->kommune = $innslag->getKommune()->getNavn();
	
	if( $innslag->getType()->harTitler() ) {
		foreach( $innslag->getTitler( $monstring )->getAll() as $tittel ) {
			$data[] = $tittel->getTittel();
		}
	} 
	
	$data[] = $innslag->getKontaktperson()->getFornavn().' '.$innslag->getKontaktperson()->getEtternavn();
	
	$JSON->filter = implode(' ', $data);
	
	
	$JSON->innslag_navn = $innslag->getNavn();
	$JSON->innslag_kommune = $innslag->getKommune()->getNavn();
	$JSON->innslag_type = $innslag->getType()->getKey();
	$JSON->innslag_har_titler = $innslag->getType()->harTitler();
	
	if( $innslag->getType()->harTitler() ) {
		$JSON->innslag_personer_antall = $innslag->getPersoner()->getAntall();
		
		if( 'utstilling' == $innslag->getType()->getKey() ) {
			$JSON->titler_antall = $innslag->getTitler( $monstring )->getAntall();
		} else {
			$JSON->titler_varighet = $innslag->getTitler( $monstring )->getVarighet()->getHumanShort();
		}
		
		$JSON->innslag_advarsel_personer_har = $innslag->getAdvarsler( $monstring )->har('personer');
		$JSON->innslag_advarsel_titler_har = $innslag->getAdvarsler( $monstring )->har('titler');
		$JSON->advarsler_personer = [];
		$JSON->advarsler_titler = [];
		foreach( $innslag->getAdvarsler( $monstring )->getAll() as $advarsel ) {
			if( $advarsel->getKategori() == 'personer' ) {
				$JSON->advarsler_personer[] = $advarsel;
			}
			elseif( $advarsel->getKategori() == 'titler' ) {
				$JSON->advarsler_titler[] = $advarsel;
			}
		}
	} else {
		$JSON->person_rolle = $innslag->getPersoner()->getSingle()->getRolle();
		$JSON->person_alder = $innslag->getPersoner()->getSingle()->geAlder();
	}
}