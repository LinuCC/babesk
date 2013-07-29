{extends file=$header} {block name=content}

<form class="simpleForm"
	action="index.php?module=administrator|Babesk|BabeskTimeSettings|Change"
	method="POST">

	<fieldset>
		<legend>Mahlzeitenanzeige</legend>

		<div class="simpleForm">
			<p class="inputItem">Anfang Mahlzeitenbestellung:</p>
			<input class="inputItem" name="displayMealsStartdate"
				type="text" value="{$displayMealsStartdate}" />
		</div>

		<div class="simpleForm">
			<p class="inputItem">Ende Mahlzeitenbestellung:</p>
			<input class="inputItem" name="displayMealsEnddate"
				type="text" value="{$displayMealsEnddate}" />
		</div>
	</fieldset>

	<fieldset>
		<legend>Bestellungen</legend>

		<div class="simpleForm">
			<p class="inputItem">Bestellungen erlaubt bis:</p>
			<input class="inputItem" name="orderEnddate"
				type="text" value="{$orderEnddate}" />
		</div>

		<div class="simpleForm">
			<p class="inputItem">Abbestellungen erlaubt bis:</p>
			<input class="inputItem" name="ordercancelEnddate"
				type="text" value="{$ordercancelEnddate}" />
		</div>
	</fieldset>

	<input type="submit" value="verändern" />
</form>

{/block}
