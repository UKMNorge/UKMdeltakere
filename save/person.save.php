<?php

// OBS - DENNE INKLUDERES OGSÅ AV PERSON_ADD-FUNKSJONALITET.

use UKMNorge\Innslag\Personer\Write;

$innslag = $monstring->getInnslag()->get( $_POST['innslag'], true );
$person = $innslag->getPersoner()->get( $_POST['object_id'] );

// We hope...
$fodselsdato = mktime(0,0,0,1,1, (int)date("Y") - (int)$DATA['alder']);

### Sett person-data på person-objektet.
$person->setFornavn( $DATA['fornavn'] );
$person->setEtternavn( $DATA['etternavn'] );
$person->setMobil( $DATA['mobil'] );
$person->setFodselsdato( $fodselsdato );
$person->setKommune( $DATA['kommune'] );
$person->setEpost( $DATA['epost'] );
// Kun innslag med titler vil komme til denne siden
$person->setRolle( $DATA['rolle'] );

Write::save( $person );
Write::saveRolle( $person );