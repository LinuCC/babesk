{extends file=$inh_path} {block name='content'}

<h2 class='moduleHeader'>Kurs verändern</h2>

<form action='index.php?module=administrator|Kuwasys|Classes|ChangeClass&amp;ID={$class.ID}' method='post'>
	<label>Name des Kurses: <input type='text' name='label' value='{$class.label}'></label><br><br>
	<label>Beschreibung: <textarea name='description' maxlength='1024' rows='4' cols='50'>{$class.description}</textarea></label><br><br>
	<label>Maximale Anmeldungen: <input type='text' name='maxRegistration' value='{$class.maxRegistration}' maxlength='5'></label><br><br>
	<label>Schuljahr: <select name='schoolyear' size='1'>
		{foreach $schoolyears as $schoolyear}
			<option
				value='{$schoolyear.ID}'
				{if $class.schoolyearId == $schoolyear.ID}selected='selected'{/if}>
				{$schoolyear.label}
			</option>
		{/foreach}
	</select></label><br><br>
	<select name='classunit' size='1'>
		{foreach $classunits as $classunit}
		<option value='{$classunit.ID}'{if $classunit.ID == $class.unitId}selected="selected"{/if}>{$classunit.translatedName}</option>
		{/foreach}
	</select><br><br>
	<label>Registrierungen für Schüler ermöglichen: <input type="checkbox" name="allowRegistration" value="1"
				{if $class.registrationEnabled}checked="checked"{/if}
			></label><br><br>
	<input type='submit' value='Kurs verändern'>
</form>

{/block}
