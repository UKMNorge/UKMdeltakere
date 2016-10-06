<?php

$JSON->twigJS = 'twigJScontactchange';

$JSON->personer = [];
$ids = [];
foreach( $innslag->getPersoner()->getAll() as $person ) {
	$ids[] = $person->getId();
	$JSON->personer[] = data_person( $person );
}

if( !in_array( $innslag->getKontaktperson()->getId(), $ids ) ) {
	$JSON->personer[] = data_person( $innslag->getKontaktperson() );
}

$JSON->kontaktperson = $innslag->getKontaktperson()->getId();