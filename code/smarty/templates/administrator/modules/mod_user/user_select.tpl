{extends file=$base_path}{block name=content}
<p>Bitte wählen sie aus, was sie tun möchten:</p><br>
<form action="index.php?section=user&action=1" method="post">
	<input type="submit" value="Einen Benutzer registrieren">
</form>
<form action="index.php?section=user&action=2" method="post">
	<input type="submit" value="Benutzer anzeigen">
</form>

{/block}