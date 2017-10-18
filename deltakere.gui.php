<?php
require_once('UKM/inc/toolkit.inc.php');
require_once('liste.inc.php');
function UKMdeltakere_list() {
	global $tittellose_innslag;
	$place = new monstring(get_option('pl_id'));
	$kategorier = $place->getBandTypes();

	if($place->g('type')=='land') {
		$geo_omrader = $place->fylkeArray();
		foreach( $geo_omrader as $id => $name ) {
			$geo_omrader[$id] = utf8_encode($name);
		}
	} elseif($place->g('type')=='fylke') {
		$geo_omrader = $place->g('kommuner_i_fylket');
	} else {
		$geo_omrader = $place->kommuneArray();
	}
	
	if(!isset($_GET['stat'])||$_GET['stat']==8) {
		$innslag_pa_monstringen = $place->innslag();
		$innslag_kun_pameldte = true;
	} elseif($_GET['stat']=='5-6') {
		$innslag7 = $place->innslag_etter_status(7);
		$innslag6 = $place->innslag_etter_status(6);
		$innslag_pa_monstringen = array_merge($innslag7,$innslag6);
		$innslag_kun_pameldte = false;
	} else {
		$innslag5 = $place->innslag_etter_status(5);
		$innslag4 = $place->innslag_etter_status(4);
		$innslag3 = $place->innslag_etter_status(3);
		$innslag2 = $place->innslag_etter_status(2);
		$innslag1 = $place->innslag_etter_status(1);
		$innslag_pa_monstringen = array_merge($innslag5,$innslag4,$innslag3,$innslag2,$innslag1);
		$innslag_kun_pameldte = false;
	}
	foreach($innslag_pa_monstringen as $data) {
		$inn = new innslag($data['b_id'],$innslag_kun_pameldte);
		if(get_option('site_type')!='kommune')
			$inn->videresendte(get_option('pl_id'));
		$inn->kalkuler_titler(get_option('pl_id'));
		if( get_option('site_type') == 'land' ) {
			$inn->loadGeo();
			$innslag[$inn->g('bt_id')][$inn->g('fylkeID')][$inn->g('b_id')] = $inn;
		} else {
			$innslag[$inn->g('bt_id')][$inn->g('b_kommune')][$inn->g('b_id')] = $inn;
		}

		$sokestreng = $inn->g('b_name');
		$personer = $inn->personer();
		foreach($personer as $person)
			$sokestreng .= ' '.$person['p_firstname'].' '.$person['p_lastname'];

		$sokestrenger[$inn->g('b_id')] = $sokestreng;
	}
	@ksort($innslag);
	?>
<div class="ukmdeltakere_kategorivelger">
	<strong>GÅ TIL KATEGORI:</strong><br />
	<?= UKMdeltakere_kategorivelger($kategorier,$innslag) ?>
	<div class="ukmdeltakere_filtrer">
		<input id="deltaker_filter" type="text" placeholder="søk i listen" data-slide="true" data-children="li.innslag" data-parent=".kategori_wrapper" />
		<!--<img class="empty_search_box" alt="Fjern filter" src="//ico.ukm.no/delete-256.png" onclick="clear_filter('.ukmdeltakere_filtrer input')" >-->
	</div>
	<script>jQuery('#deltaker_filter').bind('keyup', filter_list)</script>

</div>
<style type="text/css">
.ui-widget-overlay.ui-front {
    z-index: -1 !important;
}
</style>
<h1>Deltakere i <?= $place->g('pl_name')?></h1>
Her redigerer du dine påmeldinger. Via <a href="?page=UKMrapport_admin">rapportsenteret</a> kan du hente ut forskjellige<br />
 rapporter for å skaffe deg oversikt over din mønstring
<div class="ukmdeltakere_tabs">
	<a href="?page=<?=$_GET['page']?>&stat=8" <?=((!isset($_GET['stat'])||$_GET['stat']=='8')?' class="active"':'')?>><div><span class="tab_header">Påmeldte innslag</span><img src="//ico.ukm.no/smiley-smile-256.png" width="32" /><br><span class="tab_description">Kun små korrigeringer nødvendig</span></div></a>
	<a href="?page=<?=$_GET['page']?>&stat=5-6" <?=($_GET['stat']=='5-6'?' class="active"':'')?>><div><span class="tab_header">Ufullstendige påmeldinger</span><img src="//ico.ukm.no/emblem-important-256.png" width="32" /><br><span class="tab_description">Krever din oppmerksomhet</span></div></a>
	<a href="?page=<?=$_GET['page']?>&stat=1-4" <?=($_GET['stat']=='1-4'?' class="active"':'')?>><div><span class="tab_header">Så vidt påbegynte påmeldinger</span><img src="//ico.ukm.no/tools-256.png" width="32" /><br><span class="tab_description">Muligens duplikat, slettes?</span></div></a>
</div>
<div class="ukmdeltakere_tabs_desc">
	<span>
		<?php
		switch( $_GET['stat'] ) {
			case '8':
				#echo 'Stat8';
				break;
			case '5-6':
				#echo 'Stat5-6';
				break;
			case '1-4':
				#echo 'Stat1-4';
				break;
		}
		?>
	</span>
</div>


<div class="ukmdeltakere_wrapper">
<?php
$viste_kategorier = array();
	if(!is_array($innslag)||sizeof($innslag)==0)
		echo '<strong> Ingen innslag i denne listen</strong>';
	else
	foreach($innslag as $kategori_san => $trash) {
		if(is_array($innslag[$kategori_san])) {
			$viste_kategorier[] = $kategorier[$kategori_san]['bt_id'];
			?>
			<div class="kategori_wrapper">
				<img src="//ico.ukm.no/subscription/<?= UKMN_btico($kategori_san,'musikk')?>.png" class="kategori_image" /><a name="kategori_<?=$kategori_san?>"></a>
				<h1><?=$kategorier[$kategori_san]['bt_name']?>
					<a href="admin.php?page=UKMdeltakere&addnew=<?= $kategorier[$kategori_san]['bt_id']?>" class="add-new-h2">Legg til nytt <?= strtolower($kategorier[$kategori_san]['bt_name'])?>-innslag</a>
				</h1>	
				<?php
				foreach($innslag[$kategori_san] as $geo_omrade_san => $trash) {
					if(is_array($innslag[$kategori_san][$geo_omrade_san])){
						if($place->g('fellesmonstring') or $place->g('type')=='land'){
							echo '<h2>'.$geo_omrader[$geo_omrade_san].'</h2>';
						} elseif($place->g('type')=='fylke') {
							echo '<h2 style="clear:both;">'.$geo_omrader[$geo_omrade_san].'</h2>';
						}
						?>
						<ul class="ukmdeltakere_liste">
						<?php foreach($innslag[$kategori_san][$geo_omrade_san] as $bid => $inn){
						?>
						<a name="innslag_<?=$inn->g('b_id')?>"></a>
						<a name="rediger_<?=$inn->g('b_id')?>"></a>
						<li class="innslag" id="<?=$inn->g('b_id')?>" data-filter="<?=$sokestrenger[$inn->g('b_id')]?>">
						<?php
							UKMd_innslagsboks($inn,$place,$tittellose_innslag);
						?></li><?php
						}
						?></ul><?php
					}
				}
			?></div><?php
		}
	} 

	$alle_kategorier = $place->getBandTypes(); ?>

	<p class="empty_search" id="deltaker_filter_empty" style="display: none">
		Beklager, ingen treff!
	</p>
	</div> <!-- End ukmdeltakere_wrapper -->
	<?php if(!isset($_GET['stat']) || $_GET['stat'] == '8') { ?>
	<div class="pameldte_missing">
	<h2>Følgende typer innslag er tillatt på din mønstring, men har ingen påmeldte</h2>
	<?php
	foreach($alle_kategorier as $btinfo) {
		if(!in_array($btinfo['bt_id'], $viste_kategorier)) { ?>
			<div class="missing_type">
			<img src="//ico.ukm.no/subscription/<?= UKMN_btico($btinfo['bt_id'],'musikk')?>.png" class="kategori_image" /><a name="kategori_<?=$btinfo['bt_name']?>"></a>
				<h3><?=$btinfo['bt_name']?></h3>
				<a href="admin.php?page=UKMdeltakere&addnew=<?= $btinfo['bt_id']?>" class="add-new-h2">Legg til nytt <?= strtolower($btinfo['bt_name'])?>-innslag</a>
			</div>
		<?php
		}	
	}
	?>
	</div>
	<?php } ?>
	<div class="clear"></div>
	<?php
}


function UKMdeltakere_kategorivelger($kategorier,$innslag) {
	$index = 0;	
	if(is_array($innslag))
	foreach($innslag as $kat => $trash) { ?>
		<a class="kategorivalg" href="#kategori_<?=$kat?>"><?=ucfirst($kategorier[$kat]['bt_name'])?></a>
	<?php
		++$index;
		if( $index != count($innslag) ){
			echo ' | ';
		}	
	}
}
