<?php


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
	$data->beskrivelse	= $tittel->getBeskrivelse();
	$data->typeogteknikk= $tittel->getType();
		
	$data->koreografi_av= $tittel->getKoreografiAv();
	$data->instrumental = $tittel->erInstrumental();
	$data->selvlaget	= $tittel->erSelvlaget();
	$data->tekst_av		= $tittel->getTekstAv();
	$data->melodi_av	= $tittel->getMelodiAv();
	$data->lese_opp		= $tittel->getLitteraturLesOpp();

	return $data;
}

/**
 * Lag et TWIGjs-objekt av et innslag
 */
function data_innslag( $innslag, $monstring ) {
	$data = new stdClass();
	$data->id	 		= $innslag->getId();
	$data->navn			= $innslag->getNavn();
	$data->beskrivelse	= $innslag->getBeskrivelse();
	$data->kategori		= $innslag->getKategori();
	$data->sjanger		= $innslag->getSjanger();
	$data->status		= $innslag->getStatus();
	$data->tekniske_behov= $innslag->getTekniskeBehov();
	
	$data->subscription	= $innslag->getSubscriptionTime();
	$data->avmeldbar	= $innslag->erAvmeldbar();
	$data->avmeldlas	= $innslag->getAvmeldbar();
	
	$data->type			= new stdClass();
	$data->type->id 	= $innslag->getType()->getId();
	$data->type->key 	= $innslag->getType()->getKey();
	$data->type->navn 	= $innslag->getType()->getNavn();
	$data->type->harTitler = $innslag->getType()->harTitler();
	$data->type->harTekniskeBehov = $innslag->getType()->harTekniskeBehov();
	
	$data->advarsler = [];
	$data->harPersonAdvarsler = false;
	foreach( $innslag->getAdvarsler( $monstring ) as $advarsel ) {
		$data->advarsler[] = data_advarsel( $advarsel );
		if ($advarsel->kategori == 'personer') {
			$data->harPersonAdvarsler = true;
		}
	}
	
	if( $innslag->getType()->harTitler() ) {
		$data->varighet		= $innslag->getTitler( $monstring )->getVarighet();
		$data->varighet_human= $innslag->getTitler( $monstring )->getVarighet()->getHumanShort();
	} 
	
	$data->hendelser	= [];
	
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
	$data->kommuner			= $monstring->getKommuner()->getKeyValArray();
	$data->fylke			= new stdClass();
	$data->fylke->id		= $monstring->getFylke()->getId();
	$data->fylke->navn		= $monstring->getFylke()->getNavn();
	
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