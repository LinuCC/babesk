{extends file=$inh_path} {block name='content'}

<h2 class='moduleHeader'>Kurs verändern</h2>

<form action='index.php?section=Kuwasys|Classes&action=changeClass&ID={$class.ID}' method='post'>
	<label>Name des Kurses: <input type='text' name='label' value='{$class.label}'></label><br><br>
	<label>Maximale Anmeldungen: <input type='text' name='maxRegistration' value='{$class.maxRegistration}' maxlength='5'></label><br><br>
	<select name='schoolYear' size='1'>
		{foreach $schoolYears as $schoolYear}
			<option 
				value='{$schoolYear.ID}' 
				{if $nowUsedSchoolYearID == $schoolYear.ID}selected='selected'{/if}>
				{$schoolYear.label}
			</option>
		{/foreach}
	</select>
	<input type='submit' value='Kurs verändern'>
</form>

{/block}