<?php
### personAdd.save.php
# Skal opprette nytt personobjekt og relatere det til et innslag, for så å kalle person.save.php.

require_once('UKM/write_person.class.php');
require_once('UKM/write_innslag.class.php');
require_once('UKM/write_monstring.class.php');
require_once('UKM/monstringer.collection.php');

$innslag = $monstring->getInnslag()->get( $_POST['innslag'], true );

$person = write_person::create($DATA['fornavn'], $DATA['etternavn'], $DATA['mobil'], write_person::fodselsdatoFraAlder($DATA['alder']), $DATA['kommune']);
$innslag->getPersoner()->leggTil( $person );

write_innslag::savePersoner( $innslag );

$_POST['object_id'] = $person->getId();
// Gjennomfører lagring av korrekt data.
require( plugin_dir_path( __FILE__ ). '../save/person.save.php' );
