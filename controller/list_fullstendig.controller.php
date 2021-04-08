<?php

use UKMNorge\Arrangement\Arrangement;

UKMdeltakere::require('controller/layout.controller.php');

if( isset( $_GET['create'] ) ) {
	UKMdeltakere::addViewData('doAdd', $_GET['create']);
}
if( isset( $_GET['edit'] ) ) {
	UKMdeltakere::addViewData('doEdit', $_GET['edit']);
}


$list_innslag = [];
$arrangement = UKMdeltakere::getArrangement();
UKMdeltakere::addViewData('arrangement', $arrangement);

foreach( $arrangement->getInnslagTyper( true ) as $type_innslag ) {
	$list_innslag[ $type_innslag->getKey() ] = $arrangement->getInnslag()->getAllByType( $type_innslag );
}
UKMdeltakere::addViewData('list_innslag', $list_innslag);
