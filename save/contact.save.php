<?php

/**
 * BYTTER KONTAKTPERSON FOR GITT INNSLAG
**/

require_once('UKM/write_innslag.class.php');

$innslag = $monstring->getInnslag()->get( $_POST['innslag'], true );
$innslag->setKontaktperson( new person_v2( (int)$DATA['person'] ) );

write_innslag::save( $innslag );