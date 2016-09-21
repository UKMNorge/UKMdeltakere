<?php
$JSON->twigJS = 'twigJSpersonadd';

$sql = new SQL("SELECT * FROM `smartukm_participant`
				WHERE `p_kommune` IN('#kommuner')",
				array('kommuner'=> implode(',', $monstring->getKommuner()->getIdArray()) )
			);
$res = $sql->run();
$personer = [];
if( $res ) {
	while( $row = mysql_fetch_assoc( $res ) ) {
		$personer[] = data_person( new person_v2( $row ) );
	}
}

$JSON->personer = $personer;