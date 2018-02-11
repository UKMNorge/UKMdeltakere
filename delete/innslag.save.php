<?php
/**
 * Meld av innslag
 *
 */
require_once('UKM/write_innslag.class.php');
require_once('UKM/write_monstring.class.php');

$innslag = $monstring->getInnslag()->get( $_POST['innslag'], true );
$monstring->getInnslag()->fjern($innslag);

write_innslag::fjern( $innslag );

$JSON->meldtAv = true;