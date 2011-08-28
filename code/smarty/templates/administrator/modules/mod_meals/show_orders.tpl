Bestellungen für den {$ordering_date}:<br><br>

{foreach $num_orders as $num_order}
{$num_order.name} hat {$num_order.number} Bestellungen.<br>
{/foreach}

<table>
	<thead>
		<tr>
			<th>Men&uuml;</th>
			<th>Person</th>
		</tr>
	</thead>
	
	<tbody>
	{foreach $orders as $order}
		<tr>
			<td>{$order.meal_name}</td>
			<td>{$order.user_name}</td>
		</tr>
	{/foreach}
	</tbody>
</table>