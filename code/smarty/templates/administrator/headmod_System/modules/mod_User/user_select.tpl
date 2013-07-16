{extends file=$UserParent}{block name=content}
<p>Bitte wählen sie aus, was sie tun möchten:</p><br>
<form action="index.php?module=administrator|System|User|Register" method="post">
	<input type="submit" value="Einen Benutzer registrieren">
</form>
<form action="index.php?module=administrator|System|User|DisplayAll" method="post">
	<input type="submit" value="Benutzer anzeigen">
</form>
<form action="index.php?module=administrator|System|User|CreateUsernames" method="post">
	<input type="submit" value="Benutzernamen automatisch zuweisen">
</form>
<form action="index.php?module=administrator|System|User|RemoveSpecialCharsFromUsernames" method="post">
	<input type="submit" value="Benutzernamen die speziellen Spezialcharakter entfernen">
</form>

{/block}
