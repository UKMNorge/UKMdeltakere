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
}
UKMdeltakere::addViewData('action', static::getAction());