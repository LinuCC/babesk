geben sie bitte das Datum ein, wofür sie die bisher eingegangenen Bestellungen angezeigt haben möchten:<br>

<form action="index.php?section=meals&amp;action=3" method="post">
	<label>Tag:<input type="text" name="ordering_day" maxlength="2" size="2" value={$today.day} /></label>
	<label>Monat:<input type="text" name="ordering_month" maxlength="2" value={$today.month} size="2" /></label>
	<label>Jahr:<input type="text" name="ordering_year" maxlength="4" value={$today.year} size="4" /></label><br>
	<input type="submit" value="Anzeigen" />
</form>
