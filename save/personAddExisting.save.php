<?php
// Skal relatere en person til et gitt innslag.

require_once('UKM/write_person.class.php');
require_once('UKM/write_innslag.class.php');
require_once('UKM/write_monstring.class.php');
require_once('UKM/monstringer.collection.php');

$monstring = new write_monstring( get_option('pl_id') );
$innslag = new write_innslag( $_POST['innslag'] );
$person = new write_person( $_POST['object_id'] );

// Vil automatisk videresende til riktig mønstring
$innslag->getPersoner()->leggTil($person, $monstring);

### Sett rolle / instrument på dette innslaget
if(!$innslag->getType()->harTitler()) {
	# TODO:
	throw new Exception("PERSON_SAVE: Husk å lagre instrument_objekter for tittelløse innslag, og prettify instrument-verdier.");
}
$innslag->setRolle($person, $DATA['rolle']);
$innslag->save();