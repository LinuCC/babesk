<table cellpadding='10' cellspacing='10'>
	<thead> 
		<tr> 
			<th align="center">ID</th>
			<th align="center">pc_ID</th>
			<th align="center">Preisklassenname</th>
			<th align="center">zugehörige Gruppe</th>
			<th align="center">Preis</th>
		</tr>
	</thead>
	
	<tbody>
	{foreach $priceclasses as $priceclass}
		<tr>
			<td align="center">{$priceclass['ID']}</td>
			<td align="center">{$priceclass['pc_ID']}</td>
			<td align="center">{$priceclass['name']}</td>
			<td align="center">{$priceclass['group_name']}</td>
			<td align="center">{$priceclass['price']} Euro</td>
			<td align="center"><form action='index.php?section=priceclass&action=3&where={$priceclass['ID']}' method='post'>
				<input type="submit" value='löschen'>
			</form></td>
			<td align="center"><form action='index.php?section=priceclass&action=4&where={$priceclass['ID']}' method='post'>
				<input type="submit" value='ändern'>
			</form></td>
		</tr>
	{/foreach}
	</tbody>

</table>