<?php

use UKMNorge\Arrangement\Arrangement;
use UKMNorge\Innslag\Personer\Person;
use UKMNorge\Innslag\Personer\Foresatt;

require_once('UKM/flashbag.class.php');
require_once('UKM/sms.class.php');
require_once('UKM/Autoloader.php');


$arrangement = new Arrangement(get_option('pl_id'));

$TWIGdata['arrangement'] = $arrangement;

global $current_user;

$flashbag = new Flashbag('foresatte');

/*
SEND SMS
*/

if(isset($_POST['submit']))
{
  if($_POST['personer'] == NULL)
  {
    $flashbag->error("Ingen personer valgt." );
  } else {
    foreach($_POST['personer'] as $p_id )
    {
      $SMS = new SMS('wordpress', $current_user->ID, get_option('pl_id'));
      $mobil = (new Person($p_id))->getMobil();

      $smstext = 'Hei! Vi trenger kontaktinformasjon til en av dine foreldre/foresatte. Fyll ut dette her: https://delta.'. UKM_HOSTNAME .'/foresatt/'. $p_id .'/'. $mobil .'/';
      $res = $SMS->text($smstext)->to($mobil)->from('UKMNorge')->ok();
      $oppdatertid = Foresatt::oppdaterTid($p_id);
      if($res){
        $flashbag->sucess("Sendte melding til $mobil.");
        $oppdatertid = Foresatt::oppdaterTid($p_id);
        } else {
        $flashbag->error("Fikk ikke sendt melding til $mobil. <br /> Systemet sa: ". $SMS->getError() );}
    }
  }
}

$TWIGdata['flashbag'] = $flashbag;