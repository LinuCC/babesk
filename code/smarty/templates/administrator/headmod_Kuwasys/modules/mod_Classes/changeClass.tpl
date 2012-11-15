{extends file=$inh_path} {block name='content'}

<h2 class='moduleHeader'>Kurs verändern</h2>

<form action='index.php?section=Kuwasys|Classes&action=changeClass&ID={$class.ID}' method='post'>
	<label>Name des Kurses: <input type='text' name='label' value='{$class.label}'></label><br><br>
	<label>Beschreibung: <textarea name='description' maxlength='1024' rows='4' cols='50'>{$class.description}</textarea></label><br><br>
	<label>Maximale Anmeldungen: <input type='text' name='maxRegistration' value='{$class.maxRegistration}' maxlength='5'></label><br><br>
	<label>Schuljahr: <select name='schoolYear' size='1'>
		{foreach $schoolYears as $schoolYear}
			<option
				value='{$schoolYear.ID}'
				{if $nowUsedSchoolYearID == $schoolYear.ID}selected='selected'{/if}>
				{$schoolYear.label}
			</option>
		{/foreach}
	</select></label><br><br>
	<select name='weekday' size='1'>
		{foreach $classUnits as $classUnit}
		<option value='{$classUnit.ID}'{if $classUnit.ID == $class.unitId}selected="selected"{/if}>{$classUnit.translatedName}</option>
		{/foreach}
	</select><br><br>
	<label>Registrierungen für Schüler ermöglichen: <input type="checkbox" name="allowRegistration" value="1"
				{if $class.registrationEnabled}checked="checked"{/if}
			></label><br><br>
	<input type='submit' value='Kurs verändern'>
</form>

{/block}