<?php


/**
 * Lag et TWIGjs-objekt av en tittel
 */
function data_tittel( $tittel ) {
	$data = new stdClass();
	$data->id			= $tittel->getId();
	$data->tittel		= $tittel->getTittel();
	$data->varighet		= $tittel->getVarighet();
	$data->varighet_human= $tittel->getVarighet()->getHumanShort();
	$data->parentes		= 'test';#$tittel->getParentes();

	return $data;
}

/**
 * Lag et TWIGjs-objekt av et innslag
 */
function data_innslag( $innslag ) {
	$data = new stdClass();
	$data->id	 		= $innslag->getId();
	$data->navn			= $innslag->getNavn();
	$data->beskrivelse	= $innslag->getBeskrivelse();
	$data->kategori		= $innslag->getKategori();
	$data->sjanger		= $innslag->getSjanger();
	
	$data->type			= new stdClass();
	$data->type->id 	= $innslag->getType()->getId();
	$data->type->key 	= $innslag->getType()->getKey();
	$data->type->navn 	= $innslag->getType()->getNavn();
	
	$data->hendelser	= [];
	
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
	$data->valgte_funksjoner	= $person->getInstrumentObject();
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