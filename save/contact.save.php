<?php
/*
 * Bytter kontaktperson på et innslag. 
 *
 *
 */
require_once('UKM/write_innslag.class.php');
require_once('UKM/write_person.class.php');

$innslag = new write_innslag( $_POST['innslag'] );
$person = new write_person( (int)$DATA['person'] );

$innslag->setKontaktperson( $person );
$innslag->save();
