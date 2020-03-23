<?php

use UKMNorge\Arrangement\Arrangement;

require_once('UKM/Autoloader.php');

// plugin_dir_path makes no sense but makes it work...
require_once(plugin_dir_path(__FILE__) .'../functions.php');

// Info om kontekst og aktuelt innslag
$monstring = new Arrangement( intval(get_option('pl_id') ));

// Info for retur
$JSON->view = $_POST['view'];
$JSON->monstring = data_monstring( $monstring );

if( null == $JSON->innslag_id) { 
	require_once('innslag_new.controller.php');
}
else {	
	// Mer info om kontekst og innslag
	if( is_numeric( $JSON->innslag_id ) ) {
		$innslag = $monstring->getInnslag()->get( $JSON->innslag_id, true );
		// Mer info for retur
		$JSON->innslag = data_innslag( $innslag );
	} else {
		$innslag = null;
	}
	$JSON->twigJS = 'unsupported';

	switch( $_POST['view'] ) {
		// INNSLAG
		case 'innslag_new':
			require_once('innslag_new.controller.php');
			break;
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
