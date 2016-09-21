<?php

// plugin_dir_path makes no sense but makes it work...
require_once(plugin_dir_path(__FILE__) .'../functions.php');

// Info om kontekst og aktuelt innslag
$monstring = new monstring_v2( get_option('pl_id') );
$innslag = new innslag_v2( $JSON->innslag_id, $monstring->getId() );

// Info for retur
$JSON->view = $_POST['view'];
$JSON->monstring = data_monstring( $monstring );
$JSON->innslag = data_innslag( $innslag );
$JSON->twigJS = 'twigJSunsupported';

switch( $_POST['view'] ) {
	case 'overview':
		require_once('overview.controller.php');
		break;
	case 'edit':
		require_once('edit.controller.php');
		break;
	case 'addToEvent':
		require_once('event.controller.php');
		break;
}