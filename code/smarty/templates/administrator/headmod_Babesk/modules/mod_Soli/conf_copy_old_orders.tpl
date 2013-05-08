{extends file=$soliParent}{block name=content}
<p align="center">Wollen sie s&auml;mtlichen alten Bestellungen wirklich &uuml;bernehmen?</p>
<form align="center" action="index.php?section=Babesk|Soli&action=7" method="post">
	<input type="submit" value="Ja, Bestellungen &uuml;bernehmen" name="copy">
	<input type="submit" value="Nein, Bestellungen NICHT &uuml;bernehmen" name="dont_copy">
</form>
{/block}