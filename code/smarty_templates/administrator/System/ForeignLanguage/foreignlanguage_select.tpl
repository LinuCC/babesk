{extends file=$ForeignLanguageParent}{block name=content}
<p>Bitte wählen sie aus, was sie tun möchten:</p><br>
<form action="index.php?section=System|ForeignLanguage&action=1" method="post">
	<input type="submit" value="Fremdsprachen editieren">
</form>
<form action="index.php?section=System|ForeignLanguage&action=3" method="post">
	<input type="submit" value="Fremdsprachen den Benutzern zuordnen">
</form>
<form action="index.php?section=System|ForeignLanguage&action=5" method="post">
	<input type="submit" value="Fremdsprachen mit Inventarnummer(n) zuordnen">
</form>
{/block}