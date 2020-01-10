<?php

use UKMNorge\Arrangement\Arrangement;

$monstring = new Arrangement( get_option('pl_id') );
$collection = $monstring->getInnslag();
$TWIGdata['list_innslag'] = [];

foreach( $monstring->getInnslagTyper( true ) as $type_innslag ) {
    $TWIGdata['list_innslag'][ $type_innslag->getKey() ] = $collection->filterByType( $type_innslag, $collection->getAllUfullstendige());
// $TWIGdata['list_innslag'][ $type_innslag->getKey() ] = $collection->filterByStatus([7,6,5], $collection->filterByType( $type_innslag, $collection->getAllUfullstendige() ) );
}