<?php
$JSON->twigJS = 'personadd';

$personer = [];
// Personer fra kommunen
if( $monstring->getType() != 'land' ) {
	$sql = new SQL("SELECT * FROM `smartukm_participant`
					WHERE `p_kommune` IN('#kommuner')",
					array('kommuner'=> implode(',', $monstring->getKommuner()->getIdArray()) )
				);
	$res = $sql->run();
	
	if( $res ) {
		while( $row = mysql_fetch_assoc( $res ) ) {
			$personer[ $row['p_id'] ] = data_person( new person_v2( $row ) );
		}
	}
}

// Personer fra fullstendinge innslag
foreach( $monstring->getInnslag()->getAll() as $innslag ) {
	foreach( $innslag->getPersoner()->getAll() as $person ) {
		if( !isset( $personer[ $person->getId() ] ) ) {
			$personer[ $person->getId() ] = data_person( $person );
		}
	}
}

// Personer fra ufullstendinge innslag
foreach( $monstring->getInnslag()->getAllUfullstendige() as $innslag ) {
	foreach( $innslag->getPersoner()->getAll() as $person ) {
		if( !isset( $personer[ $person->getId() ] ) ) {
			$personer[ $person->getId() ] = data_person( $person );
		}
	}
}


$JSON->personer = $personer;