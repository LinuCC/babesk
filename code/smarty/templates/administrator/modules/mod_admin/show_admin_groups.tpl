{extends file=$base_path}{block name=content}
<table>
	<thead>
		<tr bgcolor='#33CFF'>
			<th align='center'>ID</th>
			<th align='center'>Name</th>
			<th align='center'>erlaubte Module</th>
		</tr>
	</thead>
	<tbody>
		{foreach $admingroups as $admingroup}
		<tr bgcolor='#FFC33'>
			<td align="center">{$admingroup.ID}</td>
			<td align="center">{$admingroup.name}</td>
			<td align="center">{$admingroup.modules}</td>
			<td align="center" bgcolor='#FFD99'>
			<form action="index.php?section=admin&action=" method="post"><input type='submit' value='bearbeiten'></form>
			<form action="index.php?section=admin&action=6&ID={$admingroup.ID}" method="post"><input type='submit' value='löschen'></form>
			</td>
		</tr>
		{/foreach}
	</tbody>
</table>
{/block}