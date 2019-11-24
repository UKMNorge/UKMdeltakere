<?php

$JSON->twigJS = 'contactchange';

$JSON->personer = [];
$ids = [];
foreach( $innslag->getPersoner()->getAll() as $person ) {
	$ids[] = $person->getId();
	$JSON->personer[] = data_person( $person );
}

$JSON->kontaktperson = new stdClass();
$JSON->kontaktperson->id = $innslag->getKontaktperson()->getId();
$JSON->kontaktperson->fornavn = $innslag->getKontaktperson()->getFornavn();
$JSON->kontaktperson->etternavn = $innslag->getKontaktperson()->getEtternavn();
$JSON->kontaktperson->deltar = null;	
if( !in_array( $innslag->getKontaktperson()->getId(), $ids ) ) {
	$JSON->personer[] = data_person( $innslag->getKontaktperson() );
	$JSON->kontaktperson->deltar = false;	
}