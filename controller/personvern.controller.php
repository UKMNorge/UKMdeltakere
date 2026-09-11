<?php

/**
 * Henter alle deltakere på mønstringen, og viser personvern-status
 * fra UKM/Samtykkeskjema.
 *
 * - 18 år eller eldre: kun deltakerens svar
 * - Under 18 år: deltakerens svar og foresatt-godkjenning
 */

use UKMNorge\Arrangement\Skjema\DeltaRespondent;

if (!function_exists('personvernByggSamtykkeskjemaRad')) {
	function personvernByggSamtykkeskjemaRad($person): array
	{
		$svar = $person->getPersonvernSamtykkeskjemaSvar();
		$deltaUser = DeltaRespondent::loadByMobil($person->getMobil());
		$erU18 = (int) $person->getAlderTall() < 18;

		$deltakerSvar = $svar !== null ? $svar->getSvar() : null;
		if ($deltakerSvar === 'ja') {
			$deltakerLabel = 'Godtatt';
			$deltakerLevel = 'success';
		} elseif ($deltakerSvar === 'nei') {
			$deltakerLabel = 'Ikke godtatt';
			$deltakerLevel = 'danger';
		} else {
			$deltakerLabel = 'Ikke svart (ukjent)';
			$deltakerLevel = 'info';
		}

		$foresattGodkjent = $svar !== null && $svar->hasForesattGodkjent();
		$foresattMobil = $deltaUser !== null ? $deltaUser->getForesattMobil() : null;
		$samtykkeOppfylt = $deltakerSvar === 'ja' && (!$erU18 || $foresattGodkjent);

		return [
			'id' => $person->getId(),
			'navn' => $person->getNavn(),
			'mobil' => $person->getMobil(),
			'alder' => $person->getAlder(),
			'er_u18' => $erU18,
			'har_svar' => $svar !== null,
			'deltaker_svar' => $deltakerSvar,
			'deltaker_label' => $deltakerLabel,
			'deltaker_level' => $deltakerLevel,
			'foresatt_godkjent' => $foresattGodkjent,
			'foresatt_label' => $foresattGodkjent ? 'Godtatt' : 'Ikke svart',
			'foresatt_level' => $foresattGodkjent ? 'success' : 'danger',
			'foresatt_navn' => $deltaUser !== null ? $deltaUser->getForesattNavn() : null,
			'foresatt_mobil' => $foresattMobil,
			'samtykke_oppfylt' => $samtykkeOppfylt,
			'kan_sende_deltaker' => !$samtykkeOppfylt && $deltakerSvar !== 'ja' && !empty($person->getMobil()),
			'kan_sende_foresatt' => $erU18 && !$foresattGodkjent && in_array($deltakerSvar, ['ja', 'nei'], true) && !empty($foresattMobil),
		];
	}
}

$arrangement = UKMdeltakere::getArrangement();

$grupper = [
	'u18' => [
		'id' => 'u18',
		'navn' => 'Under 18 år',
		'krav' => 'Bes om å oppgi forelder/foresatt, og det er ønskelig at foresatt har sett informasjonen.',
		'personer' => [],
	],
	'o18' => [
		'id' => 'o18',
		'navn' => 'Over 18 år',
		'krav' => 'Deltakeren kan selv forholde seg til personvern og datalagring og har fått informasjon om hvor dette er.',
		'personer' => [],
	],
];

$settPersoner = [];

foreach ($arrangement->getInnslag()->getAll() as $innslag) {
	foreach ($innslag->getPersoner()->getAll() as $person) {
		$personId = $person->getId();
		if (isset($settPersoner[$personId])) {
			continue;
		}
		$settPersoner[$personId] = true;

		$rad = personvernByggSamtykkeskjemaRad($person);
		$gruppeId = $rad['er_u18'] ? 'u18' : 'o18';
		$grupper[$gruppeId]['personer'][$rad['navn'] . '-' . $personId] = $rad;
	}
}

foreach ($grupper as &$gruppe) {
	ksort($gruppe['personer']);
}
unset($gruppe);

UKMdeltakere::addViewData(
	[
		'monstring' => $arrangement,
		'personer' => $grupper,
	]
);
