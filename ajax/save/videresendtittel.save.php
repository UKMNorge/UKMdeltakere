<?php
require_once( $_SERVER['DOCUMENT_ROOT'] . '/UKM/subdomains/pamelding/include/validation.inc.php' );

function UKMdeltakere_save() {
	UKM_loader('api/tittel.class');
	
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