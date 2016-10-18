<?php
/**
 * Meld av innslag
 *
 */
require_once('UKM/write_innslag.class.php');
require_once('UKM/monstring.class.php');

$innslag = new write_innslag($_POST['innslag']);
$monstring = new monstring_v2(get_option('pl_id'));
$monstring->getInnslag()->meldAv($innslag);