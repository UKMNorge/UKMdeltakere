<?php

require_once('UKM/write_monstring.class.php');
require_once('UKM/write_innslag.class.php');
require_once('UKM/write_tittel.class.php');
require_once('UKM/titler.collection.php'); 

$innslag = new write_innslag($_POST['innslag']);
$monstring = new write_monstring(get_option('pl_id'));

$title_collection = $innslag->getTitler( $monstring );

switch($innslag->getType()->getKey()) {
	case 'musikk':
	case 'scene':
	case 'dans':
	case 'teater':
	case 'litteratur':
		$tittel = new write_tittel( $_POST['object_id'], 'smartukm_titles_scene');
		$title_collection->fjern($tittel, $monstring);
		break;
	case 'video':
		$tittel = new write_tittel( $_POST['object_id'], 'smartukm_titles_video');
		$title_collection->fjern($tittel, $monstring);
		break;
	case 'utstilling':
		$tittel = new write_tittel( $_POST['object_id'], 'smartukm_titles_exhibition');
		$title_collection->fjern($tittel, $monstring);
		break;
	default:
		throw new Exception("Tittel-save: Kan kun fjerne titler for scene, video eller utstilling, ikke ". $innslag->getType()->getKey());
}