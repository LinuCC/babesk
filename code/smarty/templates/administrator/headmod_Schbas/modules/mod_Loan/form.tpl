{extends file=$loanParent}{block name=content}
<h3>Bitte Karte Scannen</h3>
<form action="index.php?section=Schbas|Loan&{$sid}" method="post">
	<fieldset>
		<legend>Karte</legend>
		<label>ID</label>
			<input type="text" name="card_ID" size="10" maxlength="10"
				autofocus /><br />
	</fieldset>
	<input type="submit" value="Abschicken" />
</form>
{/block}
