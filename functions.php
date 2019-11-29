<?php

use UKMNorge\Innslag\Typer\Type;

/**
 * Lag et TWIGjs-objekt av en tittel
 */
function data_tittel( $tittel ) {
	$data = new stdClass();
	$data->id			= $tittel->getId();
	$data->tittel		= $tittel->getTittel();
	$data->varighet		= $tittel->getVarighet();
	$data->varighet_sek	= $tittel->getVarighet()->getSekunder();
	$data->varighet_human= $tittel->getVarighet()->getHumanShort();
    
    $mightbe = [
        'beskrivelse'       => 'getBeskrivelse',
        'typeogteknikk'     => 'getType',
        'koreografi_av'     => 'getKoreografiAv',
        'instrumental'      => 'erInstrumental',
        'selvlaget'         => 'erSelvlaget',
        'tekst_av'          => 'getTekstAv',
        'melodi_av'         => 'getMelodiAv',
        'lese_opp'          => 'erLesOpp'
    ];

    foreach( $mightbe as $store => $function ) {
        if( method_exists( $tittel, $function) ) {
            $data->$store = $tittel->$function();
        }
    }

	return $data;
}

/**
 * Lag et TWIGjs-objekt av et innslag
 */
function data_innslag( $innslag ) {
	$data = new stdClass();
	$data->id				 			= $innslag->getId();
	$data->navn							= $innslag->getNavn();
	$data->beskrivelse					= $innslag->getBeskrivelse();
	$data->kategori						= $innslag->getKategori();
	$data->sjanger						= $innslag->getSjanger();
	$data->status						= $innslag->getStatus();
	$data->tekniske_behov				= $innslag->getTekniskeBehov();
	
	$data->subscription					= $innslag->getSubscriptionTime();
	$data->avmeldbar					= $innslag->erAvmeldbar();
	$data->avmeldlas					= $innslag->getAvmeldbar();
	
	$data->type                         = data_type( $innslag->getType() );
	
	$data->advarsler	 				= [];
	$data->harPersonAdvarsler	 		= false;
	foreach( $innslag->getAdvarsler() as $advarsel ) {
		$data->advarsler[] 				= data_advarsel( $advarsel );
		if ($advarsel->kategori == 'personer') {
			$data->harPersonAdvarsler	= true;
		}
	}
	
	if( $innslag->getType()->harTid() ) {
		$data->varighet					= $innslag->getTitler()->getVarighet();
		$data->varighet_human			= $innslag->getTitler()->getVarighet()->getHumanShort();
	} 
	
	$data->hendelser					= [];
	
	return $data;
}

function data_type( Type $type ) {
    $data					= new stdClass();
	$data->id		 		= $type->getId();
	$data->key 				= $type->getKey();
    $data->navn	 			= $type->getNavn();
    $data->frist            = $type->getFrist();
    
    $data->erGruppe         = $type->erGruppe();
    $data->erEnkeltperson   = $type->erEnkeltPerson();

    $data->harTid           = $type->harTid();
    $data->harTitler		= $type->harTitler();
    $data->harSjanger       = $type->harSjanger();
    $data->harBeskrivelse   = $type->harBeskrivelse();
	$data->harFunksjoner	= $type->harFunksjoner();
	$data->harTekniskeBehov	= $type->harTekniskeBehov();
    
    $data->har_filmer       = $type->harFilmer();
    $data->har_bilder       = $type->harBilder();
    
    $data->funksjoner		= $type->getFunksjoner();
    $data->tekst            = $type->getAllTekst();
    
    if( $type->harTitler() ) {
        $data->tabell       = $type->getTabell();
    }

    return $data;
}

function data_advarsel( $advarsel ) {
	$data = new stdClass();
	$data->kategori = $advarsel->getKategori();
	$data->melding = $advarsel->getMelding();
	return $data;
}

/**
 * Lag et TWIGjs-objekt av en monstring
 */
function data_monstring( $monstring ) {
	$data = new stdClass();
	$data->type				= $monstring->getType();
	$data->navn				= $monstring->getNavn();
	$data->erFellesmonstring= $monstring->erFellesmonstring();
	if( $monstring->getType() != 'land' ) {
		$data->kommuner			= $monstring->getKommuner()->getKeyValArray();
		$data->fylke			= new stdClass();
		$data->fylke->id		= $monstring->getFylke()->getId();
		$data->fylke->navn		= $monstring->getFylke()->getNavn();
	}
	
	return $data;
}


/**
 * Lag et TWIGjs-objekt av en person
 */
function data_person( $person ) {
	$data = new stdClass();
	$data->id 					= $person->getId();
	$data->fornavn				= $person->getFornavn();
    $data->etternavn			= $person->getEtternavn();
    $data->navn                 = $person->getNavn();
	$data->mobil				= $person->getMobil();
	$data->epost				= $person->getEpost();
	$data->alder_tall			= $person->getAlder('');
	$data->alder				= $person->getAlder();
	$data->kommune_id			= $person->getKommune()->getId();
	$data->kommune_navn			= $person->getKommune()->getNavn();
	$data->valgte_funksjoner	= $person->getRolleObject();
	$data->rolle				= $person->getRolle();
	$data->kjonnspronomen		= $person->getKjonnspronomen();
	return $data;
}

/**
 * Lag et TWIGj-objekt av en hendelse
 */
function data_program( $hendelse ) {
	$data 			= new stdClass();
	$data->id 		= $hendelse->getId();
	$data->navn 	= $hendelse->getNavn();
	$data->start	= $hendelse->getStart();
	
	return $data;
}