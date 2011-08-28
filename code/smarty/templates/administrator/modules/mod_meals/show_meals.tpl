
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
				<td>{$meal.ID}</td>
				<td>{$meal.name}</td>
				<td>{$meal.description}</td>
				<td>{$meal.price_class}</td>
				<td>{$meal.date}</td>
				<td>{$meal.max_orders}</td>
			</tr>
			{/foreach}
		</tbody>
</table>