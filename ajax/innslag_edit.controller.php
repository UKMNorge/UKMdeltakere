<?php
	
if( $innslag->getType()->harTitler() ) {
	$JSON->twigJS = 'twigJSform';
	// Kommune hvor innslaget er meldt på:
	$JSON->innslag->kommune_id = $innslag->getKommune()->getId();
} else {
	$JSON->twigJS = 'twigJSformtittellos';
	
	$person 			= $innslag->getPersoner()->getSingle();
	$JSON->person 		= data_person( $person );
	$JSON->erfaring		= $innslag->getBeskrivelse();

	if( in_array($innslag->getType()->getKey(), array('nettredaksjon', 'arrangor') ) ) {
		if( null == $JSON->person->valgte_funksjoner ) {
			$JSON->person->valgte_funksjoner = [];
		}

		$JSON->funksjonsnavn = $innslag->getType()->getFunksjoner();
		$JSON->funksjoner = array_keys($JSON->funksjonsnavn);
	}
}
