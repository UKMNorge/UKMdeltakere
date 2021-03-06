<?php

use UKMNorge\Database\SQL\Delete;
use UKMNorge\Database\SQL\Insert;
use UKMNorge\Database\SQL\Query;

require_once('UKM/Autoloader.php');
date_default_timezone_set('Europe/Oslo');
ini_set('display_errors', true);

if(isset($_GET['season'])) {
	$SEASON = $_GET['season'];
} else {
	if( (int) date('m') > 10)
		$SEASON = (int) date('Y') + 1;
	else
		$SEASON = (int) date('Y');
}
$STORLIMIT = 3000;

echo '<h1>Beregner målgrupper og dekning for '. $SEASON .'</h1>';

$kommuneQRY = new Query("SELECT * FROM `smartukm_kommune`");
$kommuneRES = $kommuneQRY->run();

while( $kommune = Query::fetch( $kommuneRES ) ) {
	echo '<h2>'. $kommune['name'].'</h2>';
	$missing_monstring = new kommune_monstring( $kommune['id'], $SEASON );
	$missing_pl = $missing_monstring->monstring_get();
	
	$num_kommuner = sizeof( $missing_pl->g('kommuner') );
	$missing  = $missing_pl->get('pl_missing');
	$my_missing = floor( $missing / $num_kommuner );
	
	$clean = new Delete('ukm_statistics_missing', array('k_id' => $kommune['id'], 'season' => $SEASON));
	$clean->run();
	echo $clean->debug();
	
	$insert = new Insert('ukm_statistics_missing');
	$insert->add('k_id', $kommune['id']);
	$insert->add('f_id', $kommune['idfylke']);
	$insert->add('season', $SEASON);
	$insert->add('missing', $my_missing);

	$insert->run();
	echo $insert->debug();
}

echo '<h1>FERDIG</h1>';