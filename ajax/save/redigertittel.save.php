<?php
require_once( $_SERVER['DOCUMENT_ROOT'] . '/UKM/subdomains/pamelding/include/validation.inc.php' );

function UKMdeltakere_save() {
	UKM_loader('api/tittel.class');
	$title = new tittel($_POST['t_id'], $_POST['form']);
		
	if (isset($_POST['tittel'])) 
		$title->update('tittel');
	if (isset($_POST['melodi_av'])) 
		$title->update('melodi_av');
	if (isset($_POST['varighet'])) 
		$title->update('varighet');
	if (isset($_POST['koreografi'])) 
		$title->update('koreografi');
	if (isset($_POST['tekst_av'])) 
		$title->update('tekst_av');
	if (isset($_POST['beskrivelse'])) 
		$title->update('beskrivelse');
	if (isset($_POST['teknikk'])) 
		$title->update('teknikk');
	if (isset($_POST['type'])) 
		$title->update('type');
	if (isset($_POST['format'])) 
		$title->update('format');
		
	$title = new tittel($_POST['t_id'], $_POST['form']);
	
/*
		case 'tittel': return 50;
		case 'koreografi': return 25;
		case 'varighet': return 15;
		default: return 20;
*/	
	validateBand($_POST['b_id']);
	$inn = new innslag($_POST['b_id']);
	if(get_option('site_type')!='kommune')
		$inn->videresendte(get_option('pl_id'));

	$data = array('tittel'=>$_POST['t_id'],
				 'ny_tittel'=>shortString($title->g('tittel'),30),
				 'ny_tittel_full'=>$title->g('tittel'),
				 'ny_melodi_av'=>shortString($title->g('melodi_av'),20),
				 'ny_melodi_av_full'=>$title->g('melodi_av'),
				 'ny_varighet'=>$title->g('tid'),
				 'ny_koreografi'=>shortString($title->g('koreografi'),25),
				 'ny_koreografi_full'=>$title->g('koreografi'),
				 'ny_tekst_av'=>shortString($title->g('tekst_av'),20),
				 'ny_tekst_av_full'=>$title->g('tekst_av'),
				 'ny_beskrivelse'=>$title->g('beskrivelse'),
				 'ny_teknikk'=>shortString($title->g('teknikk'),20),
				 'ny_teknikk_full'=>$title->g('teknikk'),
				 'ny_type'=>shortString($title->g('type'),20),
				 'ny_type_full'=>$title->g('type'),
				 'ny_format'=>shortString($title->g('format'),20),
				 'ny_format_full'=>$title->g('format'),
				 'ny_beskrivelse'=>shortString($title->g('beskrivelse'),20),
				 'ny_beskrivelse_full'=>$title->g('beskrivelse'),
				 'varighet' => $inn->tid(get_option('pl_id')),
				 'b_id' => $inn->g('b_id'),
				 'titler' => $inn->g('antall_titler_lesbart'),
				 'advarsler'=>$inn->warnings(get_option('pl_id'))

				 );

	die(json_encode($data));
}
?>