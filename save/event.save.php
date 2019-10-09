<?php

use UKMNorge\Innslag\Write;

require_once('UKM/Autoloader.php');

try {
	$forestilling = $monstring->getProgram()->get( $DATA['event'] );
} catch ( Exception $e ) {
	throw new Exception('Du må velge en hendelse å legge til innslaget i');	
}
$innslag = $monstring->getInnslag()->get( $_POST['innslag'] );

try {
	$forestilling->getInnslag()->leggTil( $innslag );
	Write::leggTil( $innslag );
} catch( Exception $e ) {
	switch( (int) $e->getCode() ) {
		case 10404:
			throw new Exception('Innslaget er allerede med i '. $forestilling->getNavn() .', og ble derfor ikke lagt til');
	}
	throw $e;
}