<?php

require_once('UKM/monstring.class.php');
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

require_once('UKM/sql.class.php');

echo '<h1>Beregner målgrupper og dekning for '. $SEASON .'</h1>';

$kommuneQRY = new SQL("SELECT * FROM `smartukm_kommune`");
$kommuneRES = $kommuneQRY->run();

while( $kommune = SQL::fetch( $kommuneRES ) ) {
	echo '<h2>'. $kommune['name'].'</h2>';
	// FINN MALGRUPPE
	// Hent malgruppe
	$AR_start 	= $SEASON-21;
	$AR_stop	= $SEASON-12;
	$AR_stop	= $SEASON-9; // BEREGNER MÅLGRUPPE FRA 10-20
	
	$malgruppeSQL = new SQL("SELECT SUM(`count`) AS `malgruppe`
							FROM `ukm_befolkning`
							WHERE `k_id` = '#kommune'
							AND `year` > '#ar_start'
							AND `year` < '#ar_stop'",
							array('kommune'		=> $kommune['id'],
								  'ar_start' 	=> $AR_start,
								  'ar_stop'		=> $AR_stop)
							);
	$malgruppe = $malgruppeSQL->run('field','malgruppe');
	
	// MISSING
	$missing_monstring = new kommune_monstring( $kommune['id'], $SEASON );
	$missing_pl = $missing_monstring->monstring_get();
	
	$num_kommuner = sizeof( $missing_pl->g('kommuner') );
	$missing  = $missing_pl->get('pl_missing');
	$my_missing = floor( $missing / $num_kommuner );

	
	
	// KALKULER STØRRELSESGRUPPERING
	$size = $malgruppe > $STORLIMIT ? 'stor' : 'liten';
	
	// FINN DELTAKERANTALL
	$persQry = new SQL("SELECT COUNT(`stat_id`) AS `personer` 
						FROM `ukm_statistics`
						WHERE `k_id` = '#kommune'
						AND `season` = '#season'",
					   array('kommune' => $kommune['id'],
					   		 'season' => $SEASON)
					  );
	$personer = (int) $persQry->run('field','personer') + (int) $my_missing;
	
	// FINN DEKNINGSPROSENT
	if( (int)$personer && (int) $malgruppe > 0) {
		$enperson = 100 / (int) $malgruppe;
		$dekning = round($enperson * $personer, 2);
	} else {
		$dekning = 0;
	}
		
	
	$clean = new SQLdel('ukm_statistics_malgruppe', array('k_id' => $kommune['id'], 'season' => $SEASON));
	$clean->run();
	echo $clean->debug();
	
	$insert = new SQLins('ukm_statistics_malgruppe');
	$insert->add('k_id', $kommune['id']);
	$insert->add('f_id', $kommune['idfylke']);
	$insert->add('season', $SEASON);
	$insert->add('size', $size);
	$insert->add('malgruppe', $malgruppe);
	$insert->add('deltakere', $personer);
	$insert->add('prosent', $dekning);
	
	$insert->run();
	echo $insert->debug();
}

echo '<h1>FERDIG</h1>';