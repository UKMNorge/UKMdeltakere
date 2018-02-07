<?php

require_once('UKM/write_tittel.class.php');
require_once('UKM/write_innslag.class.php');
require_once('UKM/write_monstring.class.php');

$innslag = $monstring->getInnslag()->get( $_POST['innslag'] );


// Skal vi lagre en ny tittel?
if(null == $DATA['tittel_id']) {
	$DATA['tittel_id'] = write_tittel::create( $innslag );
}
// Last inn tittel-objekt fra innslaget
$tittel = $innslag->getTitler()->get( $DATA['tittel_id'] );

// SETT DATA
$tittel->setTittel($DATA['tittel']);

switch( $innslag->getType()->getKey() ) {
	case 'dans':
		$tittel->setVarighet($DATA['lengde']); // I sekunder
		$tittel->setSelvlaget("1" == $DATA['selvlaget']); // true / false
		$tittel->setKoreografiAv($DATA['koreografi']);
		break;
	
	case 'film':
	case 'video':
		$tittel->setVarighet($DATA['lengde']); // I sekunder
		// Setter ikke format?
		#$tittel->setFormat($DATA['format']);
		break;
	
	case 'litteratur':
		$tittel->setVarighet($DATA['lengde']); // I sekunder
		$tittel->setLitteraturLesOpp("1" == $DATA['leseopp']); // true / false
		$tittel->setTekstAv($DATA['tekstforfatter']);
		break;
	
	case 'musikk':
		$tittel->setVarighet($DATA['lengde']); // I sekunder
		$tittel->setInstrumental('instrumental' == $DATA['sangtype'] ? true : false); // Instrumental eller sang
		$tittel->setSelvlaget("1" == $DATA['selvlaget']); // true / false
		$tittel->setTekstAv($DATA['tekstforfatter']);
		$tittel->setMelodiAv($DATA['melodiforfatter']);
		break;
	
	case 'scene':
		$tittel->setVarighet($DATA['lengde']); // I sekunder
		break;
	
	case 'teater':
		$tittel->setVarighet($DATA['lengde']); // I sekunder
		$tittel->setSelvlaget("1" == $DATA['selvlaget']); // true / false
		$tittel->setTekstAv($DATA['tekstforfatter']);
		break;
	
	case 'utstilling':
		$tittel->setType($DATA['teknikk']);
		$tittel->setBeskrivelse($DATA['beskrivelse']);
		break;

	default: 
		throw new Exception('Kunne ikke sette tittel-egenskaper for ukjent type tittel ('. $innslag->getType()->getKey() .')');
}

write_tittel::save( $tittel );


// Skal alltid videresende til "min" mønstring, ikke festivalen e.l.
// da dette er leggTil, ikke videresending.
throw new Exception('TODO: legg til tittel i innslaget');
$innslag->getTitler()->leggTil( $tittel );