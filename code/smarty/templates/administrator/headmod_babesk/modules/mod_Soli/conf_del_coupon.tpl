{extends file=$soliParent}{block name=content}
<p align="center">Wollen sie den Coupon vom Benutzer {$username} wirklich löschen?</p>
<form align="center" action="index.php?section=babesk|Soli&action=5&ID={$id}" method="post">
	<input type="submit" value="Ja, ich möchte den Coupon löschen" name="delete">
	<input type="submit" value="Nein, ich möchte den Coupon NICHT löschen" name="not_delete">
</form>
{/block}