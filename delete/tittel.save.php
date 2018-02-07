<?php

require_once('UKM/write_monstring.class.php');
require_once('UKM/write_innslag.class.php');
require_once('UKM/write_tittel.class.php');
require_once('UKM/titler.collection.php'); 

$innslag = $monstring->getInnslag()->get( $_POST['innslag'] );

$tittel = $innslag->getTitler()->get( $_POST['object_id'] );

$innslag->getTitler()->fjern( $tittel );

write_innslag::saveTitler( $innslag );
/*
switch($innslag->getType()->getKey()) {
	case 'musikk':
	case 'scene':
	case 'dans':
	case 'teater':
	case 'litteratur':
		$tittel = new write_tittel( $_POST['object_id'], 'smartukm_titles_scene');
		$innslag->getTitler()->fjern($tittel);
		break;
	case 'video':
		$tittel = new write_tittel( $_POST['object_id'], 'smartukm_titles_video');
		$innslag->getTitler()->fjern($tittel);
		break;
	case 'utstilling':
		$tittel = new write_tittel( $_POST['object_id'], 'smartukm_titles_exhibition');
		$innslag->getTitler()->fjern($tittel);
		break;
	default:
		throw new Exception("Tittel-save: Kan kun fjerne titler for scene, video eller utstilling, ikke ". $innslag->getType()->getKey());
}
*/