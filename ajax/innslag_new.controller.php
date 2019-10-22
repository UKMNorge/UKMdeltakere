<?php
### innslag_new.controller.php

use UKMNorge\Database\SQL\Query;
use UKMNorge\Geografi\Fylker;
use UKMNorge\Innslag\Personer\Person;
use UKMNorge\Innslag\Typer;

require_once('UKM/Autoloader.php');

$type = $_POST['type'];
$monstring = new monstring_v2(get_option('pl_id'));

$JSON->innslag_type = $type;

// TODO: Fix this:
// $JSON->personer = $monstring->getPersoner();
// $JSON->personer = $monstring->getPersoner()->getAll();
// WORKAROUND:
$personer = [];

if( $monstring->getType() != 'land' ) {
	$sql = new Query("SELECT * FROM `smartukm_participant`
					WHERE `p_kommune` IN('#kommuner')",
					array('kommuner'=> implode(',', $monstring->getKommuner()->getIdArray()) )
				);
	$res = $sql->run();

	if( $res ) {
		while( $row = Query::fetch( $res ) ) {
			$personer[ $row['p_id'] ] = data_person( new Person( $row ) );
		}
	}
}

foreach( $monstring->getInnslag()->getAll() as $innslag ) {
	foreach( $innslag->getPersoner()->getAll() as $person ) {
		$personer[ $person->getId() ] = data_person( $person );
	}
}
foreach( $monstring->getInnslag()->getAllUfullstendige() as $innslag ) {
	foreach( $innslag->getPersoner()->getAll() as $person ) {
		$personer[ $person->getId() ] = data_person( $person );
	}
}
$JSON->personer = $personer;

if( $monstring->getType() == 'land' ) {
	$JSON->fylker = [];
	foreach( Fylker::getAllInkludertFalske() as $fylke ) {
		$data = new stdClass();
		$data->id = $fylke->getId();
		$data->navn = $fylke->getNavn();
		$data->kommuner = [];
		
		foreach( $fylke->getKommuner() as $kommune ) {
			$data_kommune = new stdClass();
			$data_kommune->id = $kommune->getId();
			$data_kommune->navn = $kommune->getNavn();
			$data->kommuner[] = $data_kommune;
		}
		
		$JSON->fylker[] = $data;
	}
}


switch( $type ) {
	case 'scene':
	case 'musikk':
	case 'dans':
	case 'teater':
	case 'litteratur':
	case 'film':
	case 'video':
	case 'utstilling':
		$JSON->twigJS = 'innslagtittel';
		break;
	case 'konferansier':
	// Mulig vi også må ha sceneteknikk her
		$JSON->twigJS = 'innslagkonferansier';
		break;
	case 'nettredaksjon':
	case 'arrangor':
	case 'ressurs':
		$JSON->twigJS = 'innslagtittellos';
		$iType = Typer::getByName($type);
		$funksjoner = $iType->getFunksjoner();

		$JSON->type_nicename = $iType->getNavn();
		$JSON->funksjoner = array_keys($funksjoner);
		$JSON->funksjonsnavn = $funksjoner;
		break;
	case 'matkultur':
		throw new Exception("Matkultur må midlertidig meldes på av deltakerne selv. Dette vil bli mulig også herfra så fort vi har fått rettet feilen.");
	default:
		throw new Exception("Fant ikke rett skjema for ".$type);
}
