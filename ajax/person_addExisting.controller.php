<?php

use UKMNorge\Innslag\Personer\Person;

$JSON->twigJS = 'personaddexisting';

$person = new Person( $_POST['object_id'] );
$JSON->person = data_person( $person );