{extends file=$soliParent}{block name=content}
<p align="center">Wollen sie s&auml;mtlichen alten Bestellungen wirklich &uuml;bernehmen?</p>
<form align="center" action="index.php?section=Babesk|Soli&action=7" method="post">
	<input type="submit" value="Ja, ich möchte den Coupon löschen" name="copy">
	<input type="submit" value="Nein, ich möchte den Coupon NICHT löschen" name="dont_copy">
</form>
{/block}