<?php
$JSON->twigJS = 'personedit';

$person = $innslag->getPersoner()->getById( $_POST['object_id'] );
$JSON->person = data_person( $person );
$JSON->person->rolle = $person->getRolle();

if( $monstring->getType() == 'land' ) {
	$JSON->fylker = [];
	foreach( fylker::getAllInkludertFalske() as $fylke ) {
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