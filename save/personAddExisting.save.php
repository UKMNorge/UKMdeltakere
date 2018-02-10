<?php
// Skal relatere en person til et gitt innslag.

require_once('UKM/write_person.class.php');
require_once('UKM/write_innslag.class.php');
require_once('UKM/write_monstring.class.php');
require_once('UKM/monstringer.collection.php');

$innslag = $monstring->getInnslag()->get( $_POST['innslag'] );
$person = new person_v2( $_POST['object_id'] );
$person->setRolle( $DATA['rolle'] );

// Vil automatisk videresende til riktig mønstring og lagre rolle
$innslag->getPersoner()->leggTil($person);
write_innslag::savePersoner( $innslag );