<?php

/**
 * Henter alle deltakere på mønstringen, og sjekker status
 * på deres personvern-tilbakemeldinger.
 * 
 * Hvis personen ikke ligger i samtykke-systemet, blir en rad opprettet nå.
 * Personen vil da få sms fra cronjobben som følger opp dette.
 */

use UKMNorge\Arrangement\Arrangement;
use UKMNorge\Samtykke\Kategorier;
use UKMNorge\Samtykke\Person;


$arrangement = UKMdeltakere::getArrangement();

$grupper = [
	'u13' => Kategorier::getById('u13'),
	'u15' => Kategorier::getById('u15'),
	'15o' => Kategorier::getById('15o')
];

foreach ($arrangement->getInnslag()->getAll() as $innslag) {
	foreach ($innslag->getPersoner()->getAll() as $person) {
		$samtykke = new Person($person, $innslag);

		$grupper[$samtykke->getKategori()->getId()]->personer[$samtykke->getNavn() . '-' . $samtykke->getId()] = $samtykke;
	}
}

foreach ($grupper as $gruppe) {
	if (is_array($gruppe)) {
		ksort($gruppe->personer);
	}
}

UKMdeltakere::addViewData(
	[
		'monstring' => $arrangement,
		'personer' => $grupper,
		'sms_u15' => $grupper['u15']->getSms(),
		'sms_15o' => $grupper['15o']->getSms(),
		'is_super_admin' => is_super_admin()
	]
);
