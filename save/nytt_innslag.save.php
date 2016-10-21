<?php

require_once('UKM/write_innslag.class.php');

// TODO: Sjekk for påkrevd data:
#var_dump($_POST);
var_dump($DATA);
if(null == $DATA['kontakt']) {
	throw new Exception("Oppretting av kontaktperson er ikke implementert.");
} else {
	$kontaktperson = new write_person($DATA['kontakt']);
}
$kommune = new kommune( $DATA['kommune'] );
$monstring = new monstring_v2( get_option('pl_id') );
$type = innslag_typer::getByName($DATA['type']);
$navn = $DATA['navn'];

throw new Exception("Not implemented!");

$id = write_innslag::create($kommune->getId(), $monstring->getId(), $type, $navn, $contact );