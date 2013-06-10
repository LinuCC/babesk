{extends file=$schbasSettingsParent}{block name=content}

<form action="index.php?section=System|User&action=4&ID={$user.ID}"
	method="post">
	<legend>Allgemein</legend>
		<label>Bankverbindung<input type="text" name="id" maxlength="100" size=40></label>
		<br> <br> 
	<input id="submit" onclick="submit()" type="submit" value="Submit" />
</form>

{/block}