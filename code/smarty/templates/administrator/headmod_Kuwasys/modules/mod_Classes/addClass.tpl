{extends file=$inh_path} {block name='content'}

<h2 class='moduleHeader'>Einen Kurs hinzufügen</h2>

<form action='index.php?module=administrator|Kuwasys|Classes|AddClass'
	method='post'>

	<label>Name des Kurses: <input type='text' name='label'></label><br><br>
	<label>Beschreibung: <textarea name='description' maxlength='1024' rows='4' cols='50'></textarea></label><br><br>
	<label>maximale Registrierungen: <input type='text' name='maxRegistration' maxlength='4'></label><br><br>
	<label>Zu welchem Schuljahr gehört der Kurs?
	<select name='schoolyear' size='1'>
		{foreach $schoolyears as $schoolyear}
			<option
				value='{$schoolyear.ID}'
				{if $schoolyear.active}selected='selected'{/if}>
				{$schoolyear.label}
			</option>
		{/foreach}
	</select>
	</label><br><br>
	<label>Veranstaltungstag des Kurses:
	<select name='classunit' size='1'>
		{foreach $classunits as $classunit}
		<option value='{$classunit.ID}'>{$classunit.translatedName}</option>
		{/foreach}
	</select>
	</label><br><br>
	<label>Registrierungen für Schüler ermöglichen: <input type="checkbox" name="allowRegistration" value="1" checked="checked"></label><br><br>
	<input type='submit' value='Kurs hinzufügen'>
</form>

{/block}
