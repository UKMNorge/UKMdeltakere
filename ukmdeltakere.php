<?php
/* 
Plugin Name: UKM Deltakere
Plugin URI: http://www.ukm.no
Description: UKM Norge admin
Author: UKM Norge / M Mandal 
Version: 2.0
Author URI: http://www.ukm.no
*/

use Symfony\Bundle\SecurityBundle\Command\SetAclCommand;
use UKMNorge\Wordpress\Modul;

require_once('UKM/Autoloader.php');

define('PATH_PLUGIN_UKMDELTAKERE', dirname(__FILE__) . '/');

class UKMdeltakere extends Modul
{
	public static $path_plugin = null;
	public static $action = 'list_fullstendig';

	public static function hook()
	{

		add_action(
			'wp_ajax_UKMdeltakere_ajax',
			[static::class,	'ajax']
		);
		add_action(
			'network_admin_menu',
			[static::class, 'network_menu']
		);

		if (is_admin() && get_option('pl_id')) {
			add_action('admin_menu', [static::class, 'meny'], 1);
		}
	}

	/**
	 * Admin-meny
	 *
	 * @return void
	 */
	public static function meny()
	{
		// Deltakere-menyen
		$page = add_menu_page(
			'Påmeldte',
			'Påmeldte',
			'editor',
			'UKMdeltakere',
			[static::class, 'renderAdmin'],
			'dashicons-buddicons-buddypress-logo', #'//ico.ukm.no/people-menu.png',
			50
		);

		// Intoleranser-menyen
		$page_intoleranse = add_submenu_page(
			'UKMdeltakere',
			'Intoleranser',
			'Intoleranser',
			'editor',
			'UKMdeltakere_intoleranser', // Page variabel i path-en
			[static::class, 'renderIntoleranser']
		);


		// Personvern-menyen
		$page_personvern = add_submenu_page(
			'UKMdeltakere',
			'Personvern',
			'Personvern',
			'editor',
			'UKMdeltakere_personvern',
			[static::class, 'renderPersonvern']
		);

		add_action(
			'admin_print_styles-' . $page,
			['UKMdeltakere', 'scriptsandstyles']
		);

		add_action(
			'admin_print_styles-' . $page_personvern,
			['UKMdeltakere', 'scriptsandstyles_basic']
		);

		add_action(
			'admin_print_styles-' . $page_intoleranse,
			['UKMdeltakere', 'scriptsandstyles']
		);
		add_action(
			'admin_print_styles-' . $page_intoleranse,
			['UKMdeltakere', 'scriptsandstyles_intoleranse']
		);
	}
	public static function network_menu()
	{
		$page = add_menu_page(
			'Deltakere',
			'Deltakere',
			'superadmin',
			'UKMdeltakere_network_search',
			[static::class, 'renderNetworkAdmin'],
			'dashicons-buddicons-buddypress-logo', #'//ico.ukm.no/people-menu.png',
			24
		);
		add_action('admin_print_styles-' . $page, 	['UKMdeltakere', 'scriptsandstyles']);
	}

	public static function renderNetworkAdmin()
	{
		static::setAction('network/search');
		return parent::renderAdmin();
	}


	/**
	 * Legg til melding til brukeren om at informasjonssiden må være oppdatert
	 *
	 * @param Array $meldinger
	 * @return Array $meldinger
	 */
	public static function meldinger($meldinger)
	{
		$forside = get_page_by_path('info');
		if (null == $forside) {
			return $meldinger;
		}

		if (get_option('UKMnettside_info_last_updated') < (int) mktime(0, 0, 0, 8, 1, get_site_option('season') - 1)) {
			$meldinger[] = array(
				'level' 	=> 'alert-warning',
				'header' 	=> 'Sjekk at informasjonssiden din er oppdatert',
				'link' 		=> 'edit.php?page=UKMnettside&action=infoside',
			);
		}

		return $meldinger;
	}

	public static function ajax()
	{
		$JSON = new stdClass();
		$JSON->innslag_id = $_POST['innslag'];

		$controller = dirname(__FILE__) . '/ajax/' . $_POST['do'] . '.controller.php';
		if (!file_exists($controller)) {
			$JSON->success = false;
			$JSON->message = 'Missing controller ' . $controller . '!';
		} else {
			$JSON->success = true;
			try {
				require_once('ajax/' . $_POST['do'] . '.controller.php');
			} catch (Exception $e) {
				$JSON->success = false;
				$JSON->message = $e->getMessage();
				$JSON->code = $e->getCode();
			}
		}

		$json_encoded = json_encode($JSON);
		if (false == $json_encoded) {
			$_JSON = $JSON; // failed json data
			$JSON = null;
			$JSON = new stdClass();
			$JSON->innslag_id = $_POST['innslag'];
			$JSON->success = false;
			switch (json_last_error()) {
				case JSON_ERROR_SYNTAX:
					$JSON->message = "JSON har syntaks-feil! Dette er en systemfeil, kontakt UKM Norge.";
					break;
				case JSON_ERROR_UTF8:
					// Try to convert to utf8 by traversing
					$re_encode = convert_array_to_utf8($_JSON);
					// Try to re-encode
					$json_encoded = json_encode($re_encode);
					// If still error, fail hard
					if (false == $json_encoded) {
						$JSON->message = "En UTF8/JSON-feil oppsto. Dette er en systemfeil, kontakt UKM Norge.";
					}
					// Restore original JSON data for encoding.
					else {
						$JSON = $_JSON;
					}
					break;
				default:
					$JSON->message = "En ukjent feil oppsto med JSON-enkodingen. Dette er en systemfeil, kontakt UKM Norge. JSON-feil: " . json_last_error();
			}
			$json_encoded = json_encode($JSON);
		}
		header('Content-Type: application/json');
		echo $json_encoded;
		wp_die(); // this is required to terminate immediately and return a proper response
		die(); // nødvendig?
	}

	public static function convert_array_to_utf8($mixed)
	{
		if (is_array($mixed)) {
			foreach ($mixed as $key => $value) {
				$mixed[$key] = convert_array_to_utf8($value);
			}
		} else if (is_string($mixed)) {
			return utf8_encode($mixed);
		}
		return $mixed;
	}

	## INCLUDE SCRIPTS
	public static function scriptsandstyles()
	{
		wp_enqueue_script('TwigJS');
		wp_enqueue_script('jQuery-fastlivefilter');

		wp_enqueue_script('UKMDELTA_tittelJS', 'https://delta.' . UKM_HOSTNAME . '/js/tittel.js');

		wp_enqueue_script('UKMdeltakere_css', static::getPluginUrl() . 'ukmdeltakere.js');
		wp_enqueue_style('UKMdeltakere_css', static::getPluginUrl() . 'ukmdeltakere.css');
		static::scriptsandstyles_basic();
	}

	public static function scriptsandstyles_basic()
	{
		wp_enqueue_script('WPbootstrap3_js');
		wp_enqueue_style('WPbootstrap3_css');
		wp_enqueue_style('WPbootstrap3_outlinebtn');
	}

	/**
	 * Intoleranser-menyen
	 *
	 * @return void
	 */
	public static function renderIntoleranser()
	{
		static::setAction('intoleranser');
		return static::renderAdmin();
	}

	public static function scriptsandstyles_intoleranse()
	{
		wp_enqueue_script('UKMVideresending_script_tilrettelegging');
	}


	/**
	 * Personvern-menyen
	 *
	 * @return void
	 */
	public static function renderPersonvern()
	{
		static::setAction('personvern');
		if (isset($_GET['send'])) {
			require_once('controller/personvern/' . basename($_GET['send']) . '.controller.php');
		}

		return parent::renderAdmin();
	}
}

UKMdeltakere::init(__DIR__);
UKMdeltakere::hook();
