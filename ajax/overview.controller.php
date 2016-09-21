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
		$tmp->rolle 	= $person->getInstrument();
		$snittalder 	+= ( ($tmp->alder_tall == '25+') ? 0 : $tmp->alder_tall );
		$JSON->innslag->personer[] = $tmp;
	}
	$JSON->innslag->snittalder	= round( $snittalder / ($innslag->getPersoner()->getAntall() > 0 ? $innslag->getPersoner()->getAntall() : 1 ), 1);
} else {
	$JSON->twigJS	 	= 'twigJSoverviewtittellos';
	$person 			= $innslag->getPersoner()->getSingle();
	$JSON->person 		= data_person( $person );
	$JSON->erfaring		= $innslag->getBeskrivelse();
}
// HENT UT PROGRAMMET FOR INNSLAGET PÅ DENNE MØNSTRINGEN
foreach( $innslag->getProgram( $monstring )->getAllInkludertSkjulte() as $hendelse ) {
	$tmp 				= data_program( $hendelse );
	$tmp->rekkefolge	= $innslag->getProgram( $monstring )->getRekkefolge( $hendelse );
	
	$JSON->innslag->hendelser[ $hendelse->getId() ] = $tmp;
}
