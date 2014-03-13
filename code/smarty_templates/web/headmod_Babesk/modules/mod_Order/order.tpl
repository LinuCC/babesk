

{extends file=$inh_path}{block name=content}
<h2>
	<u>Speiseplan:</u>
</h2>

{literal}
<style type="text/css">
th {
	background-color: #84ff00;
	text-align: center;
}

td {
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

.notOrderable {
	color: #993333;
}

table {
	width: 100%;
}
</style>

{/literal}

{*
	Creates Hidden divs containing Meal-Information and a Order-Button
	That gets displayed when a meal in the Table is clicked
*}
{foreach $mealweeklist as $mealweek}
	{foreach $mealweek->weekdayDataGet() as $day}
		{foreach $day.meals as $meal}
			{if isset($meal->id)}
				<div class="div-info" id="MealDiv{$meal->id}" style="display: none;">
					<fieldset class="div-info">
						<legend>
							<b>Informationen zu {$meal->name}:</b>
						</legend>
						{$meal->description}
						<p>
							<b>Preis:</b> {$meal->price} &euro;
						</p>
					</fieldset>
					<fieldset class="div-info">
						<form class="div-info-submit"
							action="index.php?section=Babesk|Order&order={$meal->id}" method="post">
							<input type="submit" value='{$meal->name} bestellen'>
						</form>
					</fieldset>
				</div>
			{/if}
		{/foreach}
	{/foreach}
{/foreach}

{*
	Creates the Meal-Tables. Each Table represents a Mealweek
*}
{foreach $mealweeklist as $mealweek}
	<b>Woche {$mealweek->mealweeknumberGet()}</b>
	<table width="100%">
		<tr>
			<th>
				Preisklasse
			</th>
			{foreach $mealweek->weekdayDataGet() as $day}
				<th>
					{$day.dayname}<br />
					{date('d.m.Y', strtotime($day.date))}
				</th>
			{/foreach}
		</tr>
		{foreach $mealweek->priceclassesGet() as $pcId => $pcName}
		<tr>
			<td>
				{$pcName}
			</td>
			{foreach $mealweek->weekdayDataGet() as $day}
				<td>
					<ul>
					{foreach $mealweek->mealsByPriceclassAndDateGet(
						$pcId, $day.date) as $meal}
						<li>
							{$mealTs = strtotime($meal->date)}
							{$orderEnd = strtotime($orderEnddate, $mealTs)}
							{if $orderEnd >= time()}
								<a href="javascript:switchInfo('MealDiv{$meal->id}')">
									{$meal->name}
								</a>
							{else}
								<p class="notOrderable">
									{$meal->name}
								</p>
							{/if}
						</li>
					{foreachelse}
						<li>
							---
						</li>
					{/foreach}
					</ul>
				</td>
			{/foreach}
		</tr>
		{foreachelse}
		<tr>
			<td colspan="6">
				Keine Mahlzeiten in dieser Woche
			</td>
		</tr>
		{/foreach}
	</table>
{/foreach}

<p>
<hr>
{$infotext1}
<hr>
</p>
<p>{$infotext2}</p>
