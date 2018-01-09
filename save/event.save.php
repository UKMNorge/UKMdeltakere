<?php

require_once('UKM/write_innslag.class.php');
require_once('UKM/forestilling.class.php');

$innslag = new write_innslag($_POST['innslag']);
$forestilling = new forestilling_v2($DATA['event']);
try {
	$forestilling->getInnslag()->leggTil( $innslag );
} catch( Exception $e ) {
	switch( (int) $e->getCode() ) {
		case 1:
			throw new Exception('Innslaget er allerede med i '. $forestilling->getNavn() .', og ble derfor ikke lagt til');
		case 2:
			throw new Exception('Du må velge en hendelse å legge til innslaget i');
	}
	throw $e;
}