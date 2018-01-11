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

	$query = person_v2::getLoadQry(); // SELECT * FROM `smartukm_participant`
	
	
	
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
	$sql = new SQL( $query, ['search' => utf8_decode( $_POST['search'] ) ] );
	$res = $sql->run();
	
#	echo $sql->debug();
	
	while( $row = mysql_fetch_assoc( $res ) ) {
		$person = new person_v2( $row );
	
		$TWIGdata['results']['personer'][] = $person;
	}
}