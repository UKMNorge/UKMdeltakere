<?php
$JSON->twigJS = 'personaddexisting';

$person = new person_v2( $_POST['object_id'] );
$JSON->person = data_person( $person );