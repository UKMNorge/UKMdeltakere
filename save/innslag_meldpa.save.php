<?php

use UKMNorge\Innslag\Write;

require_once('UKM/Autoloader.php');

// Sjekk for påkrevd data:
if( empty($_POST['innslag']) ) {
	throw new Exception("Innslag.save: Mangler noe data!");
}

$innslag = $monstring->getInnslag()->get( $_POST['innslag'], true );
$innslag->setStatus( 8 );

Write::saveStatus( $innslag );

$JSON->redirect = '?page=UKMdeltakere&edit='. $_POST['innslag'] .'#innslag_'. $_POST['innslag'];