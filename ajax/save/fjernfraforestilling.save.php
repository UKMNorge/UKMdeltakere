<?php
function UKMdeltakere_save() {
	$innslag = new innslag($_POST['b_id']);	
	if(get_option('site_type')!='kommune')
		$innslag->videresendte(get_option('pl_id'));
	$res = $innslag->fjernfraforestilling(str_replace('forestilling_','',$_POST['c_id']));
	die(json_encode(array('result'=>(bool)$res)));
}
?>