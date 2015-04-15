{extends file=$loanParent}{block name=content}
<h3 class="module-header">Schulbuchausleihe</h3>
<h3>Bitte Karte Scannen</h3>
<form action="index.php?section=Schbas|Loan&amp;{$sid}" method="post"
	class="form-horizontal">
	<fieldset>
		<legend>Karte</legend>
		<div class="form-group">
			<label class="control-label col-sm-2" for="card_IDInput">ID</label>
				<div class="col-sm-10">
					<input class="form-control" id="card_IDInput" type="text" name="card_ID" size="10" maxlength="10" autofocus />
				</div>
		</div>
	</fieldset>
	<input class="btn btn-primary" type="submit" value="Abschicken" />
</form>
{/block}
