<?php

use UKMNorge\Arrangement\Arrangement;

require_once('UKM/Autoloader.php');

if ( !get_option('pl_id')) {
    UKMdeltakere::setAction('registrer_monstring');
} else {
    $monstring = new Arrangement(get_option('pl_id'));

    $ufullstendige = $monstring->getInnslag()->getAllUfullstendige();

    $stat = new stdClass();
    $stat->fullstendige = $monstring->getInnslag()->getAntall();
    $stat->ufullstendige = sizeof($ufullstendige);

    $TWIGdata['tab_active'] = isset($_GET['list']) ? $_GET['list'] : 'fullstendig';
    $TWIGdata['monstring'] = $monstring;
    $TWIGdata['stat'] = $stat;
}
