<?php

require_once('UKM/write_innslag.class.php');

$forestilling = $monstring->getProgram()->get( $_POST['object_id'] );
$innslag = $forestilling->getInnslag()->get( $_POST['innslag'] );

$forestilling->getInnslag()->fjern( $innslag );
write_innslag::fjern( $innslag );