<?php

$JSON->twigJS = 'addToEvent';

$JSON->monstring->hendelser = [];
foreach( $monstring->getProgram()->getAllInkludertSkjulte() as $hendelse ) {
	$JSON->monstring->hendelser[] = data_program( $hendelse );
}
