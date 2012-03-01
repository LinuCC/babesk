bitte geben sie das Datum ein, vor welchem ältere Mahlzeiten gelöscht werden sollen:<br><br>
<form action="index.php?section=meals&amp;action=4" method="post">
	<label>Tag:<input type="text" name="day" maxlength="2" size="2" value={$today.day} /></label>
	<label>Monat:<input type="text" name="month" maxlength="2" value={$today.month} size="2" /></label>
	<label>Jahr:<input type="text" name="year" maxlength="4" value={$today.year} size="4" /></label><br>
	<input type="submit" value="Löschen" />
</form>
