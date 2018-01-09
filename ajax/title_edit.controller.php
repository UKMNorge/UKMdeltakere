<?php

$JSON->twigJS = 'titleadd'.ucfirst( $innslag->getType()->getKey() );

$JSON->tittel = data_tittel( $innslag->getTitler( $monstring )->get( $_POST['object_id'] ) );