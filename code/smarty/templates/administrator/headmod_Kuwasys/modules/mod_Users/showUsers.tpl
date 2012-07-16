{extends file=$inh_path} {block name='content'}

<style type='text/css'  media='all'>
/*Table should not be over the Border of the Line of the main-block*/
#main {
	width:1000px;
}
</style>

<h2 class='moduleHeader'>Die Benutzer</h2>

<table>
	<thead>
		<tr bgcolor='#33CFF'>
			<th align='center'>ID</th>
			<th align='center'>Vorname</th>
			<th align='center'>Name</th>
			<th align='center'>Benutzername</th>
			<th align='center'>Geburtstag</th>
			<th align='center'>Email-Adresse</th>
			<th align='center'>Telefonnummer</th>
			<th align='center'>letzter Login</th>
			<th align='center'>In Klasse</th>
			<th align='center'>Schuljahr</th>
		</tr>
	</thead>
	<tbody>
		{foreach $users as $user}
		<tr bgcolor='#FFC33'>
			<td align="center">{$user.ID}</td>
			<td align="center">{$user.forename}</td>
			<td align="center">{$user.name}</td>
			<td align="center">{$user.username}</td>
			<td align="center">{$user.birthday}</td>
			<td align="center">{$user.email}</td>
			<td align="center">{$user.telephone}</td>
			<td align="center">{$user.last_login}</td>
			<td align="center">{$user.gradeLabel}</td>
			<td align="center">{$user.schoolyearLabel}</td>
			<td align="center" bgcolor='#FFD99'>
			<form action="index.php?section=Kuwasys|Users&action=changeUser&ID={$user.ID}" method="post"><input type='submit' value='bearbeiten'></form>
			<form action="index.php?section=Kuwasys|Users&action=deleteUser&ID={$user.ID}" method="post"><input type='submit' value='löschen'></form>
			</td>
		</tr>
		{/foreach}
	</tbody>
</table>

{/block}