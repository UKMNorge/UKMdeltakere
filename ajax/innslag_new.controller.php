<?php
### innslag_new.controller.php
$type = $_POST['view'];

switch( $type ) {
	case 'musikk':
		$JSON->twigJS = 'twigJSinnslagmusikk';
	break;
	default:
		throw new Exception("Fant ikke rett skjema for ".$type);
}
