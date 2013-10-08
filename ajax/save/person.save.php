<?php
require_once( $_SERVER['DOCUMENT_ROOT'] . '/UKM/subdomains/pamelding/include/validation.inc.php' );

function UKMdeltakere_save() {
	$reload = false;
	// Valider felter
	if (empty($_POST['p_firstname']) || empty($_POST['p_lastname']) ||empty($_POST['p_phone']) || strlen($_POST['p_phone']) != 8) {
		$data = array('error'=>'Fornavn, etternavn og mobil er obligatorisk');
		die(json_encode($data));
	}
	
	if (isset($_POST['p']))
		$pers = new person($_POST['p']);
	else if (isset($_POST['i']))
		$pers = new person($_POST['i']);
	
	$pers->update('p_firstname');
	$pers->update('p_lastname');
	$pers->update('p_dob');
	$pers->update('p_email');
	$pers->update('p_phone');
	if (isset($_POST['p_kommune']))
		$pers->update('p_kommune');
	$pers->update('p_postnumber');
	$pers->update('p_adress');
	
	if (isset($_POST['instrument']))
		$pers->update('instrument', 'instrument', $_POST['b']);
	
	// Bare tittelløst innslag	
	if (isset($_POST['erfaring'])) {
		$pers->update('td_konferansier', 'erfaring', $_POST['b']);
		
		// Sett innslagnavn likt med deltaker
		$innslag = new innslag($_POST['b']);
		$_POST['b_name'] = $_POST['p_firstname'].' '.$_POST['p_lastname'];
		$innslag->update('b_name');
		
		$tittellos = true;
	}
	if(get_option('site_type')!='kommune')
		$innslag->videresendte(get_option('pl_id'));
	
	validateBand($_POST['b']);
	
	$pers = new person($_POST['i'], $_POST['b']);
	$data = array('person'=>$pers->g('p_id'),
				  'p_id'=>$pers->g('p_id'),
				  'p_name'=>shortString($pers->g('name')),
				  'p_name_full'=>$pers->g('name'),
				  'p_phone'=>$pers->getNicePhoneWithColor(),
				  'instrument'=>$pers->g('instrument'),
				  'p_email'=>$pers->g('p_email'),
				  'p_alder'=>$pers->alder()
				  );
	if($tittellos) {
		$data['tittellos'] = true;
		$data['b_id'] = $innslag->g('b_id');
		$data['innslag'] = $innslag->g('b_name');
		$data['kategori'] = 'Nettredaksjon';
		$data['sjanger'] = '';
		$data['p_alder'] = $pers->alder();
		
		
		if(isset($_POST['p_kommune'])){
			if($_POST['p_kommune'] !== $innslag->g('b_kommune')) {

				$_POST['b_kommune'] = $_POST['p_kommune'];
				$_POST['log_current_value_b_kommune'] = '';

				$innslag->update('b_kommune');
				$reload = true;
			}
		}
		$data['reload'] = $reload;
	}
	
	$inn = new innslag($_POST['b']);
	$personer = $inn->personer();
	foreach($personer as $pers) {
		$p = new person($pers['p_id']);
		$alder += (int)$p->alder();
	}
	if(sizeof($personer) == 0)
		$snitt = 0;
	else
		$snitt = round($alder / sizeof($personer));
	
	$personer = $inn->num_personer() . ' person' . ($inn->num_personer() == 1 ? '':'er');

	$data['personer'] = $personer;
	$data['snitt']	= $snitt;
	$data['b_id'] = isset($_POST['b']) ? $_POST['b'] : $_POST['i2'];
	$data['advarsler'] = $inn->warnings(get_option('pl_id'));

	die(json_encode($data));
}
?>