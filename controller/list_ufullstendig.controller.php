<?php

$list_innslag = [];
$arrangement = UKMdeltakere::getArrangement();
UKMdeltakere::addViewData('arrangement', $arrangement);

foreach( $arrangement->getInnslagTyper( true ) as $type_innslag ) {
    $list_innslag[ $type_innslag->getKey() ] = $arrangement->getInnslag()->filterByType( $type_innslag, $arrangement->getInnslag()->getAllUfullstendige());
}
UKMdeltakere::addViewData('list_innslag', $list_innslag);