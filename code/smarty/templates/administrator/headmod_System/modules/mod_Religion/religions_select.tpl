{extends file=$ReligionParent}{block name=content}
<p>Bitte wählen sie aus, was sie tun möchten:</p><br>
<form action="index.php?section=System|Religion&action=1" method="post">
	<input type="submit" value="Konfessionen editieren">
</form>
<form action="index.php?section=System|Religion&action=3" method="post">
	<input type="submit" value="Konfessionen den Benutzern zuordnen">
</form>

{/block}