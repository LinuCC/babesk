    <form action="index.php?section=register" method="post">
    <fieldset>
        <legend>Persönliche Daten</legend>
        <label for="forename">Vorname:</label>
        <input type="text" name="forename" /><br><br>
        <label for="name">Name:</label>
        <input type="text" name="name" /><br><br>
        <label for="username">Benutzername:</label>
        <input type="text" name="username" /><br><br>
        <label for="passwd">Passwort:</label>
        <input type="password" name="passwd" /><br><br>
		<label for="passwd">Passwort wiederholen:</label>
        <input type="password" name="passwd_repeat" /><br><br>
        Geburtstag :
        <label for="b_day">Tag:</label>
        <input type="int" name="b_day" size="2" maxlength="2" />
        <label for="b_month">Monat:</label>
        <input type="int" name="b_month" size="2" maxlength="2" />
        <label for="b_year">Jahr:</label>
        <input type="int" name="b_year" size="4" maxlength="4" />
    </fieldset>
    <br>
    <fieldset>
        <legend>Identitätsinformationen</legend><br><br>
        <select name="gid">
			{html_options values=$gid output=$g_names selected="1"}
		</select>
        <label for="credits">Guthaben:</label>
        <input type="int" name="credits" size="5" maxlength="5" />
    </fieldset><br>
    <input type="submit" value="Submit" />
  </form>
