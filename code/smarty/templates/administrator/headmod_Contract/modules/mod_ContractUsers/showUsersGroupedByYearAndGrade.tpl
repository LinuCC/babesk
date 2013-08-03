{extends file=$inh_path} {block name='content'}

<style type='text/css'  media='all'>
/*Table should not be over the Border of the Line of the main-block*/
#main {
	width:1100px;
}

fieldset.selectiveLink {
	margin-left: 5%;
	margin-right: 5%;
	margin-bottom: 30px;
	border: 2px dashed rgb(100,100,100);
}

a.selectiveLink {
	padding: 5px;
}

.dataTable {
	margin: 0 auto;
}
</style>

<script type="text/javascript">
function showOptions (ID) {
	document.getElementById('optionButtons' + ID).hidden = false;
	document.getElementById('option' + ID).hidden = true;
}
</script>

<h2 class='moduleHeader'>Die Benutzer</h2>

<fieldset class="selectiveLink">
<legend>Jahrgang</legend>
{foreach $schoolyearAll as $schoolyear}
	<a class="selectiveLink" href="index.php?section=Kuwasys|Users&action=showUsersGroupedByYearAndGrade&schoolyearIdDesired={$schoolyear.ID}"
		{if $schoolyear.ID == $schoolyearDesired.ID}style="color:rgb(150,40,40);"{/if}>
		{$schoolyear.label}
	</a>
{/foreach}
</fieldset>
<fieldset class="selectiveLink">
	<legend>Klasse</legend>
	{foreach $gradeAll as $grade}
		<a class="selectiveLink" href="index.php?section=Kuwasys|Users&action=showUsersGroupedByYearAndGrade&schoolyearIdDesired={$schoolyearDesired.ID}&gradeIdDesired={$grade.ID}"
		{if $grade.ID == $gradeDesired.ID}style="color:rgb(150,40,40);"{/if}>
				{$grade.gradelevel}{$grade.label}
		</a>
	{/foreach}
</fieldset>


<table class="dataTable">
	<thead>
		<tr>
			<th align='center'>ID</th>
			<th align='center'>Vorname</th>
			<th align='center'>Name</th>
			<th align='center'>Benutzername</th>
			<th align='center'>Geburtstag</th>
			<th align='center'>Email-Adresse</th>
			<th align='center'>Telefonnummer</th>
			<th align='center'>letzter Login</th>
		</tr>
	</thead>
	<tbody>
		{foreach $users as $user}
		<tr>
			<td align="center">{$user.ID}</td>
			<td align="center">{$user.forename}</td>
			<td align="center">{$user.name}</td>
			<td align="center">{$user.username}</td>
			<td align="center">{$user.birthday}</td>
			<td align="center">{$user.email}</td>
			<td align="center">{$user.telephone}</td>
			<td align="center">{$user.last_login}</td>
			<td align="center" bgcolor='#FFD99'>
			<div id='option{$user.ID}'>
			<form method="post"><input type='button' value='Optionen' onclick='showOptions("{$user.ID}")'></form>
			</div>
			<div id='optionButtons{$user.ID}' hidden>
			<form action="index.php?section=Kuwasys|Users&action=changeUser&ID={$user.ID}" method="post"><input type='submit' value='bearbeiten'></form>
			<form action="index.php?section=Kuwasys|Users&action=deleteUser&ID={$user.ID}" method="post"><input type='submit' value='löschen'></form>
			<form action="index.php?section=Kuwasys|Users&action=addUserToClass&ID={$user.ID}" method="post"><input type='submit' value='zu einem Kurs hinzufügen'></form>
			<form action="index.php?section=Kuwasys|Users&action=showUserDetails&ID={$user.ID}" method="post"><input type='submit' value='Details anzeigen'></form>
			</div>
			</td>
		</tr>
		{/foreach}
	</tbody>
</table>

<form action="index.php?section=Kuwasys|Users&action=printParticipationConfirmation" method="post">
	{foreach $users as $user}
		<input type="hidden" name="userIds[]" value="{$user.ID}">
	{/foreach}
	<input type="hidden" name="schoolyearId" value="{$schoolyearDesired.ID}">
	<input type="hidden" name="gradeId" value="{$gradeDesired.ID}">
	<input type="submit" value="Bestätigungsdokumente für diese Klasse abrufen">
</form>

<form action="index.php?section=Kuwasys|Users&action=sendEmailsParticipationConfirmation" method="post">
	{foreach $users as $user}
		<input type="hidden" name="userIds[]" value="{$user.ID}">
	{/foreach}
	<input type="hidden" name="schoolyearId" value="{$schoolyearDesired.ID}">
	<input type="hidden" name="gradeId" value="{$gradeDesired.ID}">
	<input type="submit" value="Emails mit Bestätigungsdokumenten versenden">
</form>

{/block}
