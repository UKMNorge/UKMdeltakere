<?php

$monstring = new monstring_v2( get_option('pl_id') );
$TWIGdata['monstring'] = $monstring;

$personer = [];
foreach( $monstring->getInnslag()->getAll() as $innslag ) {
	foreach( $innslag->getPersoner()->getAll() as $person ) {
		$samtykke = new samtykke_person( $person, $monstring->getSesong() );
    
       # $samtykke->setAttr('melding', $samtykke->getKommunikasjon()->sendMelding('samtykke'));
        
		$personer[] = $samtykke;
	}
}

$TWIGdata['personer'] = $personer;