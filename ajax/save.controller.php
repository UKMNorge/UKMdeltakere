<?php

global $current_user;
get_currentuserinfo();  

require_once('UKM/logger.class.php'); 

UKMlogger::setID( 'wordpress', $current_user->ID, get_option('pl_id') );


$DATA = [];
if(is_array($_POST['formData'])) {
	foreach( $_POST['formData'] as $field ) {
		$DATA[ $field['name'] ] = $field['value'];
	}
}

switch( $_POST['doSave'] ) {

	#### DELETE
	case 'deletePerson':
		require_once( plugin_dir_path( __FILE__ ) .'../delete/person.save.php' );
		break;

	#### SAVE AND UPDATE
	case 'innslag':
		require_once( plugin_dir_path( __FILE__ ) .'../save/innslag.save.php' );
		break;
	case 'personAdd':
		require_once( plugin_dir_path( __FILE__ ). '../save/personAdd.save.php');
		break;
	case 'personAddExisting':
		require_once( plugin_dir_path( __FILE__ ). '../save/personAddExisting.save.php');
		break;
	case 'person':
		require_once( plugin_dir_path( __FILE__ ). '../save/person.save.php' );
		break;
	case 'person':
		require_once( plugin_dir_path( __FILE__ ). '../save/person.save.php' );
		break;
	case 'tittellos':
	default:
		throw new Exception("NOT IMPLEMENTED!");
		break;

}