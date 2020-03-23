<?php

use UKMNorge\Arrangement\Arrangement;
use UKMNorge\Innslag\Write;

require_once('UKM/Autoloader.php');

$monstring = new Arrangement( get_option('pl_id') );
$innslag = $monstring->getInnslag()->get( $_POST['innslag'], true );
$person = $innslag->getPersoner()->get( $_POST['object_id'] );

$innslag->getPersoner()->fjern( $person );

$res = Write::savePersoner( $innslag );