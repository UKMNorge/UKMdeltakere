<?php  
/* 
Plugin Name: UKM Deltakere
Plugin URI: http://www.ukm-norge.no
Description: UKM Norge admin
Author: UKM Norge / M Mandal 
Version: 1.0 
Author URI: http://www.ukm-norge.no
*/
## HOOK MENU AND SCRIPTS

$tittellose_innslag = array(4,5,8,9);
if(is_admin()) {
	require_once('UKM/innslag.class.php');
	require_once('UKM/inc/phaseout.ico.inc.php');
	global $blog_id;
	if($blog_id != 1)
		add_action('UKM_admin_menu', 'UKMdeltakere_menu',200);

	require_once('UKM/inc/toolkit.inc.php');
	require_once('ajax.deltakere.php');
	add_action('wp_ajax_UKMdeltakere_gui', 'UKMdeltakere_gui');
	
	add_action('admin_init', 'UKMdeltakere_addnew', 300);	
}

function UKMdeltakere_addnew(){
	global $tittellose_innslag;
	if(isset($_GET['page'])&&$_GET['page']=='UKMdeltakere'&&isset($_GET['addnew'])) {
		
		if(in_array($_GET['addnew'], $tittellose_innslag)&&!isset($_GET['fornavn'])) {
			header("Location: ".admin_url('admin.php?page=UKMdeltakere&addperson='.$_GET['addnew']));
			exit();
		} elseif(get_option('site_type') != 'kommune' && !isset($_GET['kommune'])) {
			header("Location: ".admin_url('admin.php?page=UKMdeltakere&addnewforwarded='.$_GET['addnew']));
			exit();
		} else {
			if(get_option('site_type')=='kommune') {
				$m = new monstring(get_option('pl_id'));
				$kommuner = $m->g('kommuner');
				$kommuneid = $kommuner[0]['id'];
				$pl_id = get_option('pl_id');
			} else {
				$m = new kommune_monstring($_GET['kommune'], get_option('season'));
				$m = $m->monstring_get();
				$pl_id = $m->g('pl_id');
				$kommuneid = $_GET['kommune'];
			}
			$addnew = create_innslag($_GET['addnew'], get_option('season'), $pl_id, $kommuneid);
			switch(get_option('site_type')) {
				case 'fylke': 
					$band = new innslag($addnew,false);
					$band->videresend($pl_id, get_option('pl_id'));
					break;
				case 'land':
					die('St&oslash;tte ikke integrert');
			}
			if(isset($_GET['fornavn'])) {
				$pers = new person(false);
				$pers = $pers->getExistingPerson($_GET['fornavn'], $_GET['etternavn'], $_GET['mobil']);
				if (!$pers) {
					$pers = new person(false);
					$pers->create($addnew);
					
					$_POST['p_firstname'] = $_GET['fornavn'];
					$_POST['log_current_p_firstname'] = '';
					$pers->update('p_firstname');

					$_POST['p_lastname'] = $_GET['etternavn'];
					$_POST['log_current_p_lastname'] = '';
					$pers->update('p_lastname');
					
					$_POST['p_phone'] = $_GET['mobil'];
					$_POST['log_current_p_phone'] = '';
					$pers->update('p_phone');
				} else {
					$pers->relate($addnew);
				}
				$band = new innslag($addnew);
				
				$_POST['log_current_value_b_contact'] = 0;
				$_POST['b_contact'] = $pers->g('p_id');
				$band->update('b_contact');

				$_POST['log_current_value_b_name'] = 'Nytt innslag';
				$_POST['b_name'] = $_GET['fornavn'].' '.$_GET['etternavn'];
				$band->update('b_name');
				
				if(isset($_GET['kommune'])) {
					$_POST['log_current_value_b_kommune'] = 0;
					$_POST['b_kommune'] = $_GET['kommune'];
					$band->update('b_kommune');
					
					$_POST['log_current_value_p_kommune'] = 0;
					$_POST['p_kommune'] = $_GET['kommune'];
					$pers->update('p_kommune');
					
					if(get_option('site_type') != 'kommune') {
						$pl_from = new kommune_monstring($band->g('b_kommune'), get_option('season'));
						$pl_from = $pl_from->monstring_get();
						$res = $pers->videresend($pl_from->g('pl_id'), get_option('pl_id'), 0);
						$res = $band->videresend($pl_from->g('pl_id'), get_option('pl_id'));
					}
				}

			}

			
			header("Location: ".admin_url('admin.php?page=UKMdeltakere#rediger_'.$addnew));
			exit();			
		}
	}
}


## CREATE A MENU
function UKMdeltakere_menu() {
	global $UKMN;
	UKM_add_menu_page('monstring','Deltakere', 'Deltakere', 'editor', 'UKMdeltakere', 'UKMdeltakere', 'http://ico.ukm.no/people-menu.png',5);

	UKM_add_scripts_and_styles('UKMdeltakere', 'UKMdeltakere_scriptsandstyles' );
}
## INCLUDE SCRIPTS
function UKMdeltakere_scriptsandstyles() {
	wp_enqueue_style( 'jquery-ui-style', WP_PLUGIN_URL .'/UKMresources/css/jquery-ui-1.7.3.custom.css');
	wp_enqueue_style( 'jquery-ui-stylefont', WP_PLUGIN_URL .'/UKMdeltakere/deltakere.style.css');

	wp_enqueue_script('jquery');
	wp_enqueue_script('jqueryGoogleUI', 'http://ajax.googleapis.com/ajax/libs/jqueryui/1.9.2/jquery-ui.min.js');
	/*
wp_enqueue_script('jquery-ui-core');
	
	wp_enqueue_script('jquery-ui-sortable');
	wp_enqueue_script('jquery-ui-effects-core');
	
	wp_enqueue_script('jquery-ui-effects', '/wp-content/plugins/project_manager/scripts/ui.effects.js');
*/

	wp_enqueue_script('UKMdeltakere_script', WP_PLUGIN_URL . '/UKMdeltakere/deltakere.script.js' );
	wp_enqueue_script('UKMdeltakere_script_modernizer', WP_PLUGIN_URL . '/UKMdeltakere/modernizr.input.js');
	wp_enqueue_script('UKMdeltakere_script_temp', WP_PLUGIN_URL . '/UKMdeltakere/temp.script.js' );
}

## SHOW STATS OF PLACES
function UKMdeltakere() {
	global $UKMN, $lang;
	
	if(isset($_GET['addperson'])) {
		require_once('addperson.gui.php');
	} elseif(isset($_GET['addnewforwarded'])) {
		require_once('addnewforwarded.gui.php');	
	} else {	
		require_once('UKM/form.class.php');
		require_once('UKM/inc/toolkit.inc.php');
		require_once('UKM/inc/ukmlog.inc.php');
		require_once('deltakere.gui.php');
	
		echo '<div class="wrap">'.UKMdeltakere_list().'</div>';	
	}
}

function UKMdeltakere_tittelgui($btid,$kategori) {
	$felter['musikk']	= array('tittel'=>'Tittel',
								'melodi_av'=>'Melodi av',
								'tekst_av'=>'Tekst av',
								'varighet'=>'Varighet');
	$felter['annet']	= array('tittel'=>'Tittel',
								'melodi_av'=>'Evt melodi',
								'tekst_av'=>'Evt tekst',
								'varighet'=>'Varighet');
	$felter['dans']		= array('tittel'=>'Tittel',
								'koreografi'=>'Koreografi',
								'varighet'=>'Varighet');
	$felter['teater']	= array('tittel'=>'Tittel',
								'melodi_av'=>'Komponist',
								'tekst_av'=>'Forfatter',
								'varighet'=>'Varighet');
	$felter['litteratur']=array('tittel'=>'Tittel',
								'melodi_av'=>'Evt komponist',
								'tekst_av'=>'Evt medforfatter',
								'varighet'=>'Varighet');
	$felter[6]			= array('tittel'=>'Navn på gruppen/artisten',
								'beskrivelse'=>'Beskrivelse');
	$felter[3]			= array('tittel'=>'Tittel',
								'beskrivelse'=>'Beskrivelse',
								'teknikk'=>'Teknikk',
								'type'=>'Type');
	$felter[2]			= array('tittel'=>'Tittel',
								'format'=>'Format',
								'varighet'=>'Varighet');
	switch($btid) {
		case 1:
			switch(strtolower($kategori)){
				case 'musikk':		return $felter['musikk'];
				case 'dans':		return $felter['dans'];
				case 'teater':		return $felter['teater'];
				case 'litteratur':	return $felter['litteratur'];
				default: 		return $felter['annet'];
			}
			break;
		case 2:
		case 3:
		case 6:
			return $felter[$btid];
	}
	return array();
}
?>
