<?php  
/* 
Plugin Name: UKM Deltakere
Plugin URI: http://www.ukm.no
Description: UKM Norge admin
Author: UKM Norge / M Mandal 
Version: 2.0
Author URI: http://www.ukm.no
*/

add_action( 'wp_ajax_UKMdeltakere_ajax', 'UKMdeltakere_ajax' );
add_action('network_admin_menu', 'UKMdeltakere_network_menu');

if( is_admin() && in_array( get_option('site_type'), array('kommune','fylke','land')) ) {
	require_once('UKM/inc/twig-js.inc.php');

	add_action('UKM_admin_menu', 'UKMdeltakere_menu',200);
	add_action('UKMWPDASH_shortcuts', 'UKMdeltakere_dash_shortcut', 30);
}

function UKMdeltakere_ajax() {
	$JSON = new stdClass();
	$JSON->innslag_id = $_POST['innslag'];
	
	$controller = dirname( __FILE__ ) .'/ajax/'. $_POST['do'] .'.controller.php';
	if( !file_exists( $controller ) ) {
		$JSON->success = false;
		$JSON->message = 'Missing controller '.$controller.'!';
	} else {
		$JSON->success = true;
		try {
			require_once('ajax/'. $_POST['do'] .'.controller.php');
		} catch( Exception $e ) {
			$JSON->success = false;
			$JSON->message = $e->getMessage();
		}
	}

	$json_encoded = json_encode($JSON);
	if( false == $json_encoded ) {
		$_JSON = $JSON; // failed json data
		$JSON = null;
		$JSON = new stdClass();
		$JSON->innslag_id = $_POST['innslag'];
		$JSON->success = false;
		switch(json_last_error() ) {
			case JSON_ERROR_SYNTAX:
				$JSON->message = "JSON har syntaks-feil! Dette er en systemfeil, kontakt UKM Norge.";
				break;
			case JSON_ERROR_UTF8:
				// Try to convert to utf8 by traversing
				$re_encode = convert_array_to_utf8( $_JSON );
				// Try to re-encode
				$json_encoded = json_encode( $re_encode );
				// If still error, fail hard
				if( false == $json_encoded ) {
					$JSON->message = "En UTF8/JSON-feil oppsto. Dette er en systemfeil, kontakt UKM Norge.";
				}
				// Restore original JSON data for encoding.
				else {
					$JSON = $_JSON;
				}
				break;
			default:
				$JSON->message = "En ukjent feil oppsto med JSON-enkodingen. Dette er en systemfeil, kontakt UKM Norge. JSON-feil: ".json_last_error();
		}
		$json_encoded = json_encode($JSON);
	}
	header('Content-Type: application/json');
	echo $json_encoded;
	wp_die(); // this is required to terminate immediately and return a proper response
	die(); // nødvendig?
}

function convert_array_to_utf8($mixed) {
		if (is_array($mixed)) {
		foreach ($mixed as $key => $value) {
			$mixed[$key] = convert_array_to_utf8($value);
		}
	} else if (is_string ($mixed)) {
		return utf8_encode($mixed);
	}
	return $mixed;
}

function UKMdeltakere_dash_shortcut( $shortcuts ) {	
	$shortcut = new stdClass();
	$shortcut->url = 'admin.php?page=UKMdeltakere';
	$shortcut->title = 'Deltakere';
	$shortcut->icon = '//ico.ukm.no/people-menu.png';
	$shortcuts[] = $shortcut;
	
	return $shortcuts;
}

## CREATE A MENU
function UKMdeltakere_menu() {
	global $UKMN;
	UKM_add_menu_page('monstring','Deltakere', 'Deltakere', 'editor', 'UKMdeltakere', 'UKMdeltakere', '//ico.ukm.no/people-menu.png',5);

	UKM_add_scripts_and_styles('UKMdeltakere', 'UKMdeltakere_scriptsandstyles' );
}
## INCLUDE SCRIPTS
function UKMdeltakere_scriptsandstyles() {
	wp_enqueue_script('TwigJS');
	wp_enqueue_script('jQuery-fastlivefilter');

	wp_enqueue_script('UKMDELTA_tittelJS', WP_PLUGIN_URL . '/UKMdeltakere/DELTA_tittel.js' );
	
	wp_enqueue_script('UKMdeltakere_css', WP_PLUGIN_URL . '/UKMdeltakere/ukmdeltakere.js' );
	wp_enqueue_style('UKMdeltakere_css', WP_PLUGIN_URL . '/UKMdeltakere/ukmdeltakere.css' );
	wp_enqueue_script('WPbootstrap3_js');
	wp_enqueue_style('WPbootstrap3_css');
	wp_enqueue_style('WPbootstrap3_outlinebtn');
}

## SHOW STATS OF PLACES
function UKMdeltakere() {
	$TWIGdata = [];
	
	require_once('controller/layout.controller.php');

/*
	if( get_option('site_type') != 'kommune' ) {
		echo TWIG( 'sorry.html.twig', $TWIGdata, dirname(__FILE__), true);
		return;	
	}
*/
	require_once('controller/list_'. $TWIGdata['tab_active'] .'.controller.php' );
	
	echo TWIG( 'list_'. $TWIGdata['tab_active']. '.html.twig', $TWIGdata, dirname(__FILE__), true);
	echo TWIGjs_simple( dirname(__FILE__) );

}

function UKMdeltakere_network_menu() {
	$page = add_menu_page(
		'Deltakere', 
		'Deltakere', 
		'superadmin', 
		'UKMdeltakere_network_search',
		'UKMdeltakere_network_search', 
		'//ico.ukm.no/people-menu.png',
		24
	);
	add_action( 'admin_print_styles-' . $page, 	'UKMdeltakere_scriptsandstyles' );

}
function UKMdeltakere_network_search() {
	$TWIGdata = [];
	
	require_once('controller/network/search.controller.php');

	echo TWIG( 'network/search.html.twig', $TWIGdata, dirname(__FILE__), true);
}
?>