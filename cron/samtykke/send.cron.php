<?php
/**
 * CRON: samtykke/send
 * 
 * TODO: Del opp i chunks, i tilfelle det blir litt for mange
 * 
 * Itererer over alle som ikke har fått sms, og sender dette ut til de.
 * Identifiseres av status=ikke_sendt og antall_innslag>0 og year=sesong
 * 
 * SMS til foresatte må sendes via GUI-controller når foresatt oppgis.
 */

use UKMNorge\Database\SQL\Query;
use UKMNorge\Samtykke;
use UKMNorge\Samtykke\Person;

ini_set('display_errors',true);
require_once('cron.config.php');
require_once('UKM/Autoloader.php');

// Alle som skulle fått mange sms får null nå
require_once('prevent_mass_spam.inc.php');

$selected = new Query(
    "SELECT `id` 
    FROM `samtykke_deltaker`
    WHERE `status` = 'ikke_sendt'
    AND `antall_innslag` > 0
    AND `year` = '#sesong'
    ORDER BY `id` ASC",
    [
        'sesong' => SEASON
    ]
);

$res = $selected->run();

$count = 0;
echo '<h1>Personer som skal ha samtykke-SMS</h1>';
if( Query::numRows( $res ) == 0 ) {
    echo 'ingen som skal sendes';
}
else {
    while( $row = Query::fetch( $res ) ) {
        $count++;
        $samtykke = Person::getById( $row['id'] );
        $melding = $samtykke->getKommunikasjon()->sendMelding('samtykke');
        echo '<h4>'. 
            $samtykke->getKategori()->getId() .' - '. $samtykke->getMobil() .' '. $samtykke->getPerson()->getNavn() .': '.
            '</h4> '.
            $melding . '<br />'; 
        if( $count > 50 ) {
            echo '<h1>Reached 50. Stop</h1>';
            die();
        }
    }
    echo '<h1>Finito</h1>';
}