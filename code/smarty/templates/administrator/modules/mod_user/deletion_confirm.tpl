<p align="center">Wollen sie den Benutzer {$forename} {$name} wirklich löschen?</p>
<form align="center" action="index.php?section=user&action=3&ID={$uid}" method="post">
	<input type="submit" value="Ja, ich möchte den Benutzer löschen" name="delete">
	<input type="submit" value="Nein, ich möchte den Benutzer NICHT löschen" name="not_delete">
</form>