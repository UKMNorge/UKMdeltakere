<?php
require_once('UKM/monstring.class.php');

$monstring = new monstring_v2( get_option('pl_id') );

$stat = new stdClass();
$stat->fullstendige = 92;
$stat->ufullstendige = 10;
$stat->pabegynte = 5;


$TWIGdata['tab_active'] = isset( $_GET['list'] ) ? $_GET['list'] : 'fullstendig';
$TWIGdata['monstring'] = $monstring;
$TWIGdata['stat'] = $stat;