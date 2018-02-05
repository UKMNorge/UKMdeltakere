<?php
// Skal relatere en person til et gitt innslag.

require_once('UKM/write_tittel.class.php');
require_once('UKM/write_innslag.class.php');
require_once('UKM/write_monstring.class.php');
require_once('UKM/monstringer.collection.php');

$innslag = new write_innslag( $_POST['innslag'] );
$tittel = new write_tittel( $_POST['object_id'], $innslag->getType()->getTabell() );
$monstring = new write_monstring( get_option('pl_id') );

$innslag->getTitler( $monstring )->fjern( $tittel, $monstring);