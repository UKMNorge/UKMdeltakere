<?php
require_once('UKM/inc/validate_innslag.inc.php');

function UKMdeltakere_save() {
	require_once('UKM/tittel.class.php');
	
	$inn = new innslag($_POST['b_id']);

	$title = new tittel($_POST['t_id'], $inn->g('bt_form'));
	$inn->videresendte(get_option('pl_id'));

	$pl_from = new kommune_monstring($inn->g('b_kommune'), get_option('season'));
	$pl_from = $pl_from->monstring_get();
	$res = $title->videresend($pl_from->g('pl_id'), get_option('pl_id'));

	$data = array('result' => $res,
				  'tittel'=>$title->g('t_id'),
				  'innslag'=>$_POST['b_id'],
				  'b_id' => $inn->g('b_id'),
				  'varighet' => $inn->tid(get_option('pl_id')),
				  'titler' => $inn->g('antall_titler_lesbart'),
				  'advarsler'=>$inn->warnings(get_option('pl_id'))
				 );
	die(json_encode($data));
}
?>