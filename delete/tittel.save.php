<?php

use UKMNorge\Innslag\Titler\Write;

$innslag = $monstring->getInnslag()->get( $_POST['innslag'], true );
$tittel = $innslag->getTitler()->get( $_POST['object_id'] );

$innslag->getTitler()->fjern( $tittel );
Write::fjern( $tittel );