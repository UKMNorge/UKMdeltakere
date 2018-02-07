<?php

require_once('UKM/write_innslag.class.php');

// Sjekk for påkrevd data:
if( empty($_POST['innslag']) ) {
	throw new Exception("Innslag.save: Mangler noe data!");
}

$innslag = $monstring->getInnslag()->get( $_POST['innslag'], true );
$innslag->setStatus( 8 );

write_innslag::saveStatus( $innslag );

$JSON->redirect = '?page=UKMdeltakere&list=fullstendig&edit='. $_POST['innslag'] .'#innslag_'. $_POST['innslag'];