<?php
function UKMdeltakere_save() {
	// Ny person fra skjema
	$inn = new innslag($_POST['b_id'], false);
	$pers = new person($_POST['p_id'], $_POST['b_id']);
	if(get_option('site_type')!='kommune')
		$inn->videresendte(get_option('pl_id'));

	$pl_from = new kommune_monstring($inn->g('b_kommune'), get_option('season'));
	$pl_from = $pl_from->monstring_get();
	$res = $pers->videresend($pl_from->g('pl_id'), get_option('pl_id'));


	$personer = $inn->personer();
	foreach($personer as $person) {
		$p = new person($person['p_id']);
		$alder += (int)$p->alder();
	}
	if(sizeof($personer) == 0)
		$snitt = 0;
	else
		$snitt = $alder / sizeof($personer);
	$personer = $inn->num_personer() . ' person' . ($inn->num_personer() == 1 ? '':'er');
	
	$data = array('result' => $res,
				  'person'=>$pers->g('p_id'),
				  'innslag'=>$_POST['b_id'],
				  'b_id' => $_POST['b_id'],
				  'personer' => $personer,
				  'snitt' => $snitt,
				  'advarsler'=>$inn->warnings(get_option('pl_id'))
				  );
	die(json_encode($data));
}
?>