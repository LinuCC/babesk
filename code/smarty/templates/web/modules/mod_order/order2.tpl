{include file='web/header.tpl' title='Bestellen'}

<h2>
	<u>Speiseplan:</u>
</h2>

{literal}
<style type="text/css">
th {
	width: 20%;
	background-color: #84ff00;
	text-align: center;
}

td {
	width: 20%;
	background-color: #f8f187;
	text-align: center;
}

table {
	width: 100%;
}
</style>
{/literal} {foreach $meallist as $mealweek}
<table width="100%">
	<tr>
		<th>Montag<br>{$mealweek.date.1}
		</th>
		<th>Dienstag<br>{$mealweek.date.2}
		</th>
		<th>Mittwoch<br>{$mealweek.date.3}
		</th>
		<th>Donnerstag<br>{$mealweek.date.4}
		</th>
		<th>Freitag<br>{$mealweek.date.5}
		</th>
	</tr>
	<tr>
		<td>{foreach $mealweek.1 as $meal}
			<ul>
				<a href="index.php?section=order&order={$meal.ID}">{$meal.name}</a>
			</ul> {/foreach} {foreach $mealweek.2 as $meal}
			<ul>
				<a href="index.php?section=order&order={$meal.ID}">{$meal.name}</a>
			</ul> {/foreach} {foreach $mealweek.3 as $meal}
			<ul>
				<a href="index.php?section=order&order={$meal.ID}">{$meal.name}</a>
			</ul> {/foreach} {foreach $mealweek.4 as $meal}
			<ul>
				<a href="index.php?section=order&order={$meal.ID}">{$meal.name}</a>
			</ul> {/foreach} {foreach $mealweek.5 as $meal}
			<ul>
				<a href="index.php?section=order&order={$meal.ID}">{$meal.name}</a>
			</ul> {/foreach}
		<tr>
</td></table>


{/foreach}
