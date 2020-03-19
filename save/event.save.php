<?php

use UKMNorge\Arrangement\Arrangement;
use UKMNorge\Arrangement\Program\Write;

require_once('UKM/Autoloader.php');

$arrangement = new Arrangement( intval(get_option('pl_id')));

try {
	$hendelse = $arrangement->getProgram()->get( intval($DATA['event']) );
} catch ( Exception $e ) {
	throw new Exception('Du må velge en hendelse å legge til innslaget i');	
}

$innslag = $arrangement->getInnslag()->get( intval($_POST['innslag']) );

try {
	$hendelse->getInnslag()->leggTil( $innslag );
	Write::leggTil($hendelse, $innslag );
} catch( Exception $e ) {
	switch( (int) $e->getCode() ) {
		case 10404:
			throw new Exception('Innslaget er allerede med i '. $hendelse->getNavn() .', og ble derfor ikke lagt til');
	}
	throw $e;
}