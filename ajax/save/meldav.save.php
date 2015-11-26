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
		#$res = $inn->delete();
		# Endret nov 2015 for kompabilitet med UKMdelta og oppgradert logg.
		global $current_user;
    		get_currentuserinfo();   
    		$user_id = $current_user->ID;
		$res = $inn->delete('UKMdeltakere', $current_user->ID, get_option('pl_id'));
	}
//	stat_realtime_avmeld($inn->g('kommune'), $inn->g('b_id'), $inn->g('season'));
	
	die(json_encode(array('result'=>$res)));
}
?>
