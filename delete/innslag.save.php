<?php
/**
 * Meld av innslag
 *
 */

use UKMNorge\Innslag\Write;

require_once('UKM/Autoloader.php');

$innslag = $monstring->getInnslag()->get( $_POST['innslag'], true );
$monstring->getInnslag()->fjern($innslag);

Write::meldAv( $innslag );

$JSON->meldtAv = true;