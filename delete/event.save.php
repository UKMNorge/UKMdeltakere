<?php

require_once('UKM/write_innslag.class.php');
require_once('UKM/forestilling.class.php');

$innslag = new write_innslag($_POST['innslag']);
$forestilling = new forestilling_v2($_POST['object_id']);

$forestilling->getInnslag()->fjern($innslag);