<h2>Benutzergruppen einrichten</h2>
<h3>Schritt 3</h3>
<p>In diesem Schritt richten sie die Benutzergruppen ein. Eine
	Benutzergruppe zeichnet sich dadurch aus, dass für sie eigene Preise
	und Maximale Guthaben gelten. Beispiele sind Lehrer und Schüler</p>
<legend>Bitte Daten für eine Gruppe angeben</legend>
<form action="index.php?step=3" method="post">
	<fieldset>
		<legend>Gruppe:</legend>
		<label>Gruppenname:</label> <input type="text" name="Name" /><br /> <label>Maximales
			Guthaben:</label> <input type="text" name="Max_Credit" />Euro<br />
	<input type="submit" name="add_another"
		value="weiteren Datensatz hinzufügen" /> <input type="submit"
		name="go_on" value="Fortfahren" />
	</fieldset>
</form>
