{extends file=$inh_path}
{block name='content'}

{foreach $schoolyears as $schoolyear}
	{if $schoolyear.active}
		{$activeSchoolyear = $schoolyear}
	{/if}
{/foreach}

<script src="../smarty/templates/administrator/AddItemInterface.js">
	</script>
<script type="text/javascript">

$(document).ready(function() {

	addItemInterface = new AddItemInterface();

	$('.inputItem').on('focusout', function() {

		var elRegex = '';

		switch($(this).attr('name')) {
			case 'forename':
				elRegex = 'name';
				break;
			case 'name':
				elRegex = 'name';
				break;
			case 'username':
				elRegex = 'name';
				break;
			case 'password':
				elRegex = 'password';
				break;
			case 'passwordRepeat':
				elRegex = 'password';
				break;
			case 'email':
				elRegex = 'email';
				break;
			case 'telephone':
				elRegex = 'number';
				break;
			default:
				return;
		}

		addItemInterface.userInputCheck($(this).val(), elRegex, $(this));
	});
});

</script>

<style type='text/css'  media='all'>
.moduleFormulars {
	position: relative;
}

select, input.moduleFormulars {
	position: absolute;
	left: 250px;
}
</style>

<h2 class='moduleHeader'>Einen Benutzer hinzufügen</h2>
<br>
<div class='moduleFormulars'>
<form action='index.php?section=Kuwasys|Users&action=addUser' method='post'>
	<label>Vorname:<input type='text' name='forename' class='moduleFormulars inputItem'></label> <br><br>
	<label>Name:<input type='text' name='name' class='moduleFormulars inputItem'></label> <br><br>
	<label>Benutzername:<input type='text' name='username' class='moduleFormulars inputItem'></label> <br><br>
	<label>Passwort:<input type='password' name='password' class='moduleFormulars inputItem'></label> <br><br>
	<label>Passwort widerholen:<input type='password' name='passwordRepeat' class='moduleFormulars inputItem'></label> <br><br>
	<label>Email-Adresse:<input type='text' name='email' class='moduleFormulars inputItem'></label> <br><br>
	<label>Telefonnummer:<input type='text' name='telephone' class='moduleFormulars inputItem'></label> <br><br>
	<label>Geburtstag:{html_select_date start_year="-100"} <br><br>
	<label>Klasse:
		<select name='grade' size='1'>
			<option value='NoGrade' selected='selected'>==Keine Klasse==</option>
		{foreach $grades as $grade}
			<option
				value='{$grade.ID}'{if $grade.schoolyearId != $activeSchoolyear.ID}disabled="disabled"{/if}>
				{$grade.gradeValue} - {$grade.label}
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