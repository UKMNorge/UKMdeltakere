<?php
require_once('UKM/monstring.class.php');

if( !get_option('pl_id') ) {
    UKMdeltakere::setAction('registrer_monstring');
} else {
    $monstring = new monstring_v2( get_option('pl_id') );

$ufullstendige = $monstring->getInnslag()->getAllUfullstendige();
$pabegynte 		= $monstring->getInnslag()->filterByStatus( array(0,1,2,3,4), $ufullstendige );


$stat = new stdClass();
$stat->fullstendige 	= $monstring->getInnslag()->getAntall();
$stat->ufullstendige 	= sizeof( $ufullstendige ) - sizeof( $pabegynte );
$stat->pabegynte 		= sizeof( $pabegynte );


$TWIGdata['tab_active'] = isset( $_GET['list'] ) ? $_GET['list'] : 'fullstendig';
$TWIGdata['monstring'] = $monstring;
$TWIGdata['stat'] = $stat;

$TWIGdata['site_type'] = get_option('site_type');