<?php
	
if( $innslag->getType()->harTitler() ) {
	$JSON->twigJS = 'twigJSform';

} else {
	$JSON->twigJS = 'twigJSformtittellos';
	
	$person 			= $innslag->getPersoner()->getSingle();
	$JSON->person 		= data_person( $person );
	$JSON->erfaring		= $innslag->getBeskrivelse();

	if( 'nettredaksjon' == $innslag->getType()->getKey() ) {
		if( null == $JSON->person->valgte_funksjoner ) {
			$JSON->person->valgte_funksjoner = [];
		}
		$JSON->funksjoner 			= array('tekst','foto','videoreportasjer','flerkamera_regi','flerkamera_kamera','pr');
		$JSON->funksjonsnavn		= array('tekst'=> 'Journalist',
											'foto' => 'Fotograf',
											'videoreportasjer' => 'Videoreportasjer',
											'flerkamera_regi' => 'Flerkamera, regi',
											'flerkamera_kamera' => 'Flerkamera, kamera',
											'pr' => 'PR og pressekontakt'
											);
	} elseif ( 'arrangor' == $innslag->getType()->getKey() ) {
		if( null == $JSON->person->valgte_funksjoner ) {
			$JSON->person->valgte_funksjoner = [];
		}
		$JSON->funksjoner			= array('lyd','lys','scenearbeider','artistvert','info','koordinator');
		$JSON->funksjonsnavn		= array('lyd' => 'Lyd',
											'lys' => 'Lys',
											'scenearbeider' => 'Scenearbeider',
											'artistvert' => 'Artistvert',
											'info' => 'Info / sekretariat',
											'koordinator' => 'Koordinator / produsent'
											);

	}
}
