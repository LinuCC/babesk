{extends file=$base_path}{block name=content}
<form action="index.php?section=soli&action=3" method="post">
	<label>Zu zahlender Preis:</label>
	<input name='soli_price' maxlength=1 value='{$old_price}' />
	<input type="submit" value="Absenden" />
</form>
{$old_price}
{/block}