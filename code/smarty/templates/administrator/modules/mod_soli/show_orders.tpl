<h3 align=center>{$name} - Essenszuschuss</h3><h3 align=right>KW {$ordering_date}</h3><br>

<table style="text-align: center;">
	<thead>
		<tr bgcolor="#33CFF">
			<th>Datum</th>
			<th>Men&uuml;</th>
			<th>Preis</th>
			<th>Eigenanteil</th>
			<th>Aus Kasse</th>
			<th>Wurde abgeholt</th>
		</tr>
	</thead>
	
	<tbody>
	{foreach $orders as $order}
		<tr bgcolor="#FFC33">
			<td>{$order.date}</td>
			<td>{$order.meal_name}</td>
			<td>{$order.price} EURO</td>
			<td>{$soli_price} EURO</td>
			<td>{$from_bank} EURO</td>
			<td style="text-align: center;">{$order.is_fetched}</td>
		</tr>
	{/foreach}
	<tr bgcolor="FFC33">
		<td></td>
		<td></td>
		<td></td>
		<td><b><u>Summe:</u></b></td>
		<td>{$sum} EURO</td>
		<td></td>
	</tr>
	</tbody>
</table>