<?php
### innslag_new.controller.php

use UKMNorge\Arrangement\Arrangement;
use UKMNorge\Database\SQL\Query;
use UKMNorge\Geografi\Fylker;
use UKMNorge\Innslag\Personer\Person;
use UKMNorge\Innslag\Typer\Typer;

require_once('UKM/Autoloader.php');

$type = $_POST['type'];
$arrangement = new Arrangement(get_option('pl_id'));

$JSON->innslag_type = $type;
$JSON->type = data_type( Typer::getByKey($type) );

// TODO: Fix this:
// $JSON->personer = $arrangement->getPersoner();
// $JSON->personer = $arrangement->getPersoner()->getAll();
// WORKAROUND:
$personer = [];

// Tidligere personer
if( in_array($arrangement->getEierType(), ['kommune','fylke']) ) {
	$sql = new Query("SELECT * FROM `smartukm_participant`
					WHERE `p_kommune` IN('#kommuner')",
					array('kommuner'=> implode(',', $arrangement->getKommuner()->getIdArray()) )
				);
	$res = $sql->run();

	if( $res ) {
		while( $row = Query::fetch( $res ) ) {
			$personer[ $row['p_id'] ] = data_person( new Person( $row ) );
		}
	}
}

// Personer fra påmeldte innslag
foreach( $arrangement->getInnslag()->getAll() as $innslag ) {
	foreach( $innslag->getPersoner()->getAll() as $person ) {
		$personer[ $person->getId() ] = data_person( $person );
	}
}
// Personer fra halv-påmeldte innslag
foreach( $arrangement->getInnslag()->getAllUfullstendige() as $innslag ) {
	foreach( $innslag->getPersoner()->getAll() as $person ) {
		$personer[ $person->getId() ] = data_person( $person );
	}
}
$JSON->personer = $personer;
$JSON->nedslagsfelt = $arrangement->getMetaValue('nedslagsfelt');

if( $arrangement->getEierType() == 'land' || $arrangement->getMetaValue('nedslagsfelt') == 'land' ) {
	$JSON->fylker = [];
	foreach( Fylker::getAll() as $fylke ) {
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

$real_type = Typer::getByKey($type);
if( $real_type->erGruppe() ) {
    $JSON->twigJS = 'innslagtittel';
} else {
    $JSON->twigJS = 'innslagtittellos';
    $innslag_type = Typer::getByName($type);
    if( $innslag_type->harFunksjoner() ) {
        $funksjoner = $innslag_type->getFunksjoner();
        $JSON->type_nicename = $innslag_type->getNavn();
        $JSON->funksjoner = array_keys($funksjoner);
        $JSON->funksjonsnavn = $funksjoner;
    }
}