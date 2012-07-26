{extends file=$inh_path} {block name='content'}

<h2 class='moduleHeader'>Kurs verändern</h2>

<form action='index.php?section=Kuwasys|Classes&action=changeClass&ID={$class.ID}' method='post'>
	<label>Name des Kurses: <input type='text' name='label' value='{$class.label}'></label><br><br>
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
		<option value='Mon'{if $class.weekday == 'Mon'}selected="selected"{/if}>Montag</option>
		<option value='Tue'{if $class.weekday == 'Tue'}selected="selected"{/if}>Dienstag</option>
		<option value='Wed'{if $class.weekday == 'Wed'}selected="selected"{/if}>Mittwoch</option>
		<option value='Thu'{if $class.weekday == 'Thu'}selected="selected"{/if}>Donnerstag</option>
		<option value='Fri'{if $class.weekday == 'Fri'}selected="selected"{/if}>Freitag</option>
		<option value='Sat'{if $class.weekday == 'Sat'}selected="selected"{/if}>Samstag</option>
		<option value='Sun'{if $class.weekday == 'Sun'}selected="selected"{/if}>Sonntag</option>
	</select>
	<label>Registrierungen für Schüler ermöglichen: <input type="checkbox" name="allowRegistration" value="1" 
				{if $class.registrationEnabled}checked="checked"{/if}
			></label><br><br>
	<input type='submit' value='Kurs verändern'>
</form>

{/block}