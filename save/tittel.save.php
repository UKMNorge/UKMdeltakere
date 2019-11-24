<?php

use UKMNorge\Innslag\Titler\Write;

$innslag = $monstring->getInnslag()->get( $_POST['innslag'], true );

// Skal vi lagre en ny tittel?
if(null == $DATA['tittel_id']) {
	$DATA['tittel_id'] = Write::create( $innslag );
}
// Last inn tittel-objekt fra innslaget
$tittel = $innslag->getTitler()->get( $DATA['tittel_id'] );

// SETT DATA
$tittel->setTittel($DATA['tittel']);

if( $innslag->getType()->harTid() ) {
    $tittel->setVarighet($DATA['lengde']); // I sekunder
}

switch( $innslag->getType()->getKey() ) {
	case 'dans':
		$tittel->setSelvlaget("1" == $DATA['selvlaget']); // true / false
		$tittel->setKoreografiAv($DATA['koreografi']);
		break;	
	case 'litteratur':
		if( $DATA['leseopp'] != '1' ) {
			$tittel->setVarighet( 0 );
		}
		$tittel->setLesOpp( $DATA['leseopp'] == '1' ); // true / false
		$tittel->setTekstAv($DATA['tekstforfatter']);
		break;
	
	case 'musikk':
		$tittel->setInstrumental('instrumental' == $DATA['sangtype'] ? true : false); // Instrumental eller sang
		$tittel->setSelvlaget("1" == $DATA['selvlaget']); // true / false
		$tittel->setTekstAv($DATA['tekstforfatter']);
		$tittel->setMelodiAv($DATA['melodiforfatter']);
		break;
	
	case 'teater':
		$tittel->setSelvlaget("1" == $DATA['selvlaget']); // true / false
		$tittel->setTekstAv($DATA['tekstforfatter']);
		break;
	
	case 'utstilling':
		$tittel->setType($DATA['teknikk']);
		$tittel->setBeskrivelse($DATA['beskrivelse']);
		break;
}

Write::save( $tittel );

// For at collection skal være riktig (?)
#Write::leggTil( $tittel );