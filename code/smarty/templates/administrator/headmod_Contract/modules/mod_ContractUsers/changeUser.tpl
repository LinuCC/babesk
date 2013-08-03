{extends file=$inh_path} {block name='content'}

<script type="text/javascript" src="../smarty/templates/administrator/headmod_Kuwasys/modules/mod_Users/changeUser.js">
</script>
{$userHasGrade = false}
{foreach $grades as $grade}
	{if $user.gradeIDSelected == $grade.ID}
		{$userHasGrade = true}
	{/if}
{/foreach}
<style type='text/css'  media='all'>
fieldset {
	border: 1px solid #000000;
}
</style>

<h2 class='moduleHeader'>Einen Benutzer verändern</h2>

<form action='index.php?section=Kuwasys|Users&action=changeUser&ID={$user.ID}' method='post'>
	<label>Vorname: <input type='text' value="{$user.forename}" name='forename'></label><br><br>
	<label>Name: <input type='text' value="{$user.name}" name='name'></label><br><br>
	<label>Benutzername: <input type='text' value="{$user.username}" name='username'></label><br><br>
	<!-- Show/Hide Passwords with JavaScript-->
	<div id='showPw'><button type='button' onclick='displayChangePassword()'>Passwort verändern</button></div>
	<div id='hidePw' hidden><button type='button' onclick='hideChangePassword()'>Passwort doch nicht verändern</button></div>
	<fieldset id='pwF' hidden>
	<label>Passwort: <input id='pw' type='password' name='password'></label><br><br>
	<label>Passwort wiederholen: <input id='pwRep' type='password' name='passwordRepeat'></label>
	</fieldset><br><br>
	<!--  -->
	<label>Klasse:
		<select name='grade' size='1'>
			<option value='NoGrade' {if (!$userHasGrade)}selected='selected'{/if}>==Keine Klasse==</option>
		{foreach $grades as $grade}
			<option
				value='{$grade.ID}'
				{if $user.gradeIDSelected == $grade.ID}
					selected='selected'
				{/if}>
				{$grade.gradelevel} - {$grade.label}
			</option>
		{/foreach}
	</select><br><br>
	<label>Schuljahr:
		<select name='schoolyear' size='1'>
		{if !isset($user.schoolyearIdSelected)}<option value='0' selected='selected'>==Keine Schuljahr==</option>{/if}
		{foreach $schoolyears as $schoolyear}
			<option
				value='{$schoolyear.ID}'
				{if $schoolyear.ID == $user.schoolyearIdSelected}selected='selected'{/if}>
				{$schoolyear.label}
			</option>
		{/foreach}
	</select><br><br>
	<label>Email-Adresse: <input type='text' value="{$user.email}" name='email'></label><br><br>
	<label>Telefonnummer: <input type='text' value="{$user.telephone}" name='telephone'></label><br><br>
	<input type='submit' value='Absenden'>
</form>

{/block}
