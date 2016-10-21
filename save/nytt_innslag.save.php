<?php

require_once('UKM/write_innslag.class.php');

// TODO: Sjekk for påkrevd data:
#var_dump($_POST);
var_dump($DATA);

throw new Exception("Not implemented!");
$kommune = new kommune( $DATA['kommune'] );
$monstring = new monstring_v2( get_option('pl_id') );
$type = innslag_typer::getByName($DATA['type']);
$navn = $DATA['navn'];
$contact = new write_person($DATA['kontaktperson']);

$id = write_innslag::create($kommune->getId(), $monstring->getId(), $type, $navn, $contact );