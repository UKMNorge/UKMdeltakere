<?php

use UKMNorge\Arrangement\Arrangement;
use UKMNorge\Samtykke\Person;

$monstring = new Arrangement( get_option('pl_id') );
$TWIGdata['monstring'] = $monstring;

$personer = [];
foreach( $monstring->getInnslag()->getAll() as $innslag ) {
	foreach( $innslag->getPersoner()->getAll() as $person ) {
		$samtykke = new Person( $person, $monstring->getSesong() );
    
       # $samtykke->setAttr('melding', $samtykke->getKommunikasjon()->sendMelding('samtykke'));
        
		$personer[] = $samtykke;
	}
}

$TWIGdata['personer'] = $personer;