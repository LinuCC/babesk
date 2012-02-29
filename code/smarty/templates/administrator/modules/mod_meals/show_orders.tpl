{extends file=$mealParent}{block name=content}
{literal}
<script type="text/javascript">

function switchInfo(divName) {	
	if(document.getElementById(divName).style.display == 'inline')
		document.getElementById(divName).style.display = 'none';
	else
		document.getElementById(divName).style.display = 'inline';
}

</script>
{/literal}

<h3>Anzahl der Bestellungen</h3>

	{foreach $num_orders as $num_order} <h4>{$num_order.name} hat {$num_order.number} Bestellungen:</h4>
		{foreach $num_order.user_groups as $group}
		<p style="margin-left: 10%">
			Gruppe <b>{$group.name}</b> hat <b>{$group.counter}</b> mal bestellt.
		</p>
		{/foreach}
	

	<br> {/foreach}


<a href="javascript:switchInfo('orderTable')"><h3>Bestell-Tabelle anzeigen</h3></a>
<div id="orderTable" style="display: none;">
	<table style="text-align: center; width: 100%">
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
</div>
{/block}