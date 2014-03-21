<?php
function UKMdeltakere_save() {
	$inn = new innslag($_POST['i'],false);
	if(get_option('site_type')!='kommune') {
		$kommune = $inn->g('b_kommune');
		if( empty( $kommune ) ) {
			die( json_encode(array('result'=>false)) );
		}
		$pl_from = new kommune_monstring($kommune, get_option('season'));
		$pl_from = $pl_from->monstring_get();
		$res = $inn->avmeld($pl_from->g('pl_id'), get_option('pl_id'));
	} else {
		$res = $inn->delete();
	}
//	stat_realtime_avmeld($inn->g('kommune'), $inn->g('b_id'), $inn->g('season'));
	
	die(json_encode(array('result'=>$res)));
}
?>