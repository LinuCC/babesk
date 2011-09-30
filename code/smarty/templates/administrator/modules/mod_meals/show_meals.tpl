
<table>
		<thead>
			<tr>
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
			<tr>
				<td align="center">{$meal.ID}</td>
				<td align="center">{$meal.name}</td>
				<td align="center">{$meal.description}</td>
				<td align="center">{$meal.price_class}</td>
				<td align="center">{$meal.date}</td>
				<td align="center">{$meal.max_orders}</td>
			</tr>
			{/foreach}
		</tbody>
</table>