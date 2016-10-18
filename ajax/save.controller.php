<?php

global $current_user;
get_currentuserinfo();  

require_once('UKM/logger.class.php'); 

UKMlogger::setID( 'wordpress', $current_user->ID, get_option('pl_id') );


$DATA = [];
if(is_array($_POST['formData'])) {
	foreach( $_POST['formData'] as $field ) {
		// TODO: Denne håndterer ikke array som verdier (ie. funksjoner[] som navn).
		$DATA[ $field['name'] ] = $field['value'];
	}
}

switch( $_POST['doSave'] ) {

	#### DELETE SOMETHING
	case 'deletePerson':
		require_once( plugin_dir_path( __FILE__ ) .'../delete/person.save.php' );
		break;
	case 'deleteTitle':
		require_once( plugin_dir_path( __FILE__ ) .'../delete/tittel.save.php' );
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
	case 'contact':
	    require_once( plugin_dir_path( __FILE__ ). '../save/contact.save.php' );
	    break;
	case 'tittellos':
		require_once( plugin_dir_path( __FILE__ ). '../save/innslag_tittellos.save.php');
		break;
	case 'musikkTittel':
		require_once( plugin_dir_path( __FILE__ ). '../save/tittel_musikk.save.php');
		break;
		break;
	default:
		throw new Exception("NOT IMPLEMENTED!");
		break;

}