<h2>Wilkommen zur Installation des Fits!</h2>
<h3>Schritt 1</h3>
<legend>Bitte Datenbankverbindung eingeben!</legend>
<form action="index.php?module=Fits&action=dbSetup" method="post">
    <fieldset>
		<legend>MySQL-Database</legend>
		<label>Hostname (IP-Adresse/Domainname)</label>
			<input type="text" name="Host" /><br />
		<label>vorhandener MySQL-Username</label>
			<input type="text" name="Username" /><br />
		<label>vorhandenes MySQL-Password</label>
			<input type="password" name="Password" /><br />
		<label>vorhandener MySQLDatenbankname</label>
			<input type="text" name="Database" /><br />
	<input type="submit" value="Submit" />
	</fieldset>
</form>