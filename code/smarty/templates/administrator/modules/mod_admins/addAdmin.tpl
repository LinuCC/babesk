<h3>Administrator hinzuf&uuml;gen</h3>
<p>Bitte den Namen des Administrators sowie ein Passwort und die Gruppe eingeben</p>
<form action="index.php?section=admins&action=addAdmin&{$sid}" method="post">
	<fieldset>
		<label>Benutzername:</label>
        <input type="text" name="adminname" /><br><br>
        <label>Passwort:</label>
        <input type="password" name="password" /><br><br>
		<select name="group" size="1">
 			{section name=group loop=$admin_groups}
                <option>{$admin_groups[group]}</option>
			{/section}     
        </select>
        
	</fieldset>
	<input type="submit" value="Submit" />
</form>