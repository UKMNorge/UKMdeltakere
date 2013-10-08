<?php
require_once( $_SERVER['DOCUMENT_ROOT'] . '/UKM/pamelding/stat_realtime.inc.php' );
require_once( $_SERVER['DOCUMENT_ROOT'] . '/UKM/subdomains/pamelding/include/validation.inc.php' );

function UKMdeltakere_save() {
	// Ny person fra skjema
	$band = new innslag($_POST['b_id'], false);
	if(get_option('site_type')!='kommune')
		$band->videresendte(get_option('pl_id'));

	if (!isset($_POST['p_id'])) {
		// Valider felter
		if (empty($_POST['p_firstname']) || empty($_POST['p_lastname']) || empty($_POST['p_phone']) || strlen($_POST['p_phone']) != 8) {
			$data = array('error'=>'Fornavn, etternavn og mobil er obligatorisk, og personen ble derfor ikke lagt til. Vennligst prøv igjen');
			die(json_encode($data));
		}
		
		// Sjekk om de har skrevet info om en allerede eksisterende person
		$pers = new person(false);
		$pers = $pers->getExistingPerson($_POST['p_firstname'], $_POST['p_lastname'], $_POST['p_phone']);

		// Fant ingen med samme navn og mobil
		if (!$pers) {
			$pers = new person(false);
			$pers->create($_POST['b_id']);
		}
		else {
			$pers->relate($_POST['b_id']);
		}
		
		$pers->update('p_firstname');
		$pers->update('p_lastname');
		$pers->update('p_dob');
		$pers->update('p_email');
		$pers->update('p_phone');
		if (isset($_POST['p_kommune']))
			$pers->update('p_kommune');
		$pers->update('p_postnumber');
		$pers->update('p_adress');
		$pers->update('instrument', 'instrument', $_POST['b_id']);
	}
	// Knytt til eksisterende person
	else {
		$pers = new person($_POST['p_id']);
		$pers->relate($_POST['b_id']);
		$pers->update('instrument', 'instrument', $_POST['b_id']);
	}
	
	if(get_option('site_type')=='fylke') {
		$pl_from = new kommune_monstring($band->g('b_kommune'), get_option('season'));
		$pl_from = $pl_from->monstring_get();
		$pers->videresend($pl_from->g('pl_id'), get_option('pl_id'));
	}
	// Forward?
	
	validateBand($_POST['b_id']);
	stat_realtime_add($_POST['p_kommune'],
					  $_POST['b_id'],
					  $band->g('bt_id'),
					  $band->g('kategori'),
					  $pers->g('p_id'),
					  $band->g('b_season'));
	
	$inn = new innslag($_POST['b_id']);
	if(get_option('site_type')!='kommune')
		$inn->videresendte(get_option('pl_id'));


	$personer = $inn->personer();
	foreach($personer as $person) {
		$p = new person($person['p_id']);
		$alder += (int)$p->alder();
	}
	if(sizeof($personer) == 0)
		$snitt = 0;
	else
		$snitt = $alder / sizeof($personer);
	$personer = $inn->num_personer() . ' person' . ($inn->num_personer() == 1 ? '':'er');
	
	$data = array('person'=>$pers->g('p_id'),
				  'innslag'=>$_POST['b_id'],
				  'b_id' => $_POST['b_id'],
				  'personer' => $personer,
				  'snitt' => $snitt,
				  'advarsler'=>$inn->warnings(get_option('pl_id'))
				  );
	die(json_encode($data));
}
?>