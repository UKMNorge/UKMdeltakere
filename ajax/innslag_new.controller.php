<?php
### innslag_new.controller.php
require_once('UKM/monstring.class.php');
require_once('UKM/sql.class.php');

$type = $_POST['view'];
$monstring = new monstring_v2(get_option('pl_id'));

$JSON->innslag_type = $type;

// TODO: Fix this:
// $JSON->personer = $monstring->getPersoner();
// $JSON->personer = $monstring->getPersoner()->getAll();
// WORKAROUND:
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



switch( $type ) {
	case 'scene':
	case 'musikk':
	case 'dans':
	case 'teater':
	case 'litteratur':
	case 'film':
	case 'video':
	case 'utstilling':
		$JSON->twigJS = 'twigJSinnslagtittel';
		break;
	case 'konferansier':
	// Mulig vi også må ha sceneteknikk her
		$JSON->twigJS = 'twigJSinnslagkonferansier';
		break;
	case 'nettredaksjon':
	case 'arrangor':
		$JSON->twigJS = 'twigJSinnslagtittellos';
		$iType = innslag_typer::getByName($type);
		$funksjoner = $iType->getFunksjoner();

		$JSON->type_nicename = $iType->getNavn();
		$JSON->funksjoner = array_keys($funksjoner);
		$JSON->funksjonsnavn = $funksjoner;
		break;
	default:
		throw new Exception("Fant ikke rett skjema for ".$type);
}