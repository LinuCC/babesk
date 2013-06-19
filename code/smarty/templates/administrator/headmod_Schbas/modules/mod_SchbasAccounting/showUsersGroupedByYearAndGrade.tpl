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

{$modAction = "showUsersGroupedByYearAndGrade"}

<fieldset class="selectiveLink">
	<legend>Klasse</legend>
	{foreach $gradeAll as $grade}
		<a class="selectiveLink" href="index.php?section=Schbas|SchbasAccounting&action=1&gradeIdDesired={$grade.gradeValue}-{$grade.label}"
		{if $grade.ID == $gradeDesired}style="color:rgb(150,40,40);"{/if}
		>
		{$grade.gradeValue}{$grade.label}
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
			<th align='center'>Klasse</th>
			<th align='center'>Zahlung</th>
		</tr>
	</thead>
	<tbody>
		{foreach $users as $user}
		<tr>
			<td align="center">{$user.ID}</td>
			<td align="center">{$user.forename}</td>
			<td align="center">{$user.name}</td>
			<td align="center">{$user.username}</td>
			<td align="center">{$user.gradeLabel}</td>
			<td align="center">
			<form action="index.php?section=Schbas|SchbasAccounting" method="get">
			<input type="text" name="username" size="10" maxlength="50" /><br />
			</form>
			</td>
		</tr>
		{/foreach}
	</tbody>
</table>


{/block}