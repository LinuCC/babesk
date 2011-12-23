    <form action="index.php?section=user&action=4&ID={$user.ID}" method="post">
    <fieldset>
        <legend>Persönliche Daten</legend>
        <label>ID des Users:<input type="text" name="id" maxlength="10" width="10" value={$user.ID}></label><br><br>
        <label>Vorname:<input type="text" name="forename" value="{$user.forename}"/></label><br><br>
        <label>Name:<input type="text" name="name" value="{$user.name}"/></label><br><br>
        <label>Benutzername:<input type="text" name="username" value="{$user.username}"/></label><br><br>
        <label>Passwort ändern:<input type="password" name="passwd" /></label><br><br>
        <label>Passwortänderung wiederholen:<input type="password" name="passwd_repeat"/></label><br><br>
        Geburtstag :
        {html_select_date time="{$user.birthday}" start_year="-100"}<br><br>
         <label>Konto sperren:<input type="checkbox" name="lockAccount" value="1" {if $user.locked}checked{/if}/></label>
    </fieldset>
    <br>
    <fieldset>
        <legend>Identitätsinformationen</legend><br><br>
        <select name="gid">
			{html_options values=$gid output=$g_names selected="{$user.GID}"}
		</select>
        <label>Guthaben:<input type="text" name="credits" size="5" maxlength="5" value="{$user.credit}"/></label>
    </fieldset><br>
    <input type="submit" value="Submit" />
  </form>
