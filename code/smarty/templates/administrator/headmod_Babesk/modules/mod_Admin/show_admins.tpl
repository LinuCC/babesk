{extends file=$adminParent}{block name=content}
<table>
	<thead>
		<tr bgcolor='#33CFF'>
			<th align='center'>ID</th>
			<th align='center'>Name</th>
			<th align='center'>Administratorgruppe</th>
		</tr>
	</thead>
	<tbody>
		{foreach $admins as $admin}
		<tr bgcolor='#FFC33'>
			<td align="center">{$admin.ID}</td>
			<td align="center">{$admin.name}</td>
			<td align="center">{$admin.groupname}</td>
			<td align="center" bgcolor='#FFD99'>
			<form action="index.php?section=Babesk|Admin&action=7&ID={$admin.ID}" method="post"><input type='submit' value='bearbeiten'></form>
			<form action="index.php?section=Babesk|Admin&action=5&ID={$admin.ID}" method="post"><input type='submit' value='löschen'></form>
			</td>
		</tr>
		{/foreach}
	</tbody>
</table>
{/block}