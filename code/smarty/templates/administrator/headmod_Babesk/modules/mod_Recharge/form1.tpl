{extends file=$base_path}{block name=content}
<h3>Bitte Karte Scannen</h3>
<form action="index.php?section=Babesk|Recharge&{$sid}" method="post">
	<fieldset>
		<label>Karten ID</label>
			<input type="text" name="card_ID" maxlength="10"/><br />
	</fieldset>
	<input type="submit" value="Submit" />
</form>

{/block}