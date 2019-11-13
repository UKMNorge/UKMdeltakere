<?php

if( $innslag->getType()->harTitler() ) {
    $JSON->twigJS = 'form';
	// Kommune hvor innslaget er meldt på:
	$JSON->innslag->kommune_id = $innslag->getKommune()->getId();
} else {
    $JSON->twigJS = 'formtittellos';
	
	$person 			        = $innslag->getPersoner()->getSingle();
	$JSON->person 		        = data_person( $person );
	$JSON->erfaring		        = $innslag->getBeskrivelse();
	$JSON->innslag->kommune_id  = $innslag->getKommune()->getId();
    
	if( $innslag->getType()->harFunksjoner() ) {
        if( null == $JSON->person->valgte_funksjoner ) {
            $JSON->person->valgte_funksjoner = [];
		}
		$JSON->funksjonsnavn    = $innslag->getType()->getFunksjoner();
		$JSON->funksjoner       = array_keys($JSON->funksjonsnavn);
    }
}

$JSON->type                 = data_type( $innslag->getType() );