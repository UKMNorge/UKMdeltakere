<?php

require_once('UKM/Autoloader.php');

if ( !get_option('pl_id')) {
    UKMdeltakere::setAction('registrer_monstring');
} else {
    $arrangement = UKMdeltakere::getArrangement();

    $ufullstendige = $arrangement->getInnslag()->getAllUfullstendige();

    $stat = new stdClass();
    $stat->fullstendige = $arrangement->getInnslag()->getAntall();
    $stat->ufullstendige = sizeof($ufullstendige);

    UKMdeltakere::addViewData('monstring', $arrangement);
    UKMdeltakere::addViewData('stat', $stat);


    $nominasjoner = [];
    $til = $arrangement = UKMdeltakere::getArrangement();
    $avsenderArrangementer = $til->getVideresending()->getAvsendere();

    $arrTyper = $til->getInnslagTyper();
    $arrTyper =  array_filter( $arrTyper->getAll() , function($type) use ($til) { return $type->kanHaNominasjon() && $til->harNominasjonFor($type); });
        
    foreach($arrTyper as $type) {
        foreach($avsenderArrangementer as $fra) {
            foreach($fra->getInnslag()->getAllByType($type) as $innslag) {
                
                $nominert = $innslag->getNominasjoner()->harTil($til->getId());
                $nominasjon = $nominert ? $innslag->getNominasjoner()->getTil($til->getId()) : false;
                $person = $innslag->getPersoner()->getSingle();
                $innslag->getType();

                if($nominasjon->erNominert()) {
                    $nominasjoner[] = ['nominasjon' => $nominasjon, 'innslag' => $innslag];
                }
            }
        }
    }

    UKMdeltakere::addViewData('nominasjoner', $nominasjoner);
}

UKMdeltakere::addViewData('action', static::getAction());