{extends file=$schbasSettingsParent}{block name=content}

<form action="index.php?section=Schbas|SchbasSettings&action=editBankAccount"
	method="post">
	<legend>Bankverbindung der Schule:</legend>
		<label>Kontoinhaber<input type="text" name="owner" maxlength="100" size=40 value="{$owner}"></label>
		<br>
		<label>Kontonummer<input type="text" name="number" maxlength="100" size=40 value="{$number}"></label>
		<br>
		<label>Bankleitzahl<input type="text" name="blz" maxlength="100" size=40 value="{$blz}"></label>
		<br>
		<label>Kreditinstitut<input type="text" name="institute" maxlength="100" size=40 value="{$institute}"></label>
		<br> <br> 
	<input id="submit"type="submit" value="Speichern" />
</form>

{/block}