{extends file=$baseLayout} 
{block name="header"}
<h3>Einen Administrator hinzufügen</h3>
{/block}
{block name="main" append}
<form action="index.php?module=Kuwasys&action=addAdmin" method="post">
    <fieldset>
        <label>Admin Passwort</label>
            <input type="password" name="adminPassword" /><br />
        <label>Admin Passwort (wdh.)</label>
            <input type="password" name="adminPasswordRepeat" /><br />
    </fieldset>
    <input type="submit" value="Submit" />
</form>
{/block}