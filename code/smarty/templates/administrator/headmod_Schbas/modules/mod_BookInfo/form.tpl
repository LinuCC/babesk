{extends file=$checkoutParent}{block name=content}
<h3>Bitte Buch Scannen</h3>
<form action="index.php?section=Schbas|BookInfo&{$sid}" method="post">
	<fieldset>
		<legend>Buch</legend>
		<label>ID</label>
			<input type="text" name="barcode" size="15" maxlength="20" /><br />
	</fieldset>
	<input type="submit" value="Senden" />
</form>
{/block}