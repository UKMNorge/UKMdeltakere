<?php

use UKMNorge\Arrangement\Arrangement;
use UKMNorge\Log\Logger;

require_once('UKM/Autoloader.php');

global $current_user;
get_currentuserinfo();  

Logger::setID( 'wordpress', $current_user->ID, get_option('pl_id') );

$monstring = new Arrangement( intval(get_option( 'pl_id' ) )); 

$DATA = [];
if(is_array($_POST['formData'])) {
	foreach( $_POST['formData'] as $field ) {
		// TODO: Denne håndterer ikke array som verdier (ie. funksjoner[] som navn).
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
	case 'deleteFromEvent':
		require_once( plugin_dir_path( __FILE__) . '../delete/event.save.php' );
		break;
	case 'meldAvInnslag':
		require_once( plugin_dir_path( __FILE__ ) .'../delete/innslag.save.php' );
		break;

	#### ADD NEW INNSLAG
	case 'nyttInnslag':
		require_once( plugin_dir_path( __FILE__ ) .'../save/nytt_innslag.save.php' );
		break;

	case 'meldPaInnslag':
		require_once( plugin_dir_path( __FILE__ ) .'../save/innslag_meldpa.save.php' );
		break;


	#### SAVE AND UPDATE
	case 'innslag':
		require_once( plugin_dir_path( __FILE__ ) .'../save/innslag.save.php' );
		break;
	case 'addToEvent':
		require_once( plugin_dir_path( __FILE__ ) .'../save/event.save.php' );
		break;
	case 'personAdd':
		require_once( plugin_dir_path( __FILE__ ). '../save/personAdd.save.php');
		break;
	case 'personAddExisting':
		require_once( plugin_dir_path( __FILE__ ). '../save/personAddExisting.save.php');
		break;
	case 'personForward':
		require_once( plugin_dir_path( __FILE__ ) .'../save/personForward.save.php');
		break;
	case 'personUnforward':
		require_once( plugin_dir_path( __FILE__ ) .'../save/personUnforward.save.php');
		break;
	case 'person':
		require_once( plugin_dir_path( __FILE__ ). '../save/person.save.php' );
		break;
	case 'tittelForward':
		require_once( plugin_dir_path( __FILE__ ). '../save/tittelForward.save.php' );
		break;
	case 'tittelUnforward':
		require_once( plugin_dir_path( __FILE__ ). '../save/tittelUnforward.save.php' );
		break;
	case 'contact':
	    require_once( plugin_dir_path( __FILE__ ). '../save/contact.save.php' );
	    break;
	case 'tittellos':
		require_once( plugin_dir_path( __FILE__ ). '../save/innslag_tittellos.save.php');
		break;
	case 'musikkTittel':
	case 'dansTittel':
	case 'teaterTittel':
	case 'litteraturTittel':
	case 'sceneTittel':
	case 'filmTittel':
	case 'utstillingTittel':
		require_once( plugin_dir_path( __FILE__ ). '../save/tittel.save.php');
		break;
	default:
		throw new Exception("NOT IMPLEMENTED!");
		break;

}