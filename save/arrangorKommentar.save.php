<?php

use UKMNorge\Innslag\Write;

require_once('UKM/Autoloader.php');

if(isset($_POST['arrangor_kommentar'])) {
    $arrangorKommentar = $_POST['arrangor_kommentar'];
    $innslag = $monstring->getInnslag()->get( $_POST['innslag'], true );
    
    $innslag->setArrangorKommentar($arrangorKommentar);
    Write::save( $innslag );
}