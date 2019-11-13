<?php

use UKMNorge\Arrangement\Arrangement;

$monstring = new Arrangement( get_option('pl_id') );
$collection = $monstring->getInnslag();
$TWIGdata['list_innslag'] = [];

foreach( $monstring->getInnslagTyper( true ) as $type_innslag ) {
	$TWIGdata['list_innslag'][ $type_innslag->getKey() ] = $collection->filterByStatus([0,1,2,3,4], $collection->filterByType( $type_innslag, $collection->getAllUfullstendige() ) );
}