<?php
require_once('UKM/innslag.class.php');
require_once('UKM/tittel.class.php');
function UKMdeltakere_ajax_controller($b_id){
	
	$innslag = new innslag($b_id,false);
	if(get_option('site_type')!='kommune')
		$innslag->videresendte(get_option('pl_id'));

	$bt_id = $innslag->g('bt_id');
	$form = $innslag->g('bt_form');
	$kategori = $innslag->g('b_kategori');
	$kategorier = UKMdeltakere_tittelgui($bt_id, $kategori);

	return array('form' => $form, 'b_id' => $b_id, 'kategorier' => $kategorier);
}