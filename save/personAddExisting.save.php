<?php
// Skal relatere en person til et gitt innslag.

require_once('UKM/write_person.class.php');
require_once('UKM/write_innslag.class.php');

$innslag = new write_innslag( $_POST['innslag'] );
$person = new write_person( $_POST['object_id'] );

$innslag->getPersoner()->leggTil($person);

### Sett rolle / instrument på dette innslaget
if(!$innslag->getType()->harTitler()) {
	# TODO:
	throw new Exception("PERSON_SAVE: Husk å lagre instrument_objekter for tittelløse innslag, og prettify instrument-verdier.");
}
$innslag->setRolle($person, $DATA['rolle']);
$innslag->save();
