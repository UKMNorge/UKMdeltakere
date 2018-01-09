<?php
function ukmdeltakere_ajax_view($controller){
	global $tittellose_innslag;
	if(in_array($controller['innslag']->g('bt_id'),$tittellose_innslag)) {
		echo 'skjema to come';
	} else {
		/*echo '<form id="test">';
		$form = new form('rediger_monstring_col1');
		$form->directReturn();

		echo '<div class="innslag_rediger_rad1">';
		echo $form->input('Navn','b_name',$controller['innslag']->g('b_name'));
		if($controller['innslag']->g('bt_id')==1)
			echo $form->select('Kategori','b_kategori',$controller['kategorier'],$controller['innslag']->g('b_kategori'));
		/*
			else
			echo $form->select('Kategori','b_category_dummy',
								array($controller['innslag']->g('bt_name')=>$controller['innslag']->g('bt_name')),
								$controller['innslag']->g('bt_name')
								);*/
		/*echo $form->input('Sjanger','b_sjanger',$controller['innslag']->g('b_sjanger'));
		echo '</div>';
	
		echo '<div class="innslag_rediger_rad2">';
		echo $form->textarea('Tekniske behov','td_demand',$controller['innslag']->g('td_demand'));
		echo $form->textarea('Beskrivelse','b_description', $controller['innslag']->g('td_konferansier'));
		echo $form->janei('Vis beskrivelsen på ukm.no?', 'td_description', $controller['visbeskrivelse']);
		echo '</div>';
		
		echo ukmdeltakere_ajax_buttons('innslag',$controller['innslag']->g('b_id'));
		echo '<br clear="all" />';
		echo '</form>';*/
		?>
		<form class="edit_band_form">
			<fieldset>
				<div class="group">
					<label class="name_label" for="b_name">Navn</label>
					<input class="name_input" type="text" name="b_name" value="<?=$controller['innslag']->g('b_name')?>" />
					<input type="hidden" name="log_current_value_b_name" value="<?=$controller['innslag']->g('b_name')?>" />
				</div>
				<?php
				$m = new monstring(get_option('pl_id'));
				if($m->g('fellesmonstring')) { 
				?>
				<div class="group">
					<label class="category_label" for="b_kategori">Kommune</label>
					<select class="category_select" name="b_kommune">
						<?php foreach($m->g('kommuner') as $kommune_data) { ?>
							<option <?php
								if( $kommune_data['id'] == $controller['innslag']->g('b_kommune') )
									echo 'selected' ?> value="<?=$kommune_data['id']?>"><?=$kommune_data['name']?></option>
						<?php } ?>
	 				</select>
	 				<input type="hidden" name="log_current_value_b_kommune" value="<?=$controller['innslag']->g('b_kommune')?>" />
 				</div>
				<?php } ?>

				<?php if($controller['innslag']->g('bt_id')!=1) { ?>
	 				<input type="hidden" name="log_current_value_b_kategori" value="<?=$controller['innslag']->g('b_kategori')?>" />
	 				<input type="hidden" name="b_kategori" value="<?=$controller['innslag']->g('b_kategori')?>" />
	 			<?php
				} else { ?>
				<div class="group">
					<label class="category_label" for="b_kategori">Kategori</label>
					<select class="category_select" name="b_kategori">
						<?php foreach( $controller['kategorier'] as $key => $kategori ) { ?>
							<option <?php if( $controller['innslag']->g('b_kategori') == $key ) echo 'selected' ?> value="<?=$key?>"><?=$kategori?></option>
						<?php } ?>
	 				</select>
	 				<input type="hidden" name="log_current_value_b_kategori" value="<?=$controller['innslag']->g('b_kategori')?>" />
 				</div>
 				<?php } ?>
				<div class="group">
	 				<label class="genre_label" for="b_sjanger">Sjanger</label>
	 				<input class="genre_input" type="text" name="b_sjanger" value="<?=$controller['innslag']->g('b_sjanger')?>" />
	 				<input type="hidden" name="log_current_value_b_sjanger" value="<?=$controller['innslag']->g('b_sjanger')?>" />
 				</div>
 				<div class="clear"></div>
				<div class="group">
	 				<label class="tech_demands_label" for="td_demand">Tekniske behov</label>
	 				<textarea class="tech_demands_text" name="td_demand"><?=$controller['innslag']->g('td_demand')?></textarea>
	 				<input type="hidden" name="log_current_value_td_demand" value="<?=$controller['innslag']->g('td_demand')?>" />
	 			</div>
				<div class="group">	
	 				<label class="description_label" for="b_description">Beskrivelse</label>
	 				<textarea class="description_text" name="b_description"><?=$controller['innslag']->g('td_konferansier')?></textarea>
	 				<input type="hidden" name="log_current_value_b_description" value="<?=$controller['innslag']->g('td_konferansier')?>" />
				</div>
				<div class="group">
					<label class="show_desc_label" for="td_description">Vis beskrivelsen på ukm.no?</label>
					<span class="radio_group">
						<input class="show_desc_radio" type="radio" name="td_description" <?php if( $controller['visbeskrivelse'] ) echo 'checked' ?> value="true" />Ja
					</span>
					<span class="radio_group">
						<input class="show_desc_radio" type="radio" name="td_description" <?php if( !$controller['visbeskrivelse'] ) echo 'checked' ?> value="false" />Nei
					</span>
					<input type="hidden" name="log_current_value_td_description" value="<?=$controller['visbeskrivelse']?>" />
					<div class="clear"></div>
				</div>
				<?php echo ukmdeltakere_ajax_buttons('innslag',$controller['innslag']->g('b_id')); ?>
				
			</fieldset>
		</form>
		<?php
	}
}
