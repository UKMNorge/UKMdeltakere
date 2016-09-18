<?php
function ukmdeltakere_ajax_view($c){?>
	<div class="duplikate">
		<?= $c['tips']?>

		<h3>Mulige duplikate innslag</h3>
		<div style="clear:both;"></div>
		<div class="duplikate_wrapper">
			<div class="duplikate_innslag">
				<span class="tittel">Mulige duplikate innslag basert på navnelikhet</span>
				Følgende innslag ligner på dette innslagets navn
			</div>
		
			<div class="duplikate_innslag_table">
				<ul class="titles">
					<li class="duplikate_name">Status</li>
					<li class="duplikate_band">Innslagsnavn</li>
				</ul>
				<?php foreach($c['like'] as $like) { ?>
				<ul class="data">
					<li class="duplikate_name"><?=$like['status']?></li>
					<li class="duplikate_band"><?=$like['navn']?></li>
				</ul>
				<?php }?>
			</div>

			<div class="duplikate_innslag_kontaktperson">
				<span class="tittel">Andre mulige innslag hvor personer i dette innslaget er kontaktperson</span>
				Hvis noen av personene (også kontaktperson) fra innslaget er kontaktperson for andre innslag på din mønstring vil de vises her
			</div>

			<div class="duplikate_innslag_table">
				<ul class="titles">
					<li class="duplikate_name">Status</li>
					<li class="duplikate_band">Innslagsnavn</li>
					<li class="duplikate_kontakt">Kontaktperson</li>
				</ul>
				<?php foreach($c['like_kp'] as $like) { ?>
				<ul class="data">
					<li class="duplikate_name"><?=$like['status']?></li>
					<li class="duplikate_band"><?=$like['navn']?></li>
					<li class="duplikate_kontakt"><?=$like['kontaktperson']?></li>
				</ul>
				<?php }?>
			</div>
		</div>
		<input class="approveband" type="button" value="Meld på dette innslaget med de opplysninger som finnes" />
		<div class="clear"></div>
	</div>
<?php	
}
?>
