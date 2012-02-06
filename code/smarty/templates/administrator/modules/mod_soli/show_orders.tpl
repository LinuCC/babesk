Bestellungen für den {$ordering_date}:<br><br>

{foreach $num_orders as $num_order}
Men&uuml; {$num_order.name} wurde {$num_order.number}-mal bestellt.<br>
{/foreach}
<table style="text-align: center;">
	<thead>
		<tr bgcolor="#33CFF">
			<th>Men&uuml;</th>
			<th>Person</th>
			<th>Preis</th>
			<th>Teilhabepaket</th>
			<th>Wurde abgeholt</th>
		</tr>
	</thead>
	
	<tbody>
	{foreach $orders as $order}
		<tr bgcolor="#FFC33">
			<td>{$order.meal_name}</td>
			<td>{$order.user_name}</td>
			<td>{$order.price}</td>
			<td>{$order.price-1}</td>
			<td style="text-align: center;">{$order.is_fetched}</td>
		</tr>
	{/foreach}
	</tbody>
</table>