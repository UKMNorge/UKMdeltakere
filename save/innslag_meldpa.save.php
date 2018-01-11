<?php

require_once('UKM/write_innslag.class.php');

// Sjekk for påkrevd data:
if( empty($_POST['innslag']) ) {
	throw new Exception("Innslag.save: Mangler noe data!");
}

$innslag = new write_innslag( $_POST['innslag'] );
$innslag->setStatus( 8 );
$innslag->save();

$JSON->redirect = '?page=UKMdeltakere&list=fullstendig&edit='. $_POST['innslag'] .'#innslag_'. $_POST['innslag'];