{extends file=$schbasSettingsParent}{block name=content}

<form action="index.php?section=Schbas|SchbasSettings&action=4"
	method="post">
	<legend>Allgemein</legend>
		<label>Kontoinhaber<input type="text" name="owner" maxlength="100" size=40></label>
		<br>
		<label>Kontonummer<input type="text" name="number" maxlength="100" size=40></label>
		<br>
		<label>Bankleitzahl<input type="text" name="blz" maxlength="100" size=40></label>
		<br>
		<label>Kreditinstitut<input type="text" name="institute" maxlength="100" size=40></label>
		<br> <br> 
	<input id="submit"type="submit" value="Submit" />
</form>

{/block}