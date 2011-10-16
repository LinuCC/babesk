<body>
	<table cellspacing='10' cellpadding='10'>
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
			{foreach $weekdate as $date}
			<th>{$date}</th>
			{/foreach}
		</tr>
		{assign var=counter value=1}
		{foreach $meallistweeksorted as $meallist}
			<tr>
				<td>Menü {$counter}</td>
				<td>{if isset($meallist.monday)}{$meallist.monday}{else}---{/if}</td>
				<td>{if isset($meallist.tuesday)}{$meallist.tuesday}{else}---{/if}</td>
				<td>{if isset($meallist.wednesday)}{$meallist.wednesday}{else}---{/if}</td>
				<td>{if isset($meallist.thursday)}{$meallist.thursday}{else}---{/if}</td>
				<td>{if isset($meallist.friday)}{$meallist.friday}{else}---{/if}</td>
			</tr>
			{$counter = $counter + 1}
		{/foreach}
		{$counter = 1}
		{foreach $meallistweeksorted_veg as $meallist}
			<tr>
				<td>Vegetarisches Menü {$counter}</td>
				<td>{if isset($meallist.monday)}{$meallist.monday}{else}---{/if}</td>
				<td>{if isset($meallist.tuesday)}{$meallist.tuesday}{else}---{/if}</td>
				<td>{if isset($meallist.wednesday)}{$meallist.wednesday}{else}---{/if}</td>
				<td>{if isset($meallist.thursday)}{$meallist.thursday}{else}---{/if}</td>
				<td>{if isset($meallist.friday)}{$meallist.friday}{else}---{/if}</td>
			</tr>
		{/foreach}
		</tbody>
	
	</table>
</body>