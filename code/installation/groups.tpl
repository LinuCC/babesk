<h2>Benutzergruppen einrichten</h2>
<h3>Schritt 3</h3>
<p>In diesem Schritt richten sie die Benutzergruppen ein. Eine Benutzergruppe zeichnet sich dadurch aus, dass für sie eigene Preise und Maximale Guthaben gelten. Beispiele sind Lehrer und Schüler</p>
<legend>Bitte Daten für eine Gruppe angeben</legend>
<form action="index.php?step=3" method="post">
	<fieldset>
	<legend>Gruppe</legend>
	<label>Gruppenname</label>
		<input type="text" name="Name" /><br />
	<label>Maximales Guthaben</label>
		<input type="text" name="Max_Credit" /><br />
	</fieldset>
	<input type="submit" value="weiteren Datensatz hinzufügen" />
</form>
<!-- <form action="index.php?&amp;<?php echo htmlspecialchars(SID); ?>">
<input type="submit" value="Weiter zum nächsten Schritt">
</form> -->
<a href="index.php?step=3">Weiter<a>