{extends file=$soliParent}{block name=content}

<p>Um die Benutzer zu verändern oder zu löschen, benutzen sie bitte das Benutzer-Modul</p>

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
			</td>
		</tr>
		{/foreach}
	</tbody>
</table>


{/block}