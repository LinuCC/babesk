<table>
	<thead> 
		<tr> 
			<th align="center">ID</th>
			<th align="center">Gruppenname</th>
			<th align="center">maximales Guthaben</th>
		</tr>
	</thead>
	
	<tbody>
	{foreach $groups as $group}
		<tr>
			<td align="center">{$group['ID']}</td>
			<td align="center">{$group['name']}</td>
			<td align="center">{$group['max_credit']} Euro</td>
			<td align="center"><form action='index.php?section=groups&action=3&where={$group['ID']}' method='post'>
				<input type="submit" value='löschen'>
			</form></td>
			<td align="center"><form action='index.php?section=groups&action=4&where={$group['ID']}' method='post'>
				<input type="submit" value='ändern'>
			</form></td>
		</tr>
	{/foreach}
	</tbody>

</table>