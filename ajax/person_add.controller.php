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

if( $monstring->getType() == 'land' ) {
	$JSON->fylker = [];
	foreach( fylker::getAllInkludertFalske() as $fylke ) {
		$data = new stdClass();
		$data->id = $fylke->getId();
		$data->navn = $fylke->getNavn();
		$data->kommuner = [];
		
		foreach( $fylke->getKommuner() as $kommune ) {
			$data_kommune = new stdClass();
			$data_kommune->id = $kommune->getId();
			$data_kommune->navn = $kommune->getNavn();
			$data->kommuner[] = $data_kommune;
		}
		
		$JSON->fylker[] = $data;
	}
}

$JSON->personer = $personer;