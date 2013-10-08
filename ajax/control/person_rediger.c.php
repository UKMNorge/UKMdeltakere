<?php
UKM_loader('api/innslag.class');
function UKMdeltakere_ajax_controller($p_id){
	$place = new monstring(get_option('pl_id'));
	$b_id = $_POST['i2'];
	
	$kommuner = $place->kommuneArray();
	$person = new person( $p_id, $b_id );
		
	$return = array('person' => $person, 'kommuner' => $kommuner, 'b_id' => $b_id );
	
	return $return;
}
?>