<?php
// Skal relatere en person til et gitt innslag.

require_once('UKM/write_tittel.class.php');
require_once('UKM/write_innslag.class.php');
require_once('UKM/write_monstring.class.php');
require_once('UKM/monstringer.collection.php');

$innslag = $monstring->getInnslag()->get( $_POST['innslag'], true );
$tittel = $innslag->getTitler()->get( $_POST['object_id'] );

$innslag->getTitler()->fjern( $tittel );

try {
	write_tittel::fjern( $tittel );
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