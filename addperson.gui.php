<?php
$btname = new SQL("SELECT `bt_name`
				   FROM `smartukm_band_type`
				   WHERE `bt_id` = '#btid'",
				   array('btid'=>$_GET['addperson']));
$btname = $btname->run('field', 'bt_name');
$m = new monstring(get_option('pl_id'));
?>

<h1>Legg til nytt <?= strtolower($btname) ?>-innslag</h1>

<br />
<div class="error">OBS: Alle felter er obligatoriske</div>


<form action="admin.php" method="GET">
<input type="hidden" name="page" value="<?= $_GET['page']?>" />
<input type="hidden" name="addnew" value="<?= $_GET['addperson']?>" />
<br />
	<label>
		Fornavn:
		<input type="text" name="fornavn" />
	</label>
<br />
	<label>
		Etternavn:
		<input type="text" name="etternavn" />
	</label>
<br />
	<label>
		Mobil:
		<input type="text" name="mobil" />
	</label>
<br />	
<?php if(get_option('site_type')!='kommune') { ?>
	<label>
		Kommune:
		<select name="kommune">
		<?php foreach($m->kommuneArray() as $id => $name) { ?>
			<option value="<?= $id ?>"><?= $name ?></option>
		<?php } ?>	
		</select>
	</label>
<br />
<?php } ?>
	<input type="submit" value="Opprett" />
</form>