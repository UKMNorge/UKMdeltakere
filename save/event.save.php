<?php

require_once('UKM/write_innslag.class.php');
require_once('UKM/forestilling.class.php');

try {
	$forestilling = $monstring->getProgram()->get( $DATA['event'] );
} catch ( Exception $e ) {
	throw new Exception('Du må velge en hendelse å legge til innslaget i');	
}
$innslag = $monstring->getInnslag()->get( $_POST['innslag'] );

try {
	$forestilling->getInnslag()->leggTil( $innslag );
	write_innslag::leggTil( $innslag );
} catch( Exception $e ) {
	switch( (int) $e->getCode() ) {
		case 10404:
			throw new Exception('Innslaget er allerede med i '. $forestilling->getNavn() .', og ble derfor ikke lagt til');
	}
	throw $e;
}