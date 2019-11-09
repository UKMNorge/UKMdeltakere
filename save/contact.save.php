<?php

/**
 * BYTTER KONTAKTPERSON FOR GITT INNSLAG
**/

use UKMNorge\Innslag\Personer\Person;
use UKMNorge\Innslag\Write;

require_once('UKM/Autoloader.php');

$innslag = $monstring->getInnslag()->get( $_POST['innslag'], true );
$innslag->setKontaktperson( new Person( (int)$DATA['person'] ) );

Write::save( $innslag );