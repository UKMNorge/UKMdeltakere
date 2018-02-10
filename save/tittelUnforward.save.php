<?php
// Skal relatere en person til et gitt innslag.

require_once('UKM/write_tittel.class.php');
require_once('UKM/write_innslag.class.php');
require_once('UKM/write_monstring.class.php');
require_once('UKM/monstringer.collection.php');

$innslag = $monstring->getInnslag()->get( $_POST['innslag'] );
$tittel = $innslag->getTitler()->get( $_POST['object_id'] );

$innslag->getTitler()->fjern( $tittel );
write_tittel::fjern( $tittel );