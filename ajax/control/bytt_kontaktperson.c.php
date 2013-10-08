<?php
UKM_loader('api/innslag.class');
function UKMdeltakere_ajax_controller($bid){
	$innslag = new innslag($bid);
	if(get_option('site_type')!='kommune')
		$innslag->videresendte(get_option('pl_id'));

#	$monstring = new monstring(get_option('pl_id'));
	$personer = $innslag->personer();
	return array('innslag'=>$innslag, 'personer'=>$personer);
}
?>