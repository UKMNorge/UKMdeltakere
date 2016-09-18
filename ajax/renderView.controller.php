<?php
$JSON->view = $_POST['view'];

$monstring = new monstring_v2( get_option('pl_id') );
$innslag = new innslag_v2( $JSON->innslag_id, $monstring->getId() );

$JSON->monstring = new stdClass();
$JSON->monstring->type				= $monstring->getType();
$JSON->monstring->navn				= $monstring->getNavn();
$JSON->monstring->erFellesmonstring	= $monstring->erFellesmonstring();
$JSON->monstring->kommuner			= $monstring->getKommuner()->getKeyValArray();

$JSON->innslag = new stdClass();
$JSON->innslag->id 		= $innslag->getId();
$JSON->innslag->navn	= $innslag->getNavn();

$JSON->innslag->type	= new stdClass();
$JSON->innslag->type->id 	= $innslag->getType()->getId();
$JSON->innslag->type->key 	= $innslag->getType()->getKey();
$JSON->innslag->type->navn 	= $innslag->getType()->getNavn();


if( $innslag->getType()->harTitler() ) {
} else {
	$person = $innslag->getPersoner()->getSingle();

	$JSON->person = new stdClass();
	$JSON->person->id 					= $person->getId();
	$JSON->person->fornavn				= $person->getFornavn();
	$JSON->person->etternavn			= $person->getEtternavn();
	$JSON->person->mobil				= $person->getMobil();
	$JSON->person->epost				= $person->getEpost();
	$JSON->person->alder_tall			= $person->getAlder('');
	$JSON->person->alder				= $person->getAlder();
	$JSON->person->kommune_id			= $person->getKommune()->getId();
	$JSON->person->kommune_navn			= $person->getKommune()->getNavn();
	$JSON->person->valgte_funksjoner	= $person->getInstrumentObject();


	$JSON->erfaring			= $innslag->getBeskrivelse();

}

$JSON->twigJS = 'twigJSunsupported';
switch( $_POST['view'] ) {
	case 'overview':
		if( $innslag->getType()->harTitler() ) {
		} else {
			$JSON->twigJS = 'twigJSoverviewtittellos';
		}
		break;
	case 'edit':
		if( $innslag->getType()->harTitler() ) {
		} else {
			$JSON->twigJS = 'twigJSformtittellos';
			
			if( 'nettredaksjon' == $innslag->getType()->getKey() ) {
				$JSON->funksjoner 			= array('tekst','foto','videoreportasjer','flerkamera_regi','flerkamera_kamera','pr');
				$JSON->funksjonsnavn		= array('tekst'=> 'Journalist',
													'foto' => 'Fotograf',
													'videoreportasjer' => 'Videoreportasjer',
													'flerkamera_regi' => 'Flerkamera, regi',
													'flerkamera_kamera' => 'Flerkamera, kamera',
													'pr' => 'PR og pressekontakt'
													);
			} elseif ( 'arrangor' == $innslag->getType()->getKey() ) {
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
		break;
}

