<?php

require_once('UKM/innslag.class.php');
require_once('UKM/monstring.class.php');
require_once('UKM/forestilling.class.php');
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