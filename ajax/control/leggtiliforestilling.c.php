<?php
UKM_loader('api/innslag.class');
UKM_loader('api/monstring.class');
UKM_loader('api/forestilling.class');
function UKMdeltakere_ajax_controller($b_id){
	$pl_id = get_option('pl_id');
	$monstring = new monstring($pl_id);
	$forestillinger = $monstring->forestillinger('c_start',false);
	$innslag = new innslag($b_id);
	if(get_option('site_type')!='kommune')
		$innslag->videresendte(get_option('pl_id'));
	$innslag_forestilling = $innslag->forestillinger($pl_id);
	
	return array('f' => $forestillinger, 'if' => $innslag_forestillinger, 'b_id' => $b_id);
}
?>