<?php
// Skal relatere en person til et gitt innslag.

use UKMNorge\Innslag\Personer\Write;

require_once('UKM/Autoloader.php');

$innslag = $monstring->getInnslag()->get( $_POST['innslag'], true );
$person = $innslag->getPersoner()->get( $_POST['object_id'] );

$innslag->getPersoner()->leggtil( $person );
Write::leggTil( $person );