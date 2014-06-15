{extends file=$UserParent}{block name=content}


<h2 class="moduleHeader">Benutzern automatisch Benutzernamen zuweisen</h2>

<p>Hier können sie die bisherigen Benutzernamen überschreiben und allen von
ihnen das Format "Vorname.Nachname" zuweisen (zum Beispiel Pascal.Ernst).<br>
<b>Alle Benutzernamen werden komplett und unwiederbringlich überschrieben!</b><br></p>

<form action="index.php?module=administrator|System|User|CreateUsernames" method="post">
	<input type="submit" name="confirmed" value="Bestätigen und alle Benutzernamen zurücksetzen">
</form>
{/block}
