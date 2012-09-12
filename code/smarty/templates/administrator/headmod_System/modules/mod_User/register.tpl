{extends file=$UserParent}{block name=content}
    <form action="index.php?section=System|User&action=1" method="post">
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
        {html_select_date start_year="-100"}
    </fieldset>
    <br>
    <fieldset>
        <legend>Identitätsinformationen</legend><br>
       <label for="class">Klasse:</label>
        <input type="text" name="class" /><br><br>
        <select name="gid">
			{html_options values=$gid output=$g_names selected="1"}
		</select>
        <label for="credits">Guthaben:</label>
        <input type="int" name="credits" size="5" maxlength="5" />
    </fieldset><br>
    <input type="submit" value="Submit" />
  </form>

  {/block}