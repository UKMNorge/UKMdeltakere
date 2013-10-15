<?php
require_once('UKM/inc/validate_innslag.inc.php');

function UKMdeltakere_save() {
	$pers = new person($_POST['p_id']);
	$band = new innslag($_POST['b_id']);	
	if(get_option('site_type')=='fylke' && $band->g('bt_form') != 'smartukm_titles_scene'){
		$pers = new person($_POST['p_id'], $_POST['b_id']);
		$pl_from = new kommune_monstring($band->g('b_kommune'), get_option('season'));
		$pl_from = $pl_from->monstring_get();
		$res = $pers->avmeld($pl_from->g('pl_id'), get_option('pl_id'));
	} else {
		$res = $pers->unrelate($_POST['b_id']);
	}

	
	validateBand($_POST['b_id']);
	stat_realtime_remove($pers->g('p_kommune'), $_POST['b_id'], $pers->g('p_id'), $band->g('b_season'));
	
	$inn = new innslag($_POST['b_id']);
	$personer = $inn->personer();
	foreach($personer as $pers) {
		$p = new person($pers['p_id']);
		$alder += (int)$p->alder();
	}
	if(sizeof($personer) == 0)
		$snitt = 0;
	else
		$snitt = $alder / sizeof($personer);
	$personer = $inn->num_personer() . ' person' . ($inn->num_personer() == 1 ? '':'er');
	if(get_option('site_type')!='kommune')
		$inn->videresendte(get_option('pl_id'));
	
	die(json_encode(array('result'=>(bool)$res,
						  'b_id' => $inn->g('b_id'),
						  'reloadInnslag' => get_option('site_type')!='kommune',
						  'personer' => $personer,
						  'snitt' => $snitt,
						  'advarsler'=>$inn->warnings(get_option('pl_id'))
						  )));
}
?>