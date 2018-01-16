<?php

// OBS - DENNE INKLUDERES OGSÅ AV PERSON_ADD-FUNKSJONALITET.

require_once('UKM/write_person.class.php');
require_once('UKM/write_innslag.class.php');


$person = new write_person( $_POST['object_id'] );
$innslag = new write_innslag( $_POST['innslag'] );

// We hope...
$fodselsdato = mktime(0,0,0,1,1, (int)date("Y") - (int)$DATA['alder']);

### Sett person-data på person-objektet.
$person->setFornavn( $DATA['fornavn'] );
$person->setEtternavn( $DATA['etternavn'] );
$person->setMobil( $DATA['mobil'] );
$person->setFodselsdato( $fodselsdato );
$person->setKommune( $DATA['kommune'] );
$person->setEpost( $DATA['epost'] );
$person->save();

### Sett rolle / instrument på dette innslaget
if(!$innslag->getType()->harTitler()) {
	# TODO:
	throw new Exception("PERSON_SAVE: Husk å lagre instrument_objekter for tittelløse innslag, og prettify instrument-verdier.");
}
$innslag->setRolle($person, $DATA['rolle']);
$innslag->save();
