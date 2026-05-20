<?php
/**
 * Meld av innslag
 *
 */

use UKMNorge\Arrangement\Arrangement;
use UKMNorge\Arrangement\Write as WriteArrangement;
use UKMNorge\Innslag\Write;
use UKMNorge\Innslag\Personer\Write as WritePerson;
use UKMNorge\Videresending\VideresendingNominasjon;
use UKMNorge\Videresending\Write as VideresendingNominasjonWrite;

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
    foreach( $innslag->getPersoner()->getAll() as $person ) {
        $nominasjoner = VideresendingNominasjon::getAllenForArrangementInnslagPerson($arrangement->getId(), $innslag->getId(), $person->getId());
        foreach( $nominasjoner->getAll() as $nominasjon ) {
            $nominasjon->setGodkjent(false);
            $nominasjon->setStatus(VideresendingNominasjon::STATUS_HOS_DELTAKER);
            VideresendingNominasjonWrite::save($nominasjon);
        }
        WritePerson::fjern( $person );
    }
    WriteArrangement::fjernInnslag($innslag);
}

$JSON->meldtAv = true;