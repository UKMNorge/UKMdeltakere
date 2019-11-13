<?php
// Skal relatere en person til et gitt innslag.

use UKMNorge\Innslag\Personer\Person;
use UKMNorge\Innslag\Write as WriteInnslag;

$innslag = $monstring->getInnslag()->get( $_POST['innslag'], true );
$person = new Person( $_POST['object_id'] );
$person->setRolle( $DATA['rolle'] );

// Vil automatisk videresende til riktig mønstring og lagre rolle
$innslag->getPersoner()->leggTil($person);
WriteInnslag::savePersoner( $innslag );