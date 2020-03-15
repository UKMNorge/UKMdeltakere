<?php

$JSON->twigJS = 'innslagheader';

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
		foreach( $innslag->getTitler()->getAll() as $tittel ) {
			$data[] = $tittel->getTittel();
		}
	} 
	
	$data[] = $innslag->getKontaktperson()->getFornavn().' '.$innslag->getKontaktperson()->getEtternavn();
	
	$JSON->filter = implode(' ', $data);
	
    $JSON->innslag_navn = $innslag->getNavn();
    if( $innslag->getNavn() == 'Innslag uten navn' ) {
        $JSON->innslag_navn_kontakt = $innslag->getKontaktperson()->getNavn();
    }
	$JSON->innslag_kommune = $innslag->getKommune()->getNavn();
	$JSON->innslag_fylke = $innslag->getFylke()->getNavn();
	$JSON->innslag_type = $innslag->getType()->getKey();
	$JSON->innslag_har_titler = $innslag->getType()->harTitler();
	$JSON->monstring_type = $monstring->getType();
	
	if( $innslag->getType()->erGruppe() ) {
		if( $monstring->getType() == 'kommune' ) {
			$JSON->innslag_personer_antall = $innslag->getPersoner()->getAntall();
		} else {
			$JSON->innslag_personer_antall = $innslag->getPersoner()->getAntallVideresendt();
        }
        
		if( $innslag->getType()->harTid() ) {
			$JSON->titler_varighet = $innslag->getTitler()->getVarighet()->getHumanShort();
        } else {
			$JSON->titler_antall = $innslag->getTitler()->getAntall();
		}
		
		$JSON->innslag_advarsel_personer_har = $innslag->getAdvarsler()->har('personer');
		$JSON->innslag_advarsel_titler_har = $innslag->getAdvarsler()->har('titler');
		$JSON->advarsler_personer = [];
		$JSON->advarsler_titler = [];
		foreach( $innslag->getAdvarsler()->getAll() as $advarsel ) {
			if( $advarsel->getKategori() == 'personer' ) {
				$JSON->advarsler_personer[] = $advarsel;
			}
			elseif( $advarsel->getKategori() == 'titler' ) {
				$JSON->advarsler_titler[] = $advarsel;
			}
		}
	} else {
		$JSON->person_rolle = $innslag->getPersoner()->getSingle()->getRolle();
		$JSON->person_alder = $innslag->getPersoner()->getSingle()->getAlder();
	}
}