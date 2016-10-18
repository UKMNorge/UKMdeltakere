<?php

require_once('UKM/write_tittel.class.php');
require_once('UKM/write_innslag.class.php');

$innslag = new write_innslag($_POST['innslag']);

// Skal vi lagre en ny tittel?
if(null == $DATA['tittel_id']) {
	$id = write_tittel::create( 'smartukm_titles_exhibition', $innslag->getId() );
	$tittel = new write_tittel( $id, 'smartukm_titles_exhibition' );
}
else {
	$tittel = new write_tittel( $DATA['tittel_id'], 'smartukm_titles_exhibition' );
}

$tittel->setTittel($DATA['tittel']);
$tittel->setType($DATA['teknikk']);
$tittel->setBeskrivelse($DATA['beskrivelse']);
$tittel->save();