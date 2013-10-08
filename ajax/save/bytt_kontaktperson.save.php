<?php
function UKMdeltakere_save() {
	
	$innslag = new innslag($_POST['b_id']);
	$innslag->setKontaktperson($_POST['p_id']);
	if(get_option('site_type')!='kommune')
		$innslag->videresendte(get_option('pl_id'));
	
	$pers = new person($_POST['p_id']);
	
	
	$data = array('result'=>true,
				  'p_firstname'=>$pers->g('p_firstname'),
				  'p_phone'=>$pers->g('p_phone'),
				  'age'=>$pers->alder());
	die(json_encode($data));
}
?>