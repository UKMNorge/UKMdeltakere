<?php

use UKMNorge\Geografi\Fylker;
use UKMNorge\Arrangement\Arrangement;
use UKMNorge\Allergener\Allergener;


// SETUP SENSITIVT-REQUESTER
$requester = new UKMNorge\Sensitivt\Requester(
    'wordpress', 
    wp_get_current_user()->ID,
    get_option('pl_id')
);
UKMNorge\Sensitivt\Sensitivt::setRequester( $requester );

$arrangement = new Arrangement(get_option('pl_id'));

$JSON->nedslagsfelt = $arrangement->getMetaValue('nedslagsfelt');

if( $innslag->getType()->erGruppe() ) {
    $JSON->twigJS = 'form';
	// Kommune hvor innslaget er meldt på:
	$JSON->innslag->kommune_id = $innslag->getKommune()->getId();
} else {
    $JSON->twigJS = 'formtittellos';	
	$person 			        = $innslag->getPersoner()->getSingle();
	$JSON->person 		        = data_person( $person );
	$JSON->erfaring		        = $innslag->getBeskrivelse();
	$JSON->innslag->kommune_id  = $innslag->getKommune()->getId();

	$allergi = $person->getSensitivt( $requester )->getIntoleranse();
	$JSON->person->intoleranse = UKMVideresending::getIntoleransePersonData( $person, $allergi );
	$JSON->person->intoleranse_liste = $JSON->person->intoleranse->intoleranse_liste ? $JSON->person->intoleranse->intoleranse_liste : null;
	$JSON->person->intoleranse_human = $allergi->getListeHuman();
	
	$JSON->allergener_standard = Allergener::getStandard();
    $JSON->allergener_kulturelle = Allergener::getKulturelle();
    
	if( $innslag->getType()->harFunksjoner() ) {
        if( null == $JSON->person->valgte_funksjoner ) {
            $JSON->person->valgte_funksjoner = [];
		}
		$JSON->funksjonsnavn    = $innslag->getType()->getFunksjoner();
		$JSON->funksjoner       = array_keys($JSON->funksjonsnavn);
    }
}

if( $arrangement->getEierType() == 'land' || $arrangement->getMetaValue('nedslagsfelt') == 'land' ) {
	$JSON->fylker = [];
	foreach( Fylker::getAll() as $fylke ) {
		$data = new stdClass();
		$data->id = $fylke->getId();
		$data->navn = $fylke->getNavn();
		$data->kommuner = [];
		
		foreach( $fylke->getKommuner() as $kommune ) {
			$data_kommune = new stdClass();
			$data_kommune->id = $kommune->getId();
			$data_kommune->navn = $kommune->getNavn();
			$data->kommuner[] = $data_kommune;
		}
		
		$JSON->fylker[] = $data;
	}
}

$JSON->type                 = data_type( $innslag->getType() );