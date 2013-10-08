<?php
require_once('UKM/tittel.class.php');
require_once('UKM/inc/validate_innslag.inc.php');
function UKMdeltakere_save() {
	$band = new innslag($_POST['b_id']);
	$title = new tittel($_POST['t_id'], $band->g('bt_form'));
	
	$inn = new innslag($_POST['b_id']);
	if(get_option('site_type')!='kommune') {
		$inn->videresendte(get_option('pl_id'));

		$pl_from = new kommune_monstring($inn->g('b_kommune'), get_option('season'));
		$pl_from = $pl_from->monstring_get();
		$res = $title->avmeld($pl_from->g('pl_id'), get_option('pl_id'));
	} else {
		$res = $title->delete();
		validateBand($_POST['b_id']);
	}
	
	die(json_encode(array('result'=>(bool)$res,
						  'b_id' => $inn->g('b_id'),
						  'reloadInnslag' => get_option('site_type')!='kommune',
						  'varighet' => $inn->tid(get_option('pl_id')),
						  'titler' => $inn->g('antall_titler_lesbart'),
						  'advarsler'=>$inn->warnings(get_option('pl_id'))
						  )));
}
?>