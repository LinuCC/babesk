    <form action="index.php?section=user&action=3" method="post">
    <fieldset>
        <legend>Persönliche Daten</legend>
        <label><input type="text" name="id" maxlength="10" width="10" value={$user.ID}>ID des Users:</label>
        <label><input type="text" name="forename" value={$user.forename}/>Vorname:</label><br><br>
        <label><input type="text" name="name" value={$user.name}/>Name:</label><br><br>
        <label><input type="text" name="username" value={$user.username}/>Benutzername:</label><br><br>
        <label><input type="password" name="passwd" />Passwort ändern:</label><br><br>
        <label><input type="password" name="passwd_repeat"/>Passwortänderung wiederholen:</label><br><br>
        Geburtstag :
        {html_select_date start_year="-100"}
    </fieldset>
    <br>
    <fieldset>
        <legend>Identitätsinformationen</legend><br><br>
        <select name="gid">
			{html_options values=$gid output=$g_names selected="1"}
		</select>
        <label><input type="int" name="credits" size="5" maxlength="5" value={$user.credit}/>Guthaben:</label>
    </fieldset><br>
    <input type="submit" value="Submit" />
  </form>
