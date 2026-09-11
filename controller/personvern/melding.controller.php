<?php

/**
 * Send påminnelse om personvern-samtykke til deltaker eller foresatt.
 */

use UKMNorge\Arrangement\Skjema\DeltaRespondent;

require_once('UKM/sms.class.php');

$mottakerType = isset($_GET['mottaker']) ? (string) $_GET['mottaker'] : '';
$personId = isset($_GET['id']) ? (int) $_GET['id'] : 0;

if (!in_array($mottakerType, ['deltaker', 'foresatt'], true) || $personId < 1) {
	UKMdeltakere::getFlash()->error('Ugyldig forespørsel. Kunne ikke sende melding.');
	return;
}

$arrangement = UKMdeltakere::getArrangement();
$person = null;

foreach ($arrangement->getInnslag()->getAll() as $innslag) {
	foreach ($innslag->getPersoner()->getAll() as $innslagPerson) {
		if ((int) $innslagPerson->getId() === $personId) {
			$person = $innslagPerson;
			break 2;
		}
	}
}

if ($person === null) {
	UKMdeltakere::getFlash()->error('Fant ikke deltakeren på dette arrangementet.');
	return;
}

$svar = $person->getPersonvernSamtykkeskjemaSvar();
$deltaUser = DeltaRespondent::loadByMobil($person->getMobil());
$erU18 = (int) $person->getAlderTall() < 18;
$deltakerSvar = $svar !== null ? $svar->getSvar() : null;
$foresattGodkjent = $svar !== null && $svar->hasForesattGodkjent();
$deltaLenke = 'https://delta.' . UKM_HOSTNAME . '/ukmid/samtykkeskjema/' . PERSONVERN_PROSJEKT_ID . '/';
$fornavn = $person->getFornavn();

if ($mottakerType === 'deltaker') {
	if ($deltakerSvar === 'ja') {
		UKMdeltakere::getFlash()->error('Deltakeren har allerede svart på samtykkeskjemaet.');
		return;
	}

	$mobil = $person->getMobil();
	if (empty($mobil)) {
		UKMdeltakere::getFlash()->error('Deltakeren har ikke mobilnummer.');
		return;
	}

	if ($deltakerSvar === 'nei') {
		$melding = 'Hei ' . $fornavn . '! Hvis du vil endre valget ditt om bilder og film av deg på UKM, kan du gjøre dette her: ' . $deltaLenke;
	} else {
		$melding = 'Hei ' . $fornavn . '! Vi trenger et svar fra deg om bilder og film på UKM.' . "\r\n" .
			'Gi oss beskjed på lenken nedenfor. ' . $deltaLenke;
	}
} else {
	if (!$erU18) {
		UKMdeltakere::getFlash()->error('Foresatt trenger ikke å svare når deltakeren er 18 år eller eldre.');
		return;
	}
	if (!in_array($deltakerSvar, ['ja', 'nei'], true)) {
		UKMdeltakere::getFlash()->error('Deltakeren må svare før det kan sendes melding til foresatt.');
		return;
	}
	if ($foresattGodkjent) {
		UKMdeltakere::getFlash()->error('Foresatt har allerede godkjent samtykket.');
		return;
	}
	if ($deltaUser === null || empty($deltaUser->getForesattMobil())) {
		UKMdeltakere::getFlash()->error('Mangler mobilnummer til foresatt.');
		return;
	}

	$mobil = $deltaUser->getForesattMobil();
	$foresattLenke = $deltaLenke . $deltaUser->getId() . '/';
	$melding = 'Hei! Vi savner et svar fra deg om bilder og film i forbindelse med ' . $fornavn .
		' sin deltakelse på UKM. ' . "\r\n" .
		'Gi oss beskjed på lenken nedenfor. ' . $foresattLenke;
}

if (UKM_HOSTNAME == 'ukm.dev') {
	UKMdeltakere::getFlash()->success(
		'Meldingen ble sendt (debug): ' .
			'<div class="card">' .
			'<b>Til:</b> ' . htmlspecialchars((string) $mobil) . '<br />' .
			nl2br(htmlspecialchars($melding)) .
			'</div>'
	);
	return;
}

try {
	$sms = new SMS('samtykke', 0);
	$sms->text($melding)->to($mobil)->from('UKMNorge')->ok();
	UKMdeltakere::getFlash()->success(
		'Meldingen ble sendt: ' .
			'<div class="card">' .
			nl2br(htmlspecialchars($melding)) .
			'</div>'
	);
} catch (Exception $e) {
	UKMdeltakere::getFlash()->error('Kunne ikke sende SMS: ' . $e->getMessage());
}
