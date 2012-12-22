{literal}
<style type="text/css">
table {
	width: 100%;
}
th {
	width: 16,6%;
	background-color: #84ff00;
	text-align: center;
}

td {
	width: 16,6%;
	background-color: #f8f187;
	text-align: center;
}
</style>
{/literal}
<body>
<center><h2>Speiseplan</h2></center>
	<table >
		<thead>
			<tr>
				<th>Tag:</th>
				<th>Montag</th>
				<th>Dienstag</th>
				<th>Mittwoch</th>
				<th>Donnerstag</th>
				<th>Freitag</th>
			</tr>
		</thead>
		<tbody>
		<tr>
			<th>Datum:</th>
			{foreach $weekdate as $date name="daten"}
			{if $smarty.foreach.daten.iteration <= 5}
			<th>{$date}</th>
			{/if}
			{/foreach}
		</tr>
		{assign var=counter value=1}
		{foreach $meallistweeksorted as $meallist}
			<tr>
				<td>
				    {if isset($meallist.monday.priceclass)}{$meallist.monday.priceclass}
				{elseif isset($meallist.tuesday.priceclass)}{$meallist.tuesday.priceclass}
				{elseif isset($meallist.wednesday.priceclass)}{$meallist.wednesday.priceclass}
				{elseif isset($meallist.thursday.priceclass)}{$meallist.thursday.priceclass}
				{elseif isset($meallist.friday.priceclass)}{$meallist.friday.priceclass}
				{/if}
				</td>
				<td>{if isset($meallist.monday.title)}{$meallist.monday.title}<br>{$meallist.monday.description}{else}---{/if}</td>
				<td>{if isset($meallist.tuesday.title)}{$meallist.tuesday.title}<br>{$meallist.tuesday.description}{else}---{/if}</td>
				<td>{if isset($meallist.wednesday.title)}{$meallist.wednesday.title}<br>{$meallist.wednesday.description}{else}---{/if}</td>
				<td>{if isset($meallist.thursday.title)}{$meallist.thursday.title}<br>{$meallist.thursday.description}{else}---{/if}</td>
				<td>{if isset($meallist.friday.title)}{$meallist.friday.title}<br>{$meallist.friday.description}{else}---{/if}</td>
			</tr>
		{/foreach}
		
			<tr>
			<th>Datum:</th>
			{foreach $weekdate as $date name="daten"}
			{if $smarty.foreach.daten.iteration > 5}
			<th>{$date}</th>
			{/if}
			{/foreach}
		</tr>
		{foreach $meallistweeksorted as $meallist}
			<tr>
				<td>
				  {if isset($meallist.monday2.priceclass)}{$meallist.monday2.priceclass}
				{elseif isset($meallist.tuesday2.priceclass)}{$meallist.tuesday2.priceclass}
				{elseif isset($meallist.wednesday2.priceclass)}{$meallist.wednesday2.priceclass}
				{elseif isset($meallist.thursday2.priceclass)}{$meallist.thursday2.priceclass}
				{elseif isset($meallist.friday2.priceclass)}{$meallist.friday2.priceclass}
				{/if}
				</td>
				<td>{if isset($meallist.monday2.title)}{$meallist.monday2.title}<br>{$meallist.monday2.description}{else}---{/if}</td>
				<td>{if isset($meallist.tuesday2.title)}{$meallist.tuesday2.title}<br>{$meallist.tuesday2.description}{else}---{/if}</td>
				<td>{if isset($meallist.wednesday2.title)}{$meallist.wednesday2.title}<br>{$meallist.wednesday2.description}{else}---{/if}</td>
				<td>{if isset($meallist.thursday2.title)}{$meallist.thursday2.title}<br>{$meallist.thursday2.description}{else}---{/if}</td>
				<td>{if isset($meallist.friday2.title)}{$meallist.friday2.title}<br>{$meallist.friday2.description}{else}---{/if}</td>
			</tr>
			{$counter = $counter + 1}
		{/foreach}
		</tbody>
	
	</table>
	<hr>
	{$menu_text1}
	<hr>
{$menu_text2}
</body>