{extends file=$mealParent}{block name=content}

<h2 class="module-header">Maximale Bestellungsanzahl pro Tag</h2>

<form action="index.php?module=administrator|Babesk|Meals|MaxOrderAmount"
	method="post">

	<div class="simpleForm">
	<p class="inputItem">Max Bestellungen pro Tag:</p>
	<input class="inputItem" type="text" maxlength="2" size="2"
		name="maxOrderAmount" value="{$amount}"
		title="Die maximale Bestellungszahl gibt an, wieviel Bestellungen einer Mahlzeit jeder Benutzer pro Tag betätigen kann." />
	</div>

	<input type="submit" value="Verändern" />
</form>

{/block}
