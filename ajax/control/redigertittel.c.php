<?php
UKM_loader('api/innslag.class');
UKM_loader('api/tittel.class');
function UKMdeltakere_ajax_controller($t_id){
	
	
	$b_id = $_POST['i2'];
	
	$innslag = new innslag($b_id);
	if(get_option('site_type')!='kommune')
		$innslag->videresendte(get_option('pl_id'));
	$bt_id = $innslag->g('bt_id');
	$form = $innslag->g('bt_form');
	$kategori = $innslag->g('b_kategori');
	
	$tittel = new tittel($t_id,$form);
	
	$kategorier = UKMdeltakere_tittelgui($bt_id, $kategori);
	
	$return = array('b_id' => $b_id, 'kategorier' => $kategorier, 'tittel' => $tittel);
	return $return;
}
?>