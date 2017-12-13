<?php
### personAdd.save.php
# Skal opprette nytt personobjekt og relatere det til et innslag, for så å kalle person.save.php.


require_once('UKM/write_person.class.php');
require_once('UKM/write_innslag.class.php');
require_once('UKM/write_monstring.class.php');
require_once('UKM/monstringer.collection.php');

$innslag = new write_innslag( $_POST['innslag'] );

$person = write_person::create($DATA['fornavn'], $DATA['etternavn'], $DATA['mobil'], write_person::fodselsdatoFraAlder($DATA['alder']), $DATA['kommune']);
$innslag->getPersoner()->leggTil($person);

// VIDERESEND AUTOMATISK TIL AKTIVT NIVÅ HVIS FYLKE ELLER LAND
if( get_option('site_type') == 'fylke' || get_option('site_type') == 'land' ) {
	$monstring_w_til = new write_monstring( get_option('pl_id') );
	if( get_option('site_type') == 'fylke' ) {
		$monstring_fra = monstringer_v2::kommune( $innslag->getKommune(), $monstring_w_til->getSesong() );
	} elseif( get_option('site_type') == 'land' ) {
		$monstring_fra = monstringer_v2::fylke( $innslag->getFylke(), $monstring_w_til->getSesong() );
	}
	$monstring_w_fra = new write_monstring( $monstring_fra->getId() );
	
	$innslag->getPersoner()->videresend($person, $monstring_w_til, $monstring_w_fra);
}

$_POST['object_id'] = $person->getId();
// Gjennomfører lagring av korrekt data.
require( plugin_dir_path( __FILE__ ). '../save/person.save.php' );
