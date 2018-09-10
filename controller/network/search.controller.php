<?php

if( $_SERVER['REQUEST_METHOD'] == 'POST' ) {
	require_once('UKM/sql.class.php');
	require_once('UKM/person.class.php');
	require_once('UKM/innslag.class.php');
	
	
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
		$query = innslag_v2::getLoadQuery(); 
	
		$query .= " WHERE `smartukm_band`.`b_name` $operand '#search'";
	} else {
		$key = 'personer';
		$query = person_v2::getLoadQuery(); // SELECT * FROM `smartukm_participant`
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
	$sql = new SQL( $query, ['search' => $_POST['search'] ] );
	$res = $sql->run();
	
	#echo $sql->debug();
	
	while( $row = SQL::fetch( $res ) ) {
		switch( $key ) {
			case 'personer':
				$objekt = new person_v2( $row );
				break;
			case 'innslag':
				$objekt = new innslag_v2( $row );
				break;
			default:
				throw new Exception('Kan ikke søke etter innslag av type '. $key );
		}
	
		$TWIGdata['results'][$key][] = $objekt;
	}
	$TWIGdata['searchtable'] = $key;
}
