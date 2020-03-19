<?php

use UKMNorge\Arrangement\Arrangement;
use UKMNorge\Arrangement\Program\Write;

require_once('UKM/Autoloader.php');

$arrangement = new Arrangement( intval(get_option('pl_id')));

$hendelse = $arrangement->getProgram()->get( $_POST['object_id'] );
$innslag = $hendelse->getInnslag()->get( $_POST['innslag'] );

$hendelse->getInnslag()->fjern( $innslag );
Write::fjern($hendelse, $innslag );