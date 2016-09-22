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

	header('Content-Type: application/json');
	echo json_encode( $JSON );
	wp_die(); // this is required to terminate immediately and return a proper response
	die(); // nødvendig?
}

function UKMdeltakere_dash_shortcut( $shortcuts ) {	
	$shortcut = new stdClass();
	$shortcut->url = 'admin.php?page=UKMdeltakere';
	$shortcut->title = 'Deltakere';
	$shortcut->icon = 'http://ico.ukm.no/people-menu.png';
	$shortcuts[] = $shortcut;
	
	return $shortcuts;
}

## CREATE A MENU
function UKMdeltakere_menu() {
	UKM_add_menu_page('monstring','Deltakere', 'Deltakere', 'editor', 'UKMdeltakere', 'UKMdeltakere', 'http://ico.ukm.no/people-menu.png',5);

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

	require_once('controller/list_'. $TWIGdata['tab_active'] .'.controller.php' );
	
	echo TWIG( 'list_'. $TWIGdata['tab_active']. '.html.twig', $TWIGdata, dirname(__FILE__), true);
	echo TWIGjs( dirname(__FILE__) );

}
?>