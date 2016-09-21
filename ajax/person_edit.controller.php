<?php
$JSON->twigJS = 'twigJSpersonedit';

$person = $innslag->getPersoner()->getById( $_POST['object_id'] );
$JSON->person = data_person( $person );
$JSON->person->instrument = $person->getInstrument();