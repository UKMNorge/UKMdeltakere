<?php function UKMdeltakere_ajax_view($c){?>

<form class="leggtiliforestilling">
	<fieldset>
		<legend>Velg forestilling</legend>
		<?php
		$forestillinger = $c['f'];
		$innslag_forestillinger = $c['if'];
		?>
		<select name="c_id">
		<?php
		foreach( $c['f'] as $val ) {
			if( count($c['if']) > 0 ) { 
				foreach( $c['if'] as $v ) {
					if( $val['c_id'] != $v['c_id'] ) {
					?>
						<option value="<?=$val['c_id']?>"><?=$val['c_name']?></option>
					<?php
					}
				}
			}
			else {
			?>
				<option value="<?=$val['c_id']?>"><?=$val['c_name']?></option>
			<?php				
			}
		}
		?>
		</select>
		<input type="hidden" name="b_id" value="<?=$c['b_id']?>" />
    	<div class="UKMdeltakere_buttons">
			<a class="ajax_cancel_modal" href="#">avbryt</a> 
			<input type="button" value="Lagre" class="ajax_save_modal" save="leggtiliforestilling" bid="<?=$c['b_id'];?>" />
		</div>
	</fieldset>
</form>

<?php } ?>