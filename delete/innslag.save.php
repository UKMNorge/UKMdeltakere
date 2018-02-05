<?php
/**
 * Meld av innslag
 *
 */
require_once('UKM/write_innslag.class.php');
require_once('UKM/write_monstring.class.php');

$innslag = new write_innslag($_POST['innslag']);
$monstring = new write_monstring(get_option('pl_id'));
$monstring->getInnslag()->fjern($innslag);


$JSON->meldtAv = true;