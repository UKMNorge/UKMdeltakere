<?php

/**
 * Henter alle deltakere på mønstringen, og sjekker status
 * på deres personvern-tilbakemeldinger.
 * 
 * Hvis personen ikke ligger i samtykke-systemet, blir en rad opprettet nå.
 * Personen vil da få sms fra cronjobben som følger opp dette.
 */

use UKMNorge\Samtykke\Kategorier;
use UKMNorge\Samtykke\Person;

require_once('UKM/samtykke/kategorier.class.php');

$monstring = new monstring_v2( get_option('pl_id') );
$TWIGdata['monstring'] = $monstring;

$grupper = [
	'u13' => Kategorier::getById('u13'),
	'u15' => Kategorier::getById('u15'),
	'15o' => Kategorier::getById('15o')
];

foreach( $monstring->getInnslag()->getAll() as $innslag ) {
	foreach( $innslag->getPersoner()->getAll() as $person ) {
		$samtykke = new Person( $person, $innslag );

		$grupper[ $samtykke->getKategori()->getId() ]->personer[ $samtykke->getNavn() . '-'. $samtykke->getId() ] = $samtykke;
	}
}

foreach( $grupper as $gruppe ) {
    if( is_array( $gruppe ) ) {
        ksort( $gruppe->personer );
    }
}

$TWIGdata['personer'] = $grupper;
$TWIGdata['sms_u15'] = $grupper['u15']->getSms();
$TWIGdata['sms_15o'] = $grupper['15o']->getSms();
