<?php

use UKMNorge\Arrangement\Arrangement;

if( isset( $_GET['create'] ) ) {
	$TWIGdata['doAdd'] = $_GET['create'];
}
if( isset( $_GET['edit'] ) ) {
	$TWIGdata['doEdit'] = $_GET['edit'];
}

$monstring = new Arrangement( get_option('pl_id') );
$TWIGdata['list_innslag'] = [];

foreach( $monstring->getInnslagTyper( true ) as $type_innslag ) {
	$TWIGdata['list_innslag'][ $type_innslag->getKey() ] = $monstring->getInnslag()->getAllByType( $type_innslag );
}