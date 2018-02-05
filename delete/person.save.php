<?php
require_once('UKM/write_monstring.class.php');
require_once('UKM/write_innslag.class.php');
require_once('UKM/write_person.class.php');

$monstring = new write_monstring( get_option('pl_id') );
$innslag = new write_innslag( $_POST['innslag'] );
$person = new write_person( $_POST['object_id']);

$res = $innslag->getPersoner()->fjern($person, $monstring);