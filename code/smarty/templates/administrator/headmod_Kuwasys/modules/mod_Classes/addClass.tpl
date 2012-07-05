{extends file=$inh_path} {block name='content'}

<h2 class='moduleHeader'>Eine Klasse hinzufügen</h2>

<form action='index.php?section=Kuwasys|Classes&action=addClass' method='post'>
	<label>Name des Kurses:<br><input type='text' name='label'></label><br><br>
	<label>Maximale Registrierungen:<br><input type='text' name='maxRegistration' maxlength='5'></label><br><br>
	<input type='submit' value='Kurs hinzufügen'>
</form>

{/block}