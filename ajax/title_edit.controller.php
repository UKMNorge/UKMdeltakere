<?php

$JSON->twigJS = 'titleadd';

$JSON->tittel = data_tittel( $innslag->getTitler()->get( $_POST['object_id'] ) );
$JSON->type = data_type( $innslag->getType() );

if( $innslag->getType()->getKey() == 'litteratur') {
    $JSON->script = "$('#leseopp-valg').trigger('click');";
}