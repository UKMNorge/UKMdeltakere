<?php
if( isset( $_GET['create'] ) ) {
	$TWIGdata['doAdd'] = $_GET['create'];
}
if( isset( $_GET['edit'] ) ) {
	$TWIGdata['doEdit'] = $_GET['edit'];
}

$monstring = new monstring_v2( get_option('pl_id') );
$TWIGdata['list_innslag'] = [];
#{% for type_innslag in monstring.getInnslagTyper() %}
#	{% for innslag in monstring.getInnslag().getAllByType( type_innslag ) %}
foreach( $monstring->getInnslagTyper() as $type_innslag ) {
	$TWIGdata['list_innslag'][ $type_innslag->getKey() ] = $monstring->getInnslag()->getAllByType( $type_innslag );
}