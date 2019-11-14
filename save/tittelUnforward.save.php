<?php

use UKMNorge\Innslag\Titler\Write;

$innslag = $monstring->getInnslag()->get( $_POST['innslag'], true );
$tittel = $innslag->getTitler()->get( $_POST['object_id'] );

$innslag->getTitler()->fjern( $tittel );

try {
	Write::fjern( $tittel );
	/**
	 * Prøv å reloade innslaget, for å se om det nå er fullstendig avmeldt.
	 * (Som kan skje når man sletter siste tittel fra mønstringen.)
	**/
	$monstring->resetInnslagCollection();
	$innslag = $monstring->getInnslag()->get( $_POST['innslag'], true );
} catch( Exception $e  ) {
	if( $e->getCode() == 2 ) {
		$JSON->meldtAv = true;
	} else {
		throw $e;
	}
}