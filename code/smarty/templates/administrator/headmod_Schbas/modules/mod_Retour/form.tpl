{extends file=$retourParent}{block name=content}
<h3>Bitte Karte scannen oder Benutzernamen eingeben</h3>
<form action="index.php?section=Schbas|Retour&{$sid}" method="post">
	<fieldset>

		<label>Karte oder Benutzername:</label>
			<input type="text" name="card_ID" size="20" maxlength="50"
				autofocus /><br />
	</fieldset>
	<input type="submit" value="Abschicken" />
</form>
{/block}
