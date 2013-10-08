<?php
require_once( $_SERVER['DOCUMENT_ROOT'] . '/UKM/subdomains/pamelding/include/validation.inc.php' );

function UKMdeltakere_save() {
	$reload = false;
	$inn = new innslag($_POST['i'],false);
	if(get_option('site_type')!='kommune')
		$inn->videresendte(get_option('pl_id'));
	$inn->update('b_name');		
	$inn->update('b_kategori');
	$inn->update('b_sjanger');
	$inn->update('td_demand');
	$inn->update('td_konferansier', 'b_description');
	
	if(isset($_POST['b_kommune'])){
		if($_POST['b_kommune'] !== $inn->g('b_kommune')) {
			$inn->update('b_kommune');
			$reload = true;
		}
	}
	
	if ($_POST['td_description'] == 'true') {
		$inn->update('b_description', 'b_description', true);
	}
	else {
		$inn->clear('b_description');
	}
	
	validateBand($_POST['i']);
	
	$data = array(
		'innslag'=>$_POST['b_name'],
		'sjanger'=>$_POST['b_sjanger'],
		'kategori'=>$_POST['b_kategori'],
		'advarsler'=>$inn->warnings(get_option('pl_id')),
		'reload'=>$reload,
		'b_id' => $_POST['i']
	);
	die(json_encode($data));
}
?>