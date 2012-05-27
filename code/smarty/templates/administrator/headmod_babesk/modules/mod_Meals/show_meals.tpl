{extends file=$mealParent}{block name=content}

<table>
		<thead>
			<tr bgcolor="#33CFF">
				<th>ID</th>
				<th>Name</th>
				<th>Beschreibung</th>
				<th>Preisklasse</th>
				<th>Datum</th>
				<th>Maximale Bestellungen</th>
			</tr>
		</thead>
		<tbody>
			{foreach $meals as $meal}
			<tr bgcolor="#FFC33">
				<td align="center">{$meal.ID}</td>
				<td align="center">{$meal.name}</td>
				<td align="center">{$meal.description}</td>
				<td align="center">{$meal.price_class}</td>
				<td align="center">{$meal.date}</td>
				<td align="center">{$meal.max_orders}</td>
				<td align="center"><form action="index.php?section=babesk|Meals&action=5&id={$meal.ID}" method="POST"><input type="submit" value="löschen"></form>
				<form action="index.php?section=babesk|Meals&action=8&id={$meal.ID}" method="POST">
					<input type="hidden" value="{$meal.name}" name="name">
					<input type="hidden" value="{$meal.description}" name="description">
					<input type="hidden" value="{$meal.date}" name="date">
					<input type="hidden" value="{$meal.max_orders}" name="max_orders">
					<input type="hidden" value="{$meal.price_class}" name="pcID">
					<input type="submit" value="duplizieren">
				</form>
				</td>
			</tr>
			{/foreach}
		</tbody>
</table>
{/block}