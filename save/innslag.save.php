<?php

require_once('UKM/write_innslag.class.php');

$innslag = new write_innslag( $_POST['innslag'] );

$innslag->setNavn( $DATA['navn'] );
if(isset($DATA['sjanger'])) {
	$innslag->setSjanger( $DATA['sjanger'] );
}
$innslag->setBeskrivelse( $DATA['beskrivelse'] );
$innslag->setKommune( $DATA['kommune'] );

$innslag->save();
