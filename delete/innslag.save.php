<?php
/**
 * Meld av innslag
 *
 */

use UKMNorge\Arrangement\Arrangement;
use UKMNorge\Arrangement\Write as WriteArrangement;
use UKMNorge\Innslag\Write;

require_once('UKM/Autoloader.php');

$arrangement = new Arrangement( intval(get_option('pl_id') ));

$innslag = $arrangement->getInnslag()->get( $_POST['innslag'], true );
$arrangement->getInnslag()->fjern($innslag);

// Hjemme-arrangementet, og ikke videresendt
if( $innslag->getHomeId() == $arrangement->getId() ) {
    Write::meldAv( $innslag );
}
// Fjern videresending 
elseif( $innslag->getHomeId() != $arrangement->getId() ) {
    WriteArrangement::fjernInnslag($innslag);
}

$JSON->meldtAv = true;