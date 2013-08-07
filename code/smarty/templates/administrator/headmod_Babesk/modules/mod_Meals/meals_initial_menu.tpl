{extends file=$mealParent}{block name=content}
<!-- the initial menu-->
<form action="index.php?section=Babesk|Meals&action=1" method="post">
	<input type="submit" value="Eine neue Mahlzeit erstellen" />
</form>
<br />
<form action="index.php?section=Babesk|Meals&action=2" method="post">
	<input type="submit" value="Die Mahlzeiten anzeigen" />
</form>
<br />
<form action="index.php?section=Babesk|Meals&action=3" method="post">
	<input type="submit" value="Die Bestellungen anzeigen" />
</form>
<br />
<form action="index.php?section=Babesk|Meals&action=4" method="post">
<input type="submit" value="Alte Mahlzeiten und Bestellungen löschen" />
</form>
<br />
<form action="index.php?section=Babesk|Meals&action=6" method="post">
<input type="submit" value="Infotexte f&uuml;r Speiseplan editieren" />
</form>
<br />
<form action="index.php?module=administrator|Babesk|Meals|MaxOrderAmount"
	method="post">
<input type="submit" value="Maximale Anzahl Bestellungen pro Tag setzen" />
</form>
{/block}
