<?php
require_once('UKM/inc/validate_innslag.inc.php');

function UKMdeltakere_save() {
	require_once('UKM/tittel.class.php');
	
	$title = new tittel(false, $_POST['bt_form']);
	$title->create($_POST['b_id']);
		
	if (isset($_POST['tittel'])) 
		$title->update('tittel');
	if (isset($_POST['melodi_av'])) 
		$title->update('melodi_av');
	if (isset($_POST['varighet'])) 
		$title->update('varighet');
	if (isset($_POST['koreografi'])) 
		$title->update('koreografi');
	if (isset($_POST['tekst_av'])) 
		$title->update('tekst_av');
	if (isset($_POST['beskrivelse'])) 
		$title->update('beskrivelse');
	if (isset($_POST['teknikk'])) 
		$title->update('teknikk');
	if (isset($_POST['type'])) 
		$title->update('type');
	if (isset($_POST['format'])) 
		$title->update('format');
	
	
	validateBand($_POST['b_id']);
	$inn = new innslag($_POST['b_id']);
	if(get_option('site_type')!='kommune') {
		$inn->videresendte(get_option('pl_id'));

		$pl_from = new kommune_monstring($inn->g('b_kommune'), get_option('season'));
		$pl_from = $pl_from->monstring_get();
		$title->videresend($pl_from->g('pl_id'), get_option('pl_id'));
	}
	$data = array('tittel'=>$title->g('t_id'),
				  'innslag'=>$_POST['b_id'],
				  'b_id' => $inn->g('b_id'),
				  'varighet' => $inn->tid(get_option('pl_id')),
				  'titler' => $inn->g('antall_titler_lesbart'),
				  'advarsler'=>$inn->warnings(get_option('pl_id'))
				 );
	die(json_encode($data));
}
?>