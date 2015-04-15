{extends file=$inh_path}{block name=content}
<h2>
	Speiseplan
</h2>

{literal}
<style type="text/css">

table ul {
	padding: 0;
	margin: 0;
}

table ul > li {
	list-style: none;
}

</style>

{/literal}

<div class="panel panel-default">
	<div class="panel-body">
		{*
			Creates Hidden divs containing Meal-Information and a Order-Button
			That gets displayed when a meal in the Table is clicked
		*}
		{foreach $mealweeklist as $mealweek}
			{foreach $mealweek->weekdayDataGet() as $day}
				{foreach $day.meals as $meal}
					{if isset($meal->id)}
						<div class="panel panel-primary" id="MealDiv{$meal->id}" style="display: none;">
							<div class="panel-heading">
								<div class="panel-title">
									Informationen zu {$meal->name}
								</div>
							</div>
							<div class="panel-body">
								{$meal->description}
								<p>
									<b>Preis:</b> {$meal->price} &euro;
								</p>
								<form class="div-info-submit"
									action="index.php?section=Babesk|Order&order={$meal->id}" method="post">
									<input class="btn btn-default" type="submit" value='{$meal->name} bestellen'>
								</form>
							</div>
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
			<table class="table table-responsive table-striped table-hover table-bordered">
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
										<a class="meal-link" data-meal-id="{$meal->id}">
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

		<div class="panel panel-info">
			<div class="panel-heading">
				Informationen
			</div>
			<div class="panel-body">
				<p>
					{$infotext1}
				</p>
			</div>
		</div>
		<div class="panel panel-warning">
			<div class="panel-heading">
				Kennzeichnungen
			</div>
			<div class="panel-body">
				<p>
					{$infotext2}
				</p>
			</div>
		</div>
	</div>
</div>

{/block}

{block name=js_include append}

<script type="text/javascript">

$(document).ready(function() {
	var displayed = false;
	$('a.meal-link').on('click', function(ev) {
		var mealId = $(ev.target).data('meal-id');
		if(displayed) {
			$(displayed).slideUp();
		}
		$('#MealDiv' + mealId).slideDown();
		displayed = '#MealDiv' + mealId;
	});
});

</script>

{/block}