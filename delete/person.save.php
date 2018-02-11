<?php
require_once('UKM/write_monstring.class.php');
require_once('UKM/write_innslag.class.php');
require_once('UKM/write_person.class.php');

$monstring = new monstring_v2( get_option('pl_id') );
$innslag = $monstring->getInnslag()->get( $_POST['innslag'], true );
$person = $innslag->getPersoner()->get( $_POST['object_id'] );

$innslag->getPersoner()->fjern( $person );

$res = write_innslag::savePersoner( $innslag );