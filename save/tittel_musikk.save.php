<?php

require_once('UKM/write_tittel.class.php');
require_once('UKM/write_innslag.class.php');

$innslag = new write_innslag($_POST['innslag']);

// Skal vi lagre en ny tittel?
if(null == $DATA['tittel_id']) {
	$DATA['tittel_id'] = write_tittel::create( $innslag );
}
$tittel = new write_tittel( $DATA['tittel_id'], $innslag->getType()->getTabell() );

$tittel->setTittel($DATA['tittel']);
$tittel->setVarighet($DATA['lengde']); // I sekunder
$tittel->setInstrumental('instrumental' == $DATA['sangtype'] ? true : false); // Instrumental eller sang
$tittel->setSelvlaget("1" == $DATA['selvlaget']); // true / false
$tittel->setTekstAv($DATA['tekstforfatter']);
$tittel->setMelodiAv($DATA['melodiforfatter']);
$tittel->save();