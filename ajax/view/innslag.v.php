<?php
require_once('UKM/inc/toolkit.inc.php');

function UKMdeltakere_ajax_view($c){
$viser_forestilling = strpos($_SERVER['HTTP_REFERER'],'?page=UKMprogram_admin')!==false;
$url = parse_url($_SERVER['HTTP_REFERER']);
parse_str($url['query'], $urlinfo);

$this_c_id = $urlinfo['c_id'];

$warnings = array();
if(strlen($c['advarsler'])>0) {
	if( strpos( $c['advarsler'], ', ') === false ) {
		$warnings[] = $c['advarsler'];
	}
	else {
		$warnings = explode(', ', $c['advarsler']);
	}
}

?>
<div id="redigerInnslag<?=$c['innslag']->g('b_id')?>"></div>
<div class="error_wrapper">
	<?php
	if(count($warnings)>0){ ?>
		<h3>Advarsler</h3>
		<?php foreach($warnings as $warning) { ?> 
		<div class="advarsler error"><?=ucfirst($warning)?></div>
		<?php } ?>
	
	<?php } ?>
</div>
<div class="personer">
	<h3>Personer</h3>
	<div class="topleft">
		<ul id="<?=$c['innslag']->g('b_id')?>">
			<li class="bytt_kontaktperson"><?=UKMN_icoButton('user-blue',16,'bytt kontaktperson')?></li>
			<li class="legg_til_person"><?=UKMN_icoButton('fancyplus',16,'legg til person')?></li>
		</ul>
	</div>
	<div style="clear:both;"></div>
	<div class="person_table">
		<ul class="titles">
			<li class="person_icon"></li>
			<li class="person_contact"></li>
			<li class="person_name">Navn (alder)</li>
			<li class="person_cellnumber">Mobil</li>
			<li class="person_instrument">Instrument/rolle</li>
			<li class="person_email">E-post</li>
			<li class="person_remove">Fjern</li>
		</ul>
	
		<?php 
			$kontaktperson = $c['innslag']->kontaktperson();
			$kontakt_id = $kontaktperson->g('p_id');
			$instrument = $kontaktperson->g('instrument');
			if (empty($instrument)) {
				$kontakt_rolle = 'Kontaktp. (deltar ikke)';
				$kontakt_alder = '';
			}
			foreach( $c['innslag']->personer() as $p ) {
				if( $p['p_id'] == $kontakt_id ) {
					$pers = new person($p['p_id'], $c['innslag']->g('b_id'));
					$kontakt_rolle = $pers->g('instrument');
					$kontakt_epost = $pers->g('p_email');
					$kontakt_alder = '('.($pers->alder()==26 ? 'over 25' : $pers->alder()).')';
					
					if (empty($kontakt_rolle)) {
						$kontakt_rolle = 'Kontaktp. (deltar ikke)';
						$kontakt_alder = '';
					}
				}
			}
		if($kontakt_id>0){
		?>
		<ul id="<?=$kontakt_id?>" class="person_selector_class">
			<li class="boldText person_icon" title="Rediger person" ><div class="ikon_person_rediger"><?=UKMN_icoAlt('pencil','Redigér',12); ?></div></li>
			<li class="person_contact" title="Sett som kontaktperson"><div class="ikon_person_sett_kontakt"><?=UKMN_icoAlt('user-blue', 'Sett som kontaktperson')?></li>
			<li class="boldText person_name" title="<?=$kontaktperson->g('name')?>" ><em><?=shortString($kontaktperson->g('name'), 19).' '.$kontakt_alder; ?></em></li>
			<li class="boldText person_cellnumber" title="<?=$kontaktperson->g('p_phone')?>" ><em><?=$kontaktperson->getNicePhoneWithColor(); ?></em></li>
			<li class="boldText person_instrument" title="<?=$kontakt_rolle?>" ><em><?=shortString($kontakt_rolle, 29) ?></em></li>
			<li class="boldText person_email" title="<?=$kontaktperson->g('p_email')?>" ><em><?=shortString($kontaktperson->g('p_email'), 22); ?></em></li>
			<li class="clear"></li>
		</ul>
		<div id="persondetaljer_<?=$kontakt_id?>"></div>
	
		<?php
		}
		 $personer = $c['innslag']->personObjekter(); ?>
	
		<?php foreach( $personer as $person ) { ?>	
		<?php if( $person->g('p_id') != $kontakt_id ) { ?>
		<ul id="<?=$person->g('p_id')?>" class="person_selector_class">
			<li class="person_icon" title="Rediger person"><div class="ikon_person_rediger"><?=UKMN_icoAlt('pencil', 'Redigér', 12)?></div></li>
			<li class="person_contact clickable" data-pid="<?=$person->g('p_id');?>" title="Sett som kontaktperson"><div class="ikon_person_sett_kontakt"><?=UKMN_icoAlt('user-gray', 'Sett som kontaktperson')?></li>
			<li class="person_name" title="<?=$person->g('name')?>" ><em><?=shortString($person->g('name'), 22);?> (<?=($person->alder()==26 ? 'over 25' : $person->alder())?>)</em></li>
			<li class="person_cellnumber" title="<?=$person->g('p_phone')?>" ><em><?=$person->getNicePhoneWithColor();?></em></li>
			<li class="person_instrument" title="<?=$person->g('instrument')?>" ><em><?=shortString($person->g('instrument'), 29); ?></em></li>
			<li class="person_email" title="<?=$person->g('p_email')?>" ><em><?=shortString($person->g('p_email'), 24); ?></em></li>
			<?php if(!$viser_forestilling){?>
			<li class="person_remove" title="Fjern person" ><?=UKMN_ico('delete',12)?></li>
			<?php } ?>
			<li class="clear"></li>
		</ul>
		<div id="persondetaljer_<?=$person->g('p_id')?>"></div>
		<?php }
		} ?>
	</div>
	
</div>
<?php
if(get_option('site_type')=='fylke' && $c['innslag']->g('bt_form') != 'smartukm_titles_scene') {
	$ivs_personer = $c['innslag']->ikke_videresendte_personObjekter();
 ?>
	<div style="clear:both;"></div>
	<h3>Ikke videresendte personer</h3>
	<div class="personer">	
		<div class="person_table">
			<ul class="titles">
				<li class="person_icon"></li>
				<li class="person_contact"></li>
				<li class="person_name">Navn (alder)</li>
				<li class="person_cellnumber">Mobil</li>
				<li class="person_instrument">Instrument/rolle</li>
				<li class="person_email">E-post</li>
				<li class="person_remove">Videresend</li>
			</ul>
			
		<?php 
		if(is_array($ivs_personer))
		foreach( $ivs_personer as $person ) { ?>	
		<?php if( $person->g('p_id') != $kontakt_id ) { ?>
		<ul id="<?=$person->g('p_id')?>" class="person_selector_class">
			<li class="person_icon" title="Rediger person"> </li>
			<li class="person_contact"> </li>
			<li class="person_name" title="<?=$person->g('name')?>" ><em><?=shortString($person->g('name'), 22);?> (<?=($person->alder()==26 ? 'over 25' : $person->alder())?>)</em></li>
			<li class="person_cellnumber" title="<?=$person->g('p_phone')?>" ><em><?=$person->getNicePhoneWithColor();?></em></li>
			<li class="person_instrument" title="<?=$person->g('instrument')?>" ><em><?=shortString($person->g('instrument'), 29); ?></em></li>
			<li class="person_email" title="<?=$person->g('p_email')?>" ><em><?=shortString($person->g('p_email'), 24); ?></em></li>
			<?php if(!$viser_forestilling){?>
			<li class="person_videresend clickable" title="Videresend person" ><?=UKMN_ico('arrow-blue-right',12)?></li>
			<?php } ?>
			<li class="clear"></li>
		</ul>
		<div id="persondetaljer_<?=$person->g('p_id')?>"></div>
		<?php }
		} ?>

		</div>
	</div>
	<div style="clear:both;"></div>

<?php } ?>



<!-- LISTER UT ALLE TITLER -->
<div class="innslag_titler">
	<h3>Titler</h3>
	<div class="topleft">
		<ul>
			<li class="legg_til_tittel"><?=UKMN_icoButton('fancyplus',16,'legg til tittel')?></li>
		</ul>	
	</div>
	<?php
		$felter = UKMdeltakere_tittelgui($c['innslag']->g('bt_id'),$c['innslag']->g('b_kategori'));
		?>
		<div class="innslag_titler_table">
			<ul class="titles">
				<li class="title_icon">
				<?php foreach($felter as $key => $title) { ?>
					<li class="title_<?=$key?>"><?=$title?></li>
				<?php } ?>
				<li class="title_remove_icon">Fjern</li>
			</ul> 
			<?php
			if(!is_array($c['titler'])||sizeof($c['titler'])==0)
				echo '<div class="ingentitler">Innslaget har ingen titler</div>';
			else {	
			 foreach($c['titler'] as $t_id => $tittel) {?>
			<ul class="innslag_titler" id="<?=$tittel->g('t_id')?>">
				<li class="title_icon"><?=UKMN_ico('pencil',12)?></li><?php
				foreach($felter as $key => $title) { ?>
						<li title="<?=$tittel->g(($key == 'varighet'?'tid':$key))?>"class="title_<?=$key?>"><?=shortString($tittel->g(($key == 'varighet'?'tid':$key)), getWidthOfField($key))?></li>
			<?php } 
			?>
				<?php if(!$viser_forestilling){?>
				<li class="title_remove_icon"><?=UKMN_ico('delete',12)?></li>
				<?php } ?>
				<li class="clear"></li>
			</ul>
		<div id="redigertittel_<?=$tittel->g('t_id')?>" class="edit_title_wrapper"></div>
			<?php } ?>
		</div>
		<?php } ?>
	</div>
</div>

<?php
if(get_option('site_type')=='fylke' && $c['innslag']->g('bt_form') != 'smartukm_titles_scene') {
	$ivs_titler = $c['innslag']->ikke_videresendte_titler();
 ?>
<div class="innslag_titler">
	<div style="clear:both;"></div>
	<h3>Ikke videresendte titler</h3>
	<?php
	$felter = UKMdeltakere_tittelgui($c['innslag']->g('bt_id'),$c['innslag']->g('b_kategori'));
	?>
	<div class="innslag_titler_table">
		<ul class="titles">
			<li class="title_icon">
			<?php foreach($felter as $key => $title) { ?>
				<li class="title_<?=$key?>"><?=$title?></li>
			<?php } ?>
			<li class="title_remove_icon">Videresend</li>
		</ul> 
		<?php
		if(is_array($ivs_titler))
		 foreach($ivs_titler as $t_id => $tittel) {?>
		<ul class="innslag_titler" id="<?=$tittel->g('t_id')?>">
			<li class="title_icon"> </li
			<?php foreach($felter as $key => $title) { ?>
			<li title="<?=$tittel->g(($key == 'varighet'?'tid':$key))?>"class="title_<?=$key?>"><?=shortString($tittel->g(($key == 'varighet'?'tid':$key)), getWidthOfField($key))?> &nbsp; </li>
			<?php } 
			if(!$viser_forestilling){?>
			<li class="title_videresend_icon clickable"><?=UKMN_ico('arrow-blue-right',12)?></li>
			<?php } ?>
			<li class="clear"></li>
		</ul>
		<?php } ?>
	</div>
</div>
<?php } ?>

<div class="forestillinger">
	<h3>Forestillinger</h3>
	<div class="topleft">
		<ul>
			<li class="legg_til_i_forestilling"><?=UKMN_icoButton('fancyplus',16,'legg til i forestilling')?></li>
		</ul>	
	</div>
	<div class="forestilling_wrapper">
		<ul class="titles">
			<li class="forestillinger_icon"></li>
			<li class="forestillinger_forestilling">Forestilling</li>
			<li class="forestillinger_start">Start forestilling</li>
			<?php /*<li class="forestillinger_number">Nummer</li>*/ ?>
			<li class="forestillinger_number">&nbsp;</li>
			<li class="forestillinger_antall">Rekkefølge</li>
			<li class="forestillinger_varighet">Varighet</li>
			<li class="forestillinger_remove">Fjern fra forestilling</li>
		</ul>
		<?php
		foreach($c['forestillinger'] as $forestilling) {?>
		<ul class="forestillinger" id="forestilling_<?=$forestilling['id']?>">
			<li class="forestillinger_icon"></li>
			<li class="forestillinger_forestilling"><?=$forestilling['navn'];?></li>
			<li class="forestillinger_start"><?=$forestilling['start'];?></li>
			<?php /*<li class="forestillinger_number"><?=$forestilling['nummer'];?></li>*/?>
			<li class="forestillinger_number">&nbsp;</li>
			<li class="forestillinger_antall"><?=$forestilling['nummer'];?> av <?= $forestilling['antall'] ?></li>
			<li class="forestillinger_varighet"><?=$forestilling['varighet'];?></li>
			<?php if($forestilling && $forestilling['id']!=$this_c_id) { ?>
				<li class="forestillinger_remove_icon clickable"><?=UKMN_icoAlt('delete','Fjern fra forestilling',12)?></li>
			<?php } else { ?> 
				<li class="forestillinger_remove_icon clickable">&nbsp;</li>
			<?php } ?>
		</ul>
	<?php } ?>
	</div>
</div>

<?php if($c['innslag']->g('b_status') == 5 OR $c['innslag']->g('b_status') == 6 OR $c['innslag']->g('b_status') == 7) { ?>
	<input class="approveband" type="button" value="Meld på dette innslaget med de opplysninger som finnes" />
	<div class="clear"></div>
<?php } ?>

<div style="display:none;" id="kontaktperson_<?=$c['innslag']->g('b_id')?>"></div>
<div style="display:none;" id="leggtilperson_<?=$c['innslag']->g('b_id')?>"></div>
<div style="display:none;" id="leggtiltittel_<?=$c['innslag']->g('b_id')?>"></div>
<div style="display:none;" id="leggtiliforestilling_<?=$c['innslag']->g('b_id')?>"></div>
<?php } 
function getWidthOfField($feltnavn){
	switch($feltnavn){
		case 'tittel': return 27;
		case 'koreografi': return 25;
		case 'melodi_av': return 20;
		case 'tekst_av': return 20;
		case 'beskrivelse': return 30;
		case 'teknikk': return 18;
		case 'type': return 18;
		default: return 20;
	}
}
?>
