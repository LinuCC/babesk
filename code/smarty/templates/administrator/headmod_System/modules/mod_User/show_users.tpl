{extends file=$UserParent}{block name=content}
<table width=100%>
<tr><th align='center'>{$navbar}</th></tr>
</table>

<table>
	<thead>
		<tr bgcolor='#33CFF'>
			<th align='center'>ID</th>
			<th align='center'>Vorname</th>
			<th align='center'>Name</th>
			<th align='center'>Benutzername</th>
			<th align='center'>Geburtstag</th>
			<th align='center'>Geld</th>
			<th align='center'>Gruppe</th>
			<th align='center'>letzter Login</th>
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
			<td align="center">{$user.credit}</td>
			<td align="center">{$user.groupname}</td>
			<td align="center">{$user.last_login}</td>
			<td align="center" bgcolor='#FFD99'>
			<form action="index.php?section=System|User&action=4&ID={$user.ID}" method="post"><input type='submit' value='bearbeiten'></form>
			<form action="index.php?section=System|User&action=3&ID={$user.ID}" method="post"><input type='submit' value='löschen'></form>
			</td>
		</tr>
		{/foreach}
	</tbody>
</table>



{/block}