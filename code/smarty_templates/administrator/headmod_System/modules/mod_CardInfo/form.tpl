{extends file=$checkoutParent}{block name=content}
<h3 class="module-header">Bitte Karte Scannen</h3>
<form action="index.php?section=System|CardInfo&{$sid}" method="post">
	<div class="form-group">
		<label for="card-id">ID</label>
			<input type="text" id="card-id" class="form-control" name="card_ID"
				size="10" maxlength="10" autofocus placeholder="Kartenid eingeben" />
	</div>
	<input type="submit" class="btn btn-primary" value="Senden" />
</form>
{/block}
