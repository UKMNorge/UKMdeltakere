<?php

use UKMNorge\Innslag\Innslag;
use UKMNorge\OAuth2\HandleAPICall;
use UKMNorge\Innslag\Nominasjon\Nominasjon;
use UKMNorge\Innslag\Nominasjon\Write as WriteNominasjon;
use UKMNorge\Arrangement\Write;
use UKMNorge\Meta\Write as WriteMeta;
use UKMNorge\Arrangement\Arrangement;

$handleCall = new HandleAPICall(['innslag_id', 'nominasjon_id', 'godkjent_value'], [], ['GET', 'POST'], false);

$innslagId = $handleCall->getArgument('innslag_id');
$nominasjonId = $handleCall->getArgument('nominasjon_id');
$godkjentValue = $handleCall->getArgument('godkjent_value');

try{
    $innslag = Innslag::getById($innslagId);
    $nominasjon = Nominasjon::getById($nominasjonId, $innslag->getType());
    $nominasjon->setGodkjent($godkjentValue == 'true');

    WriteNominasjon::saveGodkjent($nominasjon);

}catch(Exception $e) {
    throw $e;
}

if($godkjentValue == 'false') {
    $JSON->redirect = '?page=UKMdeltakere';

} else if($godkjentValue) { // Hvis godkjenning er true (utfør videresending)
    $til = new Arrangement( intval(get_option( 'pl_id' ) )); 

    // Hvis arrangementet som tar i mot innslaget er et land, utføres ikke videresending automatisk
    if($til->getType() == 'land') {
        $JSON->redirect = '?page=UKMdeltakere';
        return;
    }

    // Setter opp logger
    UKMdeltakere::setupLogger();
    
    try{ 
    
        $innslagId = $handleCall->getArgument('innslag_id');
        $nominasjonId = $handleCall->getArgument('nominasjon_id');
    
        $innslag = Innslag::getById($innslagId);
        $nominasjon = Nominasjon::getById($nominasjonId, $innslag->getType());
        
        $fra = $nominasjon->getFraArrangement();
    
    
        $innslagType = $innslag->getType();
    
        if(!$innslagType->har_nominasjon) {
            throw new Exception("Kun innslag som kan nomineres kan videresendes");
        }
    
        if(!$innslag->getNominasjoner()->getTil($til->getId())) {
            throw new Exception("Innslaget må være nominert til dette arrangementet for å kunne videresendes");
        }
    
        // Sjekk hvis innslaget har nominasjon for innslag type
        if($til->harNominasjonFor($innslagType)) {
            try {
                $nominasjon = $innslag->getNominasjoner()->getTil($til->getId());
                if(!$nominasjon || !$nominasjon->erGodkjent()) {
                    throw new Exception("Du kan ikke videresende før godkjennelse");
                }
            }
            catch(Exception $e) {
                throw $e;
            }
        }
    
        // Videresend innslaget
        try {
            Write::leggTilInnslag($til, $innslag, $fra);
        } catch( Exception $e ) {
            /**
             * Selv om innslaget er videresendt fra før, betyr ikke det
             * nødvendigvis at tittelen er videresendt.
             * Fortsett derfor, men dø på alle andre exceptions.
            **/
            if( $e->getCode() == 10404 ) {
                // 10404: Innslag collection: innslaget er allerede lagt til
                // fortsett til videresending av evt tittel
            } else {
                throw $e;
            }
        }
    
        // OBS:!!!! KOMMENTERER linjene under fordi innslagtypene som kan videresendes ikke har tittler
    
        // // Videresend evntuell tittel
        // if( $_POST['type'] == 'tittel' ) {
        // 	/**
        // 	 * Hent innslag på nytt.
        // 	 * Henter fra mønstringen det skal videresendes til, slik at
        // 	 * tittelen meldes på riktig mønstring (og ikke avsender-mønstringen)
        // 	**/
        // 	$innslag = $til->getInnslag()->get( $_POST['innslag'] );	
        // 	$tittel = $innslag->getTitler()->get( $_POST['id'] );
        // 	$innslag->getTitler()->leggTil( $tittel );
    
        // 	WriteTittel::leggtil( $tittel );
        // }
    
        beregnAntallVideresendtePersoner($fra, $til);
    
        $JSON->redirect = '?page=UKMdeltakere';
    
    } catch(Exception $e) {
        throw $e;
    }
}


/**
 * Beregn og lagre antall videresendte personer som metadata
 *
 * @throws Exception
 * @return Bool
 */
function beregnAntallVideresendtePersoner($fra, $til) {

	$unike_personer = [];
	foreach( $fra->getVideresendte( $til->getId() )->getAll() as $innslag ) {
		foreach ($innslag->getPersoner()->getAll() as $person) {
			$unike_personer[] = $person->getId();
		}
	}
	$unike_personer = array_unique($unike_personer);
	WriteMeta::set(
		$fra->getMeta('antall_videresendte_personer_til_'. $til->getId())
			->set(
				sizeof($unike_personer)
			)
	);

	return true;
}

