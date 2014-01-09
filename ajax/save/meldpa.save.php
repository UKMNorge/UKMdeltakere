<?php
require_once( 'UKM/inc/validate_innslag.inc.php' );
#require_once( $_SERVER['DOCUMENT_ROOT'] . '/UKM/pamelding/stat_realtime.inc.php' );

function UKMdeltakere_save() {
	if(!isset($_POST['i']) || empty($_POST['i']))
		die(json_encode(array('result'=>false)));

	$innslag = new innslag($_POST['i'],false);
	
	$_POST['log_current_value_b_status'] = 0;
	$_POST['b_status'] = 8;
	$innslag->update('b_status');
	$innslag->statistikk_oppdater();

	validateBand($_POST['i']);
//	stat_realtime_add($innslag->g('kommune'), $innslag->g('b_id'), $innslag->g('bt_id'), $innslag->g('kategori'), 0, $innslag->g('b_season'));
	
	$innslag = new innslag($_POST['i'],false);
	die(json_encode(array('result'=>$innslag->g('b_status')==8)));
}
?>