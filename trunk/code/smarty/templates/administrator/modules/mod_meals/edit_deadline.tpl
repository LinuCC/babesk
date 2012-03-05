{extends file=$mealParent}{block name=content}
<center><h3>Uhrzeit f&uuml;r letzte Bestellm&ouml;glichkeit</h3></center>
<form action="index.php?section=meals&action=7"
	method="post" onsubmit="submit()">
	<fieldset>		
		{html_select_time use_24_hours=false display_seconds=false display_meridian=false time=$lastOrderTime} Uhr	
	</fieldset>
	<br> <input id="submit" onclick="submit()" type="submit" value="Speichern" />
</form>

{/block}