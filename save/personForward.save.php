<?php
// Skal relatere en person til et gitt innslag.

require_once('UKM/write_person.class.php');
require_once('UKM/write_innslag.class.php');
require_once('UKM/write_monstring.class.php');
require_once('UKM/monstringer.collection.php');

$innslag = new write_innslag( $_POST['innslag'] );
$person = new write_person( $_POST['object_id'] );
$monstring = new write_monstring( get_option('pl_id') );

$innslag->getPersoner()->leggtil($person, $monstring);