{extends file=$checkoutParent}{block name=content}
<h3>Antrag l&ouml;schen</h3>
		<form action="index.php?section=Schbas|SchbasAccounting&action=userRemoveByID" method="post">
	<fieldset>
		<legend>Benutzer-ID</legend>
		<label>ID</label>
			<input type="text" name="UserID" size="10" maxlength="10" autofocus /><br />
	</fieldset>
	<input type="submit" value="Senden" />
</form>
		<small>Enter drücken, wenn Barcode eingescannt ist.</small>
{/block}