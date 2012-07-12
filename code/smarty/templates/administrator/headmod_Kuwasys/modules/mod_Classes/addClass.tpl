{extends file=$inh_path} {block name='content'}

<h2 class='moduleHeader'>Einen Kurs hinzufügen</h2>

<form action='index.php?section=Kuwasys|Classes&action=addClass' method='post'>

	<label>Bezeichner: <input type='text' name='label'></label><br><br>
	<label>maximale Registrierungen: <input type='text' name='maxRegistration'></label><br><br>
	<label>Zu welchem Schuljahr gehört der Kurs? 
	<select name='schoolYear' size='1'>
		{foreach $schoolYears as $schoolYear}
			<option 
				value='{$schoolYear.ID}' 
				{if $schoolYear.active}selected='selected'{/if}>
				{$schoolYear.label}
			</option>
		{/foreach}
	</select>
	</label><br><br>
	<input type='submit' value='Kurs hinzufügen'>
</form>

{/block}