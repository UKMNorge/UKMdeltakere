<?php

use UKMNorge\Samtykke;

ini_set('display_errors',true);
require_once('cron.config.php');
require_once('UKM/sql.class.php');
require_once('UKM/samtykke/person.class.php');

define('THRESHOLD', 10);

$SQL = new SQL(
    "SELECT `mobil`, COUNT(`id`) AS `num`
    FROM `samtykke_deltaker`
    WHERE `year` = '#season'
    GROUP BY `mobil`
    ORDER BY `num` DESC
    ",
    [ 
        'season' => SEASON
    ]
);

$res = $SQL->run();

while( $row = SQL::fetch( $res ) ) {
    if( $row['num'] < THRESHOLD ) {
        break;
    }
    $update = new SQLins(
        'samtykke_deltaker', 
        ['mobil' => $row['mobil']]
    );
    $update->add('status', 'ikke_send');
    $update->run();
    echo 'IKKE SEND: '. $row['mobil'] .'  ('. $row['num'] .')<br />';
}