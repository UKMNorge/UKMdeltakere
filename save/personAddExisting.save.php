<?php
// Skal relatere en person til et gitt innslag.

require_once('UKM/write_person.class.php');
require_once('UKM/write_innslag.class.php');
require_once('UKM/write_monstring.class.php');
require_once('UKM/monstringer.collection.php');


$innslag = new write_innslag( $_POST['innslag'] );
$person = new write_person( $_POST['object_id'] );

$innslag->getPersoner()->leggTil($person);

### Sett rolle / instrument på dette innslaget
if(!$innslag->getType()->harTitler()) {
	# TODO:
	throw new Exception("PERSON_SAVE: Husk å lagre instrument_objekter for tittelløse innslag, og prettify instrument-verdier.");
}
$innslag->setRolle($person, $DATA['rolle']);

// VIDERESEND AUTOMATISK TIL AKTIVT NIVÅ HVIS FYLKE ELLER LAND
if( get_option('site_type') == 'fylke' || get_option('site_type') == 'land' ) {
	$monstring_w_til = new write_monstring( get_option('pl_id') );
	if( get_option('site_type') == 'fylke' ) {
		$monstring_fra = monstringer_v2::kommune( $innslag->getKommune(), $monstring_w_til->getSesong() );
	} elseif( get_option('site_type') == 'fylke' ) {
		$monstring_fra = monstringer_v2::fylke( $innslag->getFylke(), $monstring_w_til->getSesong() );
	}
	$monstring_w_fra = new write_monstring( $monstring_fra->getId() );
	
	$innslag->getPersoner()->videresend($person, $monstring_w_til, $monstring_w_fra);
}


$innslag->save();