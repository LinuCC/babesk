<h3>Administrator hinzuf&uuml;gen</h3>
<p>Bitte den Namen des Administrators sowie ein Passwort und die Gruppe eingeben</p>
<form action="index.php?section=admin&action=1" method="post">
	<fieldset>
		<label>Benutzername:</label>
        <input type="text" name="adminname" /><br>
        <label>Passwort:</label>
        <input type="password" name="password" /><br>
    </fieldset>
    <fieldset><legend>Gruppe des Administrators:</legend>    
				{html_radios name="admin_groups" options=$admin_groups separator="<br>"}</label>
	</fieldset>
	<input type="submit" value="Submit" />
</form>