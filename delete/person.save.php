<?php
require_once('UKM/write_innslag.class.php');
require_once('UKM/write_person.class.php');

$innslag = new write_innslag( $_POST['innslag'] );
$person = new write_person( $_POST['object_id']);

$res = $innslag->getPersoner()->fjern($person);