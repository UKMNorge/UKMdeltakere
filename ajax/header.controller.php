<?php

$JSON->twigJS = 'twigJSinnslagheader';
require_once('UKM/person.class.php');
// Innslag og mønstring er allerede definert her.
#$innslag = new innslag_v2( $_POST['innslag_id'] );
#$monstring = new monstring_v2( get_option('pl_id') );
#$JSON->innslag = data_innslag( $innslag, $monstring);
#$JSON->innslag_id = $innslag->getId();

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