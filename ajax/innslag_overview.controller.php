<?php
// HENT UT INFORMASJON OM TITLER HVIS INNSLAGET HAR DET
if( $innslag->getType()->harTitler() ) {
	$JSON->twigJS = 'twigJSoverview';
	
	// INFO OM INNSLAGET
	$JSON->innslag->kontaktperson 	= $innslag->getKontaktperson();
	$JSON->innslag->beskrivelse 	= $innslag->getBeskrivelse();
	
	// TITLER OG VARIGHET
	$JSON->innslag->titler = [];
	$titler = $innslag->getTitler( $monstring )->getAll();
	if( is_array( $titler ) ) {
		foreach( $titler as $tittel ) {
			$JSON->innslag->titler[] = data_tittel( $tittel );
		}
	}
	$JSON->innslag->varighet 	= $innslag->getTitler( $monstring )->getVarighet();
	
	// PERSONER I INNSLAGET
	$JSON->innslag->personer 	= [];
	$snittalder 		= 0;
	foreach( $innslag->getPersoner()->getAll( $monstring ) as $person ) {
		$tmp 			= data_person( $person );
		$tmp->rolle 	= $person->getRolle();
		$snittalder 	+= ( ($tmp->alder_tall == '25+') ? 0 : $tmp->alder_tall );
		$JSON->innslag->personer[] = $tmp;
	}
	$JSON->innslag->snittalder	= round( $snittalder / ($innslag->getPersoner()->getAntall() > 0 ? $innslag->getPersoner()->getAntall() : 1 ), 1);
} else {
	$JSON->twigJS	 	= 'twigJSoverviewtittellos';
	$person 			= $innslag->getPersoner()->getSingle();
	$JSON->person 		= 'ta';#data_person( $person );
	$JSON->erfaring		= $innslag->getBeskrivelse();
}
// HENT UT PROGRAMMET FOR INNSLAGET PÅ DENNE MØNSTRINGEN
foreach( $innslag->getProgram( $monstring )->getAllInkludertSkjulte() as $hendelse ) {
	$tmp 				= data_program( $hendelse );
	$tmp->rekkefolge	= $innslag->getProgram( $monstring )->getRekkefolge( $hendelse );
	
	$JSON->innslag->hendelser[ $hendelse->getId() ] = $tmp;
}

// FINN LIGNENDE INNSLAG
if( 8 > $innslag->getStatus() ) {
	$JSON->lignende = true;
	$JSON->alle_lignende = [];
	
	$alle_innslag = array_merge( $monstring->getInnslag()->getAll(), $monstring->getInnslag()->getAllUfullstendige() );
	
	foreach( $alle_innslag as $sammenlign_innslag ) {
		// Hopp over seg selv
		if( $innslag->getId() == $sammenlign_innslag->getId() ) {
			continue;
		}
		
		// Navnesjekk
		similar_text($innslag->getNavn(),$sammenlign_innslag->getNavn(), $likhet);
		if($likhet > 60) {
			$data = data_innslag( $sammenlign_innslag, $monstring );
			$data->grunnlag = 'Lignende navn ('. $likhet .'%)';
			$JSON->alle_lignende[ $sammenlign_innslag->getId() ] = $data;
		}
		
		// Samme kontaktperson
		if( $innslag->getKontaktpersonId() == $sammenlign_innslag->getKontaktpersonId() ) {
			$data = data_innslag( $sammenlign_innslag, $monstring );
			$data->grunnlag = $innslag->getKontaktperson()->getFornavn() .' '. $innslag->getKontaktperson()->getEtternavn() .' er kontaktperson';
			$JSON->alle_lignende[ $sammenlign_innslag->getId() ] = $data;
		}
		
		// Deltakere
		foreach( $innslag->getPersoner()->getAll() as $deltaker ) {
			if( $deltaker->getId() == $sammenlign_innslag->getKontaktperson()->getId() ) {
			$data = data_innslag( $sammenlign_innslag, $monstring );
				$data->grunnlag = $person->getFornavn() .' '. $person->getEtternavn() .' er kontaktperson';
				$JSON->alle_lignende[ $sammenlign_innslag->getId() ] = $data;
			}
		}
	}
}