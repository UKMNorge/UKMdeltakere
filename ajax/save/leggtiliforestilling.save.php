<?php
function UKMdeltakere_save() {
	UKM_loader('api/forestilling.class');

	$concert = new concert($_POST['c_id']);
	$concert->addBand($_POST['b_id']);
	
	$data = array('forestilling'=>$_POST['c_id'], 'innslag'=>$_POST['b_id']);
	die(json_encode($data));
}
?>