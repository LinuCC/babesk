{extends file=$mealParent}{block name=filling_content}

<table class="table table-responsive table-striped">
		<thead>
			<tr>
				<th>ID</th>
				<th>Name</th>
				<th>Beschreibung</th>
				<th>Preisklasse</th>
				<th>Datum</th>
				<th>Maximale Bestellungen</th>
				<th>Optionen</th>
			</tr>
		</thead>
		<tbody>
			{foreach $meals as $meal}
			<tr >
				<td>{$meal.ID}</td>
				<td>{$meal.name}</td>
				<td>{$meal.description}</td>
				<td>{$meal.priceClassName}</td>
				<td>{$meal.date}</td>
				<td>{$meal.max_orders}</td>
				<td>
					<form action="index.php?section=Babesk|Meals&action=8&id={$meal.ID}" method="POST">
						<input type="hidden" value="{$meal.name}" name="name">
						<input type="hidden" value="{$meal.description}" name="description">
						<input type="hidden" value="{$meal.date}" name="date">
						<input type="hidden" value="{$meal.max_orders}" name="max_orders">
						<input type="hidden" value="{$meal.price_class}" name="pcID">
						<input class="btn btn-xs btn-default" type="submit" value="duplizieren">
					</form>
					<form action="index.php?section=Babesk|Meals&action=5&id={$meal.ID}" method="POST">
						<input class="btn btn-xs btn-danger" type="submit" value="löschen">
					</form>
				</td>
			</tr>
			{/foreach}
		</tbody>
</table>
{/block}