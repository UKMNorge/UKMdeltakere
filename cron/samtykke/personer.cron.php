<?php
/**
 * CRON: samtykke/personer
 * 
 * TODO: Del opp i chunks, for nå tar det TID for en full sesong
 * TODO: Iterer over alle slettede innslag og slett samtykke-request for de
 * 
 * Itererer over alle innslag for gitt sesong, og alle personer i disse.
 * Oppretter samtykke-request hvis denne ikke allerede eksisterer.
 * 
 * Default-state er 'ikke_sendt'. Trenger derfor ikke å kjøre setStatus()
 */

use UKMNorge\Samtykke;

ini_set('display_errors',true);
require_once('cron.config.php');
require_once('UKM/context.class.php');
require_once('UKM/innslag.collection.php');
require_once('UKM/samtykke/person.class.php');

$context = context::createSesong( SEASON );
$alle_innslag = new innslag_collection( $context );

#$count = 0;
echo '<h1>Lets go for '. SEASON .'</h1>';
foreach( $alle_innslag->getAll() as $innslag ) {
    echo '<h2>'. $innslag->getNavn() .'</h2>';
    foreach( $innslag->getPersoner()->getAll() as $person ) {
        #$count++;
        echo $person->getNavn() .'<br />';
        $samtykke = new Samtykke\Person( $person, $innslag );
    }
    if( $count > 500 ) {
        die('Reached 500');
    }
}
echo '<h1>Finito!</h1>';