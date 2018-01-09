<?php
$JSON->twigJS = 'personedit';

$person = $innslag->getPersoner()->getById( $_POST['object_id'] );
$JSON->person = data_person( $person );
$JSON->person->rolle = $person->getRolle();