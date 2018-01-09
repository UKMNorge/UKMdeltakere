<?php

$monstring = new monstring_v2( get_option('pl_id') );
$collection = $monstring->getInnslag();
$TWIGdata['list_innslag'] = [];
#{% for type_innslag in monstring.getInnslagTyper() %}
#	{% for innslag in monstring.getInnslag().filterByStatus([7,6,5],     monstring.getInnslag().filterByType( type_innslag, monstring.getInnslag().getAllUfullstendige() ) )  %}

foreach( $monstring->getInnslagTyper() as $type_innslag ) {
	$TWIGdata['list_innslag'][ $type_innslag->getKey() ] = $collection->filterByStatus([7,6,5], $collection->filterByType( $type_innslag, $collection->getAllUfullstendige() ) );
}