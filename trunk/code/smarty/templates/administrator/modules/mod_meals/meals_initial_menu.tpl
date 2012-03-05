	{extends file=$mealParent}{block name=content}
	<!-- the initial menu-->
	<form action="index.php?section=meals&action=1" method="post">
		<input type="submit" value="Eine neue Mahlzeit erstellen" />
	</form>
	<br><br>	
	<form action="index.php?section=meals&action=2" method="post">
		<input type="submit" value="Die Mahlzeiten anzeigen" />
	</form>
	<br><br>
	<form action="index.php?section=meals&action=3" method="post">
		<input type="submit" value="Die Bestellungen anzeigen" />
	</form>
	<br><br>
	<form action="index.php?section=meals&action=4" method="post">
	<input type="submit" value="Alte Mahlzeiten und Bestellungen löschen" />
	</form>
	<br><br>
	<form action="index.php?section=meals&action=6" method="post">
	<input type="submit" value="Infotexte f&uuml;r Speiseplan editieren" />
	</form>
	<br><br>
	<form action="index.php?section=meals&action=7" method="post">
	<input type="submit" value="Uhrzeit f&uuml;r letzte Bestellm&ouml;glichkeit" />
	</form>
	{/block}