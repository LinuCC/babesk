<h3>Schritt 2</h3>
<p>Bitte geben sie ein Passwort für den Administrator von Babesk ein</p>
<form action="index.php?module=Babesk&action=tableValueSetup" method="post">
    <fieldset>
        <legend>Allgemeines</legend>
        <label>Admin Passwort</label>
            <input type="password" name="adminPassword" /><br />
        <label>Admin Passwort (wdh.)</label>
            <input type="password" name="adminPasswordRepeat" /><br />
    </fieldset>
    <input type="submit" value="Submit" />
</form>