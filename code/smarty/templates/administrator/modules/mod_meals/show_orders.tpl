Bestellungen für den {$ordering_date}:
<br>
<br>
{foreach $num_orders as $num_order} <h4>{$num_order.name} hat
{$num_order.number} Bestellungen:</h4>
	{foreach $num_order.user_groups as $group} 
<p style="margin-left:10%">Gruppe <b>{$group.name}</b> hat <b>{$group.counter}</b> mal bestellt.</p>
	{/foreach}
	
<br>
{/foreach}

<table style="text-align: center;width:100%">
	<thead>
		<tr bgcolor="#33CFF">
			<th>Men&uuml;</th>
			<th>Person</th>
			<th>Wurde abgeholt</th>
		</tr>
	</thead>

	<tbody>
		{foreach $orders as $order}
		<tr bgcolor="#FFC33">
			<td>{$order.meal_name}</td>
			<td>{$order.user_name}</td>
			<td style="text-align: center;">{$order.is_fetched}</td>
		</tr>
		{/foreach}
	</tbody>
</table>