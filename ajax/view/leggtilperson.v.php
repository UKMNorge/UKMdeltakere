<?php function UKMdeltakere_ajax_view($c){ 
?>

<form class="edit_person_form">
	<div class="wrapper_old_person">
		<h3>Velg en person tidligere registrert på din mønstring:</h3>
		<div class="group sok_kontaktperson">
			<label for="tidligere_registrerte_kontaktpersoner">Søk etter navn på personer: <br /></label><input id="tidligere_registrerte_kontaktpersoner" type="text" data-children="#person_liste li"/>
			<span class="clickable" id="remove_choice" style="display: none;">Fjern valg</span>
		</div>
		<div class="clear">
		</div>
		<div class="personer" id="person_liste">
				<ul>
					<?php
						foreach ($c['personer'] as $key => $person) {
							echo '<li data-filter="' . $person->g('name') . '"><label for="person_id' . $person->g('p_id') . '"><input type="radio" name="p_id" value="' . $person->g('p_id') . '" id="person_id' . $person->g('p_id') . '">'.$person->g('name').'</label></li>';
						}
					?>
				</ul>
			<p class="empty_search" id="tidligere_registrerte_kontaktpersoner_empty" style="display: none">Beklager, ingen treff!</p>
		</div>
	</div>
	
	<fieldset>
		<div class="wrapper_new_person">
			<h3>eller, legg til en ny person:</h3>
			<span class="clickable" id="remove_new_data" style="display: none;">Tøm skjema</span>
			
			<div class="group">
				<label for="p_firstname">Fornavn *</label>
				<input class="first_name_input" type="text" name="p_firstname" />
			</div>
			<div class="group">
				<label for="p_lastname">Etternavn *</label>
				<input class="last_name_input" type="text" name="p_lastname" />
			</div>
			<div class="group">
				<label for="p_dob">Alder *</label>
				<select class="age_select" class="age_select" name="p_dob">
					<?php 
					$year = date('Y');
					for($i = 10; $i<=26; ++$i) {
						$date = '1 January ' . ($year-$i);
						$timestamp = strtotime( $date );
					?>
					<option value="<?=$timestamp?>"><?= ($i==26 ? '25+' : $i) . ' &aring;r'?></option>
					<?php } ?>
				</select>
			</div>
			<div class="group">
				<label for="p_email">E-post</label>
				<input class="email_input" type="text" name="p_email" />
			</div>
			<div class="group">
				<label for="p_phone">Mobil *</label>
				<input class="cellphone_input" type="text" name="p_phone" />
            </div>
			<div class="group">
		        <?php if (sizeof($kommuner) > 1) { ?>
					<label for="p_kommune">Kommune</label>
					<select class="county_input" name="p_kommune">
					<?php foreach( $kommuner as $key => $kommune ) {?>
						<option value="<?=$key?>"><?=$kommune?></option>	
					<?php } ?>
					</select>
		        <?php } ?>
			</div>
			<div class="group">
				<label for="p_adress">Adresse</label>
				<input class="adress_input" type="text" name="p_adress" />
			</div>
			<div class="group">
				<label for="p_postnumber">Postnr.</label>
				<input class="postal_input" type="text" name="p_postnumber" />
			</div>
		</div>
		
		<div class="group">
			<label class="role_label" for="rolle">Rolle</label>
			<input class="role_input" type="text" name="instrument" />
		</div>
        <input type="hidden" name="b_id" value="<?=$_POST['i'];?>" />
    
    
    	<div class="UKMdeltakere_buttons">
			<a class="ajax_cancel_modal" href="#">avbryt</a> 
			<input type="button" value="Lagre" class="ajax_save_modal" save="leggtilperson" bid="<?=$_POST['i'];?>" />
		</div>

	</fieldset>
</form>
<?php } ?>
