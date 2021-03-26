<?php

use UKMNorge\Innslag\Personer\Person;
use UKMNorge\Sensitivt\Intoleranse;
use UKMNorge\Allergener\Allergener;

require_once('UKM/Autoloader.php');

// SETUP SENSITIVT-REQUESTER
$requester = new UKMNorge\Sensitivt\Requester(
    'wordpress', 
    wp_get_current_user()->ID,
    get_option('pl_id')
);
UKMNorge\Sensitivt\Sensitivt::setRequester( $requester );

$data_intoleranse = new stdClass();
$data_intoleranse->med = [];
$data_intoleranse->uten = [];

foreach(UKMdeltakere::getArrangement()->getInnslag()->getAll() as $innslag) {
    $personer = $innslag->getPersoner()->getAll();

    foreach($personer as $person) {
        $allergi = $person->getSensitivt( $requester )->getIntoleranse();

        $id = $person->getNavn() .'-'. $person->getId();
        if( $allergi->har() ) {
            $data_intoleranse->med[ $id ] = getIntoleransePersonData( $person, $allergi );
        } else {
            $data_intoleranse->uten[ $id ] = getIntoleransePersonData( $person );
        }
    }
}    

/**
 * Hent et TwigJS-objekt av en person og dens allergier
 *
 * @param Person $person
 * @param Intoleranse $allergi
 * @return stdClass
 */
function getIntoleransePersonData(Person $person, Intoleranse $allergi = null)
{
    $data = new stdClass();
    $data->ID = $person->getId();
    $data->navn = $person->getNavn();
    $data->mobil = $person->getMobil();
    if (!is_null($allergi)) {
        $data->intoleranse_liste = $allergi->getListe();
        $data->intoleranse_human = $allergi->getListeHuman();
        $data->intoleranse_tekst = $allergi->getTekst();
    }
    return $data;
}


ksort($data_intoleranse->med);
ksort($data_intoleranse->uten);

UKMdeltakere::addViewData('personer', $data_intoleranse);
UKMVideresending::addViewData('allergener_standard', Allergener::getStandard());
UKMVideresending::addViewData('allergener_kulturelle', Allergener::getKulturelle());