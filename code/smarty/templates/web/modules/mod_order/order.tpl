

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

.div-info {
	background-color: #f8f187;
}

.div-info-hideall {
	display: inline;
	float: right;
}

.div-info-submit {
	display: inline;
	float: left;
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
				<a href="javascript:switchInfo('MealDiv{$meal.ID}')">{$meal.name}</a>
			</ul> {/foreach}
		</td>
		<td>{foreach $mealweek.2 as $meal}
			<ul>
				<a href="javascript:switchInfo('MealDiv{$meal.ID}')">{$meal.name}</a>
			</ul> {/foreach}
		</td>
		<td>{foreach $mealweek.3 as $meal}
			<ul>
				<a href="javascript:switchInfo('MealDiv{$meal.ID}')">{$meal.name}</a>
			</ul> {/foreach}
		</td>
		<td>{foreach $mealweek.4 as $meal}
			<ul>
				<a href="javascript:switchInfo('MealDiv{$meal.ID}')">{$meal.name}</a>
			</ul> {/foreach}
		</td>
		<td>{foreach $mealweek.5 as $meal}
			<ul>
				<a href="javascript:switchInfo('MealDiv{$meal.ID}')">{$meal.name}</a>
			</ul> {/foreach}
		</td>
	<tr>
</table>

{/foreach}

<!-- for every meal-element -->
{foreach $meallist as $mealweek} {foreach $mealweek as $mealday} {if
count($mealday)} {foreach $mealday as $meal} {if isset($meal.ID)}
<div class="div-info" id="MealDiv{$meal.ID}" style="display: none;">
	<fieldset class="div-info">
		<legend>
			<b>Informationen zu {$meal.name}:</b>
		</legend>
		{$meal.description}
		<p>
			<b>Preis:</b> {$meal.price} &euro;
		</p>
	</fieldset>
	<fieldset class="div-info">
		<form class="div-info-submit"
			action="index.php?section=order&order={$meal.ID}" method="post">
			<input type="submit" value="{$meal.name} Bestellen">
		</form>
	</fieldset>
</div>
{/if} {/foreach} {/if} {/foreach} {/foreach}

<p>
<hr>
{$infotext.0}
<hr>
</p>
<p>{$infotext.1}</p>
