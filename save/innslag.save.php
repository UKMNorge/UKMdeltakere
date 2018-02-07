<?php

require_once('UKM/write_innslag.class.php');

// Sjekk for påkrevd data:
if( empty($DATA['navn']) || empty($_POST['innslag']) || empty($DATA['kommune']) ) {
	throw new Exception("Innslag.save: Mangler noe data!");
}

$innslag = $monstring->getInnslag()->get( $_POST['innslag'] );

$innslag->setNavn( $DATA['navn'] );
if(isset($DATA['sjanger'])) {
	$innslag->setSjanger( $DATA['sjanger'] );
}
if( isset($DATA['tekniske_behov'] ) ) {
	$innslag->setTekniskeBehov( $DATA['tekniske_behov'] );
}
$innslag->setBeskrivelse( $DATA['beskrivelse'] );
$innslag->setKommune( $DATA['kommune'] );

write_innslag::save( $innslag );