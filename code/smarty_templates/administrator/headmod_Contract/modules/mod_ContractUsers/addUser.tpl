{extends file=$inh_path}
{block name='content'}

{foreach $schoolyears as $schoolyear}
	{if $schoolyear.active}
		{$activeSchoolyear = $schoolyear}
	{/if}
{/foreach}

<style type='text/css'  media='all'>
div.moduleFormulars {
	width:350px;
	margin:0 auto;
}

select, input.moduleFormulars {
	float:right;
}
</style>

<h2 class='module-header'>Einen Benutzer hinzufügen</h2>
<br>
<div class='moduleFormulars'>
<form action='index.php?section=Kuwasys|Users&action=addUser' method='post'>
	<label>Vorname:<input type='text' name='forename' class='moduleFormulars'></label> <br><br>
	<label>Name:<input type='text' name='name' class='moduleFormulars'></label> <br><br>
	<label>Benutzername:<input type='text' name='username' class='moduleFormulars'></label> <br><br>
	<label>Passwort:<input type='password' name='password' class='moduleFormulars'></label> <br><br>
	<label>Passwort widerholen:<input type='password' name='passwordRepeat' class='moduleFormulars'></label> <br><br>
	<label>Email-Adresse:<input type='text' name='email' class='moduleFormulars'></label> <br><br>
	<label>Telefonnummer:<input type='text' name='telephone' class='moduleFormulars'></label> <br><br>
	<label>Geburtstag:{html_select_date start_year="-100"} <br><br>
	<label>Klasse:
		<select name='grade' size='1'>
			<option value='NoGrade' selected='selected'>==Keine Klasse==</option>
		{foreach $grades as $grade}
			<option
				value='{$grade.ID}'{if $grade.schoolyearId != $activeSchoolyear.ID}disabled="disabled"{/if}>
				{$grade.gradelevel} - {$grade.label}
			</option>
		{/foreach}
	</select><br><br>
	<label>Schuljahr:
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
	<input type='submit' value='Hinzufügen'>
</form>
</div>
{/block}
