<?php

// plugin_dir_path makes no sense but makes it work...
require_once(plugin_dir_path(__FILE__) .'../functions.php');

// Info om kontekst og aktuelt innslag
$monstring = new monstring_v2( get_option('pl_id') );

// Info for retur
$JSON->view = $_POST['view'];
$JSON->monstring = data_monstring( $monstring );

if( null == $JSON->innslag_id) { 
	require_once('innslag_new.controller.php');
}
else {	
	// Mer info om kontekst og innslag
	$innslag = new innslag_v2( $JSON->innslag_id, true );

	// Mer info for retur
	$JSON->innslag = data_innslag( $innslag, $monstring );
	$JSON->twigJS = 'twigJSunsupported';
	switch( $_POST['view'] ) {
		// INNSLAG
		case 'overview':
			require_once('innslag_overview.controller.php');
			break;
		case 'edit':
			require_once('innslag_edit.controller.php');
			break;
		case 'changeContact':
			require_once('contact_change.controller.php');
			break;
		case 'header':
			require_once('header.controller.php');
			break;
		// PERSONER
		case 'editPerson':
			require_once('person_edit.controller.php');
			break;
		case 'addPerson':
			require_once('person_add.controller.php');
			break;
		case 'addExistingPerson':
			require_once('person_addExisting.controller.php');
			break;
		// TITLER
		case 'addTitle':
			require_once('title_add.controller.php');
			break;
		case 'editTitle':
			require_once('title_edit.controller.php');
			break;
		// HENDELSER	
		case 'addToEvent':
			require_once('event_add.controller.php');
			break;
		case 'meldPa':
			require_once('innslag_meldpa.controller.php');
			break;
	}	
}
