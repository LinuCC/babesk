{extends file=$UserParent}{block name=content}
<p>Bitte wählen sie aus, was sie tun möchten:</p><br>
<form action="index.php?section=System|User&action=1" method="post">
	<input type="submit" value="Einen Benutzer registrieren">
</form>
<form action="index.php?section=System|User&action=2" method="post">
	<input type="submit" value="Benutzer anzeigen">
</form>
<form action="index.php?section=System|User&action=5" method="post">
	<input type="submit" value="Benutzernamen automatisch zuweisen">
</form>

{/block}