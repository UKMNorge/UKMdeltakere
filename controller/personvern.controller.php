<?php

/**
 * Henter alle deltakere på mønstringen, og sjekker status
 * på deres personvern-tilbakemeldinger.
 * 
 * Hvis personen ikke ligger i samtykke-systemet, blir en rad opprettet nå.
 * Personen vil da få sms fra cronjobben som følger opp dette.
 */

use UKMNorge\Samtykkeskjema\SamtykkeSkjema;
use UKMNorge\Arrangement\Skjema\DeltaRespondent;

$arrangement = UKMdeltakere::getArrangement();

$personerUnder18 = [];
$personer18EllerEldre = [];

foreach ($arrangement->getInnslag()->getAll() as $innslag) {
	foreach ($innslag->getPersoner()->getAll() as $person) {
		$entry = [
			'person' => $person,
			'deltaRespondent' => null,
			'samtykkeSkjema' => null,
			'svar' => null,
			'foresattSamtykke' => null,
		];

		$deltaRespondent = DeltaRespondent::loadByMobil($person->getMobil());
		if ($deltaRespondent == null) {
			$personerUnder18[$person->getId()] = $entry;
			continue;
		}

		$entry['deltaRespondent'] = $deltaRespondent;

		$deltaUserId = $deltaRespondent->getId();
		$samtykkeSkjema = SamtykkeSkjema::getPersonvernSamtykkeskjema($deltaUserId);
		$entry['samtykkeSkjema'] = $samtykkeSkjema;
		if ($samtykkeSkjema != null) {
			try {
				$versjon = $samtykkeSkjema->getLastVersion();
				$svar = $versjon->getSvarSamtykkeForBruker($deltaUserId);
				$entry['svar'] = $svar;
				$entry['foresattSamtykke'] = $versjon->isForesattGodkjent($deltaUserId);
			} catch (Exception $e) {
				$entry['svar'] = null;
				$entry['foresattSamtykke'] = null;
			}
		}

		if ($deltaRespondent->is18YearNow()) {
			$personer18EllerEldre[$person->getId()] = $entry;
		} else {
			$personerUnder18[$person->getId()] = $entry;
		}
	}
}

UKMdeltakere::addViewData(
	[
		'monstring' => $arrangement,
		'personerUnder18' => $personerUnder18,
		'personer18EllerEldre' => $personer18EllerEldre,
		'personvern_prosjekt_id' => 1,
		'delta_base_url' => 'https://delta.' . UKM_HOSTNAME,
		'is_super_admin' => is_super_admin()
	]
);
