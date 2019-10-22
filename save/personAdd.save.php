<?php
### personAdd.save.php
# Skal opprette nytt personobjekt og relatere det til et innslag, for så å kalle person.save.php.

use UKMNorge\Geografi\Kommune;
use UKMNorge\Innslag\Personer\Write;
use UKMNorge\Innslag\Write as WriteInnslag;

require_once('UKM/write_person.class.php');
require_once('UKM/write_innslag.class.php');
require_once('UKM/write_monstring.class.php');
require_once('UKM/monstringer.collection.php');

$innslag = $monstring->getInnslag()->get( $_POST['innslag'], true );

$person = Write::create(
    $DATA['fornavn'],
    $DATA['etternavn'],
    (Int) $DATA['mobil'],
    new Kommune($DATA['kommune'])
);
$person->setFodselsdato(
    Write::fodselsdatoFraAlder($DATA['alder'])
);
$innslag->getPersoner()->leggTil( $person );

Write::save( $person );
WriteInnslag::savePersoner( $innslag );

$_POST['object_id'] = $person->getId();
// Gjennomfører lagring av korrekt data.
require( plugin_dir_path( __FILE__ ). '../save/person.save.php' );
