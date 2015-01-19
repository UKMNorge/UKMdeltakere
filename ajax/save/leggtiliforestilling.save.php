<?php
function UKMdeltakere_save() {
	require_once('UKM/forestilling.class.php');

	$concert = new concert($_POST['c_id']);
	$concert->addBand($_POST['b_id']);
	
	$data = array('forestilling'=>$_POST['c_id'], 'innslag'=>$_POST['b_id']);
	do_action('UKMprogram_save', 'leggtil_i_forestilling', $_POST['c_id'], $_POST['b_id']);

	die(json_encode($data));
}
?>