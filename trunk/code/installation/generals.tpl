<h3>Schritt 2</h3>
<p>Im ersten Schritt geben sie bitte den Namen der Schule sowie ein Passwort für den Systemadministratoren (admin) ein</p>
<legend>Bitte Name der Schule sowie Passwort für den Administrator eingeben</legend>
<form action="index.php?step=2" method="post">
    <fieldset>
        <legend>Allgemeines</legend>
        <label>Name der Schule</label>
            <input type="text" name="Schoolname" /><br />
        <label>Admin Passwort</label>
            <input type="password" name="Password[]" /><br />
        <label>Admin Passwort (wdh.)</label>
            <input type="password" name="Password[]" /><br />
    </fieldset>
    <input type="submit" value="Submit" />
</form>