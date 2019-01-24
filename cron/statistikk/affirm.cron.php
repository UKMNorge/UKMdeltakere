<?php

$time_limit = 1200; // 1200s = 20min
ignore_user_abort(true);
ini_set('max_execution_time', $time_limit);
set_time_limit( $time_limit );
ob_start();

	$DEBUG = isset( $_GET['debug'] );
	
	if(isset($_GET['season'])) {
		$SEASON = $_GET['season'];
	} else {
		if( (int) date('m') > 10)
			$SEASON = (int) date('Y') + 1;
		else
			$SEASON = (int) date('Y');
	}

	require_once('UKM/monstringer.class.php');
	require_once('UKM/monstring.class.php');
	require_once('UKM/innslag.class.php');
	require_once('UKM/person.class.php');
	require_once('UKM/sql.class.php');
	
	function echo_flush( $string, $indent=0, $type='p' ) {
		if( defined('STDIN') ) {
			$nbsp = '';
			for($i=0; $i<$indent; $i++) {
				$nbsp .= ' ';
			}
			$string = $nbsp . $string;
			
			switch( $type ) {
				case 'p':
					echo $string . PHP_EOL;
					break;
				case 'h4':
					echo '#';
				case 'h3':
					echo '#';
				case 'h2':
					echo '#';
				case 'h1':
					echo '#' . mb_strtoupper( $string ) . PHP_EOL;
					break;
			}
		} elseif( isset( $_GET['print'] ) ) {
			$nbsp = '';
			for($i=0; $i<$indent; $i++) {
				$nbsp .= ' &nbsp; ';
			}
			$string = $nbsp . $string;

			switch( $type ) {
				case 'p':
					echo $string . '<br />';
					break;
				case 'h2':
				case 'h1':
					echo '<'. $type .'>' . $string . '</'. $type .'>';
					break;
			}			
		}
		
		ob_flush();
		flush();
	}
	
	function find_sex($first_name) {
		$first_name = strtoupper($first_name);
		
		$qry = "SELECT `kjonn` from ukm_navn" .
			  " WHERE `navn` = '" . $first_name ."' ";
		
		$qry = new SQL($qry);
		$res = $qry->run('field','kjonn');
		
		if ($res == null)
			$res = 'unknown';
		
		return $res;
		
	}
	
	
	$monstringer = new monstringer($SEASON);
	
	// Henter alle monstringer fra kommunenivaa
	$monstringer = $monstringer->etter_kommune();
	
	echo_flush( 'Loop alle mønstringer', 0, 'h1');
	$TEST_COUNT = 0;
	while( ($r = SQL::fetch($monstringer)) && $TEST_COUNT < 10) {
		$monstring = new monstring($r['pl_id']);
		echo_flush( $monstring->g('pl_name'), 0, 'h2' );
		// For hvert innslag i en monstring ...
		foreach ($monstring->innslag() as $innslag_inn) {
			$innslag = new innslag($innslag_inn["b_id"]);
			$innslag->loadGeo();
			echo_flush( 'INNSLAG: '. $innslag->g('b_name'), 1, 'h3');
			foreach ($innslag->personer() as $p) { // behandle hver person
				$person = new person($p["p_id"]);
				echo_flush( 'PERSON: '. $person->g('p_firstname') .' '. $person->g('p_lastname'), 2, 'h4' );
				$age = $person->getAge();
				if($age == '25+') 
					$age = 0;
				
				$first_name = $person->get("p_firstname");
				$first_name = explode(" ", str_replace("-", " ", $first_name) );
				$first_name = $first_name[0];
				
				$time = $innslag->get('time_status_8');
				
				if (strlen($time) <= 1) {
					$time = $SEASON."-01-01T00:00:01Z";
				} else {
					$time = date("Y-m-d\TH:i:s\Z" , $innslag->get('time_status_8'));
				}
				$time = str_replace(array('T','Z'),array(' ',''), $time);
				// var_dump($time);
				
				$kommuneID = $innslag->get("kommuneID");
				$fylkeID = $innslag->get("fylkeID");
				
				// PRE 2011 does not contain kommune in database.
				// Fake by selecting first kommune of mønstring
				if(empty($kommuneID)) {
					$kommuneID = $monstring->info['kommuner'][0]['id'];
					$fylkeID = $monstring->get('fylke_id');
				}
				
				$age = $person->getAge($monstring);
				if($age == '25+')
					$age = 25;
				$age = (int) $age;
				
				$stats_info = array(
					"b_id" => $innslag->get("b_id"), // innslag-id
					"p_id" => $person->get("p_id"), // person-id
					"k_id" => $kommuneID, // kommune-id
					"f_id" => $fylkeID, // fylke-id
					"bt_id" => $innslag->get("bt_id"), // innslagstype-id
					"subcat" => $innslag->get("b_kategori"), // underkategori
					"age" => $age, // alder
					"sex" => find_sex($first_name), // kjonn
					"time" =>  $time, // tid ved registrering
					"fylke" => 'false', // dratt pa fylkesmonstring?
					"land" => 'false', // dratt pa festivalen?
					"season" => $SEASON // sesong
				);
				
				// skal lagres i ukm_statistics-tabellen
				// var_dump($stats_info);
				
				// faktisk lagre det 
				$qry = "SELECT * FROM `ukm_statistics`" .
						" WHERE `b_id` = '" . $stats_info["b_id"] . "'" .
						" AND `p_id` = '" . $stats_info["p_id"] . "'" .
						#" AND `k_id` = '" . $stats_info["k_id"] . "'"  .
						" AND `season` = '" . $stats_info["season"] . "'";
				$sql = new SQL($qry);
				
				// echo($sql->debug());

				// Sjekke om ting skal settes inn eller oppdateres
				if (SQL::numRows($sql->run()) > 0)
					$sql_ins = new SQLins('ukm_statistics', array(
						"b_id" => $stats_info["b_id"], // innslag-id
						"p_id" => $stats_info["p_id"], // person-id
						"k_id" => $stats_info["k_id"], // kommune-id
						"season" => $stats_info["season"], // kommune-id
					) );
				else 
					$sql_ins = new SQLins("ukm_statistics");
				
				// Legge til info i insert-sporringen
				foreach ($stats_info as $key => $value) {
					$sql_ins->add($key, $value);
				}
				echo_flush( $sql_ins->debug(), 3);
				$sql_ins->run();
				
				if( $DEBUG ) {
					$TEST_COUNT += 1;
				}
			}
		}
	}
	// END WHILE
	echo_flush( "Script finished", 0, 'h1');

	// lol
?>
