<?php

require_once('UKM/Autoloader.php');

use UKMNorge\Database\SQL\Query;
use UKMNorge\Innslag\Innslag;
use UKMNorge\Innslag\Personer\Person;

if( $_SERVER['REQUEST_METHOD'] == 'POST' ) {
	$TWIGdata['search'] = new stdClass();
	$TWIGdata['search']->type = $_POST['type'];
	$TWIGdata['search']->search = $_POST['search'];


	if( strpos( $_POST['search'], '*' ) !== false ) {
		$operand = 'LIKE';
		$_POST['search'] = str_replace('*', '%', $_POST['search']);
	} else {
		$operand = '=';
	}

	if( $_POST['type'] == 'innslag' ) {
		$key = 'innslag';
		$query = Innslag::getLoadQuery(); 
	
		$query .= " WHERE `smartukm_band`.`b_name` $operand '#search'";
	} else {
		$key = 'personer';
		$query = Person::getLoadQuery(); // SELECT * FROM `smartukm_participant`
		switch( $_POST['type'] ) {
			default:
				$query .= "
				WHERE `p_firstname` $operand '#search'
				   OR `p_lastname` $operand '#search'";
				if( is_numeric( $_POST['search'] ) ) {
					$query .= "
						OR `p_phone` $operand '#search'";
				}
		}
	}
	$sql = new Query( $query, ['search' => $_POST['search'] ] );
	$res = $sql->run();
	
	#echo $sql->debug();
	
	while( $row = Query::fetch( $res ) ) {
		switch( $key ) {
			case 'personer':
				$objekt = new Person( $row );
				break;
			case 'innslag':
				$objekt = new Innslag( $row );
				break;
			default:
				throw new Exception('Kan ikke søke etter innslag av type '. $key );
		}
	
		$TWIGdata['results'][$key][] = $objekt;
	}
	$TWIGdata['searchtable'] = $key;
}
