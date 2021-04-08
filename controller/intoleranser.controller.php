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
            $data_intoleranse->med[ $id ] = UKMVideresending::getIntoleransePersonData( $person, $allergi );
        } else {
            $data_intoleranse->uten[ $id ] = UKMVideresending::getIntoleransePersonData( $person );
        }
    }
}    

ksort($data_intoleranse->med);
ksort($data_intoleranse->uten);

UKMdeltakere::addViewData('personer', $data_intoleranse);
UKMVideresending::addViewData('allergener_standard', Allergener::getStandard());
UKMVideresending::addViewData('allergener_kulturelle', Allergener::getKulturelle());