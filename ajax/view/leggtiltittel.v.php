<?php function UKMdeltakere_ajax_view($c) { ?>

<form class="edit_title_form">
	<fieldset>
	<?php 
		foreach($c['kategorier'] as $key => $felt) {
			
			switch( $key ) {
				
				default:
					?>
					<div class="group">
						<label for="<?=$key?>"><?=$felt?></label>
						<input type="text" name="<?=$key?>" value="" class="title_edit_<?=$key?>" />
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
							<option value="<?=$time?>"><?=gmdate("i:s", $time);?></option>
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
	<input type="hidden" name="b_id" value="<?=$c['b_id']?>" />
	<input type="hidden" name="bt_form" value="<?=$c['form']?>" />
    	<div class="UKMdeltakere_buttons">
			<a class="ajax_cancel_modal" href="#">avbryt</a> 
			<input type="button" value="Lagre" class="ajax_save_modal" save="leggtiltittel" bid="<?=$c['b_id'];?>" />
		</div>
	</fieldset>
</form>

<?php 
} 
?>