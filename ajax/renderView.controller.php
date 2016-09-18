<?php

function data_person( $person ) {
	$data = new stdClass();
	$data->id 					= $person->getId();
	$data->fornavn				= $person->getFornavn();
	$data->etternavn			= $person->getEtternavn();
	$data->mobil				= $person->getMobil();
	$data->epost				= $person->getEpost();
	$data->alder_tall			= $person->getAlder('');
	$data->alder				= $person->getAlder();
	$data->kommune_id			= $person->getKommune()->getId();
	$data->kommune_navn			= $person->getKommune()->getNavn();
	$data->valgte_funksjoner	= $person->getInstrumentObject();
	return $data;
}

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

$JSON->innslag->hendelser	= [];
foreach( $innslag->getProgram( $monstring )->getAll() as $hendelse ) {
	$tmp = new stdClass();
	$tmp->id	 		= $hendelse->getId();
	$tmp->rekkefolge	= $innslag->getProgram( $monstring )->getRekkefolge( $hendelse );
	$tmp->navn			= $hendelse->getNavn();
	$tmp->sted			= $hendelse->getSted();
	$tmp->start			= $hendelse->getStart();
	
	$JSON->innslag->hendelser[ $hendelse->getId() ] = $tmp;
}

if( $innslag->getType()->harTitler() ) {
	// INFO OM INNSLAGET
	$JSON->innslag->kontaktperson 	= $innslag->getKontaktperson();
	$JSON->innslag->beskrivelse 	= $innslag->getBeskrivelse();
	
	// TITLER OG VARIGHET
	$JSON->innslag->titler = [];
	$titler = $innslag->getTitler( $monstring )->getAll();
	if( is_array( $titler ) ) {
		foreach( $titler as $tittel ) {
			$tmp = new stdClass();
			$tmp->id			= $tittel->getId();
			$tmp->tittel		= $tittel->getTittel();
			$tmp->varighet		= $tittel->getVarighet();
			$tmp->parentes		= $tittel->getParentes();
	
			$JSON->innslag->titler[] = $tmp;
		}
	}
	$JSON->innslag->varighet 		= $innslag->getTitler( $monstring )->getVarighet();
	
	// PERSONER I INNSLAGET
	$JSON->innslag->personer 		= [];
	$snittalder = 0;
	foreach( $innslag->getPersoner()->getAll( $monstring ) as $person ) {
		$tmp = data_person( $person );
		$tmp->rolle = $person->getInstrument();
		$snittalder += ( ($tmp->alder_tall == '25+' || $tmp->alder_tall == 0 ) ? 0 : $tmp->alder_tall );
		$JSON->innslag->personer[] = $tmp;
	}
	$JSON->innslag->snittalder 		= round( $snittalder / ($innslag->getPersoner()->getAntall() > 0 ? $innslag->getPersoner()->getAntall() : 1 ), 1);
	
} else {
	$person = $innslag->getPersoner()->getSingle();
	$JSON->person = data_person( $person );
	$JSON->erfaring			= $innslag->getBeskrivelse();

}

$JSON->twigJS = 'twigJSunsupported';
switch( $_POST['view'] ) {
	case 'overview':
		if( $innslag->getType()->harTitler() ) {
			$JSON->twigJS = 'twigJSoverview';
		} else {
			$JSON->twigJS = 'twigJSoverviewtittellos';
		}
		break;
	case 'edit':
		if( $innslag->getType()->harTitler() ) {
		} else {
			$JSON->twigJS = 'twigJSformtittellos';
			
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
		break;
	case 'addToEvent':
		$JSON->twigJS = 'twigJSaddToEvent';
		
		$JSON->monstring->hendelser = [];
		foreach( $monstring->getProgram()->getAllInkludertSkjulte() as $hendelse ) {
			$tmp = new stdClass();
			$tmp->id 	= $hendelse->getId();
			$tmp->navn 	= $hendelse->getNavn();
			$tmp->start	= $hendelse->getStart();
			
			$JSON->monstring->hendelser[] = $tmp;
		}
		break;
}
