<?php function UKMdeltakere_ajax_view($c){?>

<form class="edit_title_form" id="edit_title_<?=$c['tittel']->g('t_id')?>">
	<fieldset>
		<legend>Endre detaljer for <?=$c['tittel']->g('tittel')?></legend>		
		<?php 
			foreach( $c['kategorier'] as $key => $felt ) {
				
				switch( $key ) {
					
					default:
						?>
						<div class="group">
							<label for="<?=$key?>"><?=$felt?></label>
							<input type="text" name="<?=$key?>" value="<?=$c['tittel']->g($key)?>" class="title_edit_<?=$key?>" />
							<input type="hidden" name="log_current_value_<?=$key?>" value="<?=$c['tittel']->g($key)?>" />
						</div>
						<?php
						break;
					
					case 'varighet':
						?>
						<div class="group">
							<label for="<?=$key;?>"><?=$felt?></label>
							<select name="<?=$key?>" class="title_edit_<?=$key?>">
							<?php 
								$max = 600;
								for($time = 10; $time <= $max; $time = $time + 10) {
							?>
								<option <?php if( $c['tittel']->g($key) == $time ) echo 'selected' ?> value="<?=$time?>"><?=gmdate("i:s", $time);?></option>
							<?	
								}
							?>
							</select>
						</div>						
						<?php
						break;		
				}
			}
		?>
		<input type="hidden" name="b_id" value="<?=$c['b_id']?>"/>
        <input type="hidden" name="form" value="<?=$c['tittel']->g('form');?>">
        <input type="hidden" name="t_id" value="<?=$c['tittel']->g('t_id');?>">
		<?=ukmdeltakere_ajax_buttons('redigertittel','');?>	
	</fieldset>
</form>

<?php } ?>