<?php

$JSON->twigJS = 'titleadd';
$JSON->type = data_type( $innslag->getType() );

if( $innslag->getType()->getKey() == 'litteratur') {
    $JSON->script = "$('#leseopp-valg').trigger('click');";
}