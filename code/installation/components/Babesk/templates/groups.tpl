<h2>Benutzergruppen einrichten</h2>
<h3>Schritt 3</h3>
<p>In diesem Schritt richten sie die Benutzergruppen ein. Eine
	Benutzergruppe zeichnet sich dadurch aus, dass für sie eigene Preise
	und Maximale Guthaben gelten. Beispiele sind Lehrer und Schüler</p>
<legend>Bitte Daten für eine Gruppe angeben</legend>
<form action="index.php?module=Babesk&action=addGroup" method="post">
	<fieldset>
		<legend>Gruppe:</legend>
		<label>Gruppenname:</label> <input type="text" name="name" /><br /> <label>Maximales
			Guthaben:</label> <input type="text" name="maxCredit" />Euro<br />
	<input type="submit" name="addAnother" value="weiteren Datensatz hinzufügen" />
	<input type="submit" name="goOn" value="Fortfahren" />
	</fieldset>
</form>
