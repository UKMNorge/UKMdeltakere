<?php
$btname = new SQL("SELECT `bt_name`
				   FROM `smartukm_band_type`
				   WHERE `bt_id` = '#btid'",
				   array('btid'=>$_GET['addnewforwarded']));
$btname = $btname->run('field', 'bt_name');

$m = new monstring(get_option('pl_id'));
?>

<h1>Legg til nytt <?= strtolower($btname) ?>-innslag</h1>

<form action="admin.php" method="GET">
<input type="hidden" name="page" value="<?= $_GET['page']?>" />
<input type="hidden" name="addnew" value="<?= $_GET['addnewforwarded']?>" />
<br />
	<label>
		Kommune:
		<select name="kommune">
		<?php foreach($m->kommuneArray() as $id => $name) { ?>
			<option value="<?= $id ?>"><?= $name ?></option>
		<?php } ?>	
		</select>
	</label>
<br />	
	<input type="submit" value="Opprett" />
</form>