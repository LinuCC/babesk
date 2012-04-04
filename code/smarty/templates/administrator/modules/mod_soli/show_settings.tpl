{extends file=$base_path}{block name=content}
<form action="index.php?section=soli&action=6" method="post">
	<label>Zu zahlender Preis:</label>
	<input name='soli_price' maxlength=5 size="5" value='{sprintf("%01.2f",$old_price)}' />€
	<input type="submit" value="Absenden" />
</form>
{/block}