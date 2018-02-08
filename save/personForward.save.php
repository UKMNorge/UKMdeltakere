<?php
// Skal relatere en person til et gitt innslag.

require_once('UKM/write_person.class.php');
require_once('UKM/write_innslag.class.php');
require_once('UKM/write_monstring.class.php');
require_once('UKM/monstringer.collection.php');

$innslag = $monstring->getInnslag()->get( $_POST['innslag'] );
$person = $innslag->getPersoner()->get( $_POST['object_id'] );

$innslag->getPersoner()->leggtil( $person );
write_person::leggTil( $person );