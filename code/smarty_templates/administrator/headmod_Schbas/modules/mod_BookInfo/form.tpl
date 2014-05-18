{extends file=$checkoutParent}{block name=content}
<h3 class="moduleHeader">Buchinformationen</h3>
<h3>Bitte Buch Scannen</h3>
<form class="form-horizontal" action="index.php?section=Schbas|BookInfo&amp;{$sid}" method="post">
	<fieldset>
		<legend>Buch</legend>
		<div class="form-group">
			<label for="barcode-input" class="col-sm-2 control-label">ID</label>
				<div class="col-sm-10">
					<input id="barcode-input" class="form-control" type="text" name="barcode" size="20" maxlength="30" />
				</div>
		</div>
	</fieldset>
	<input class="btn btn-primary" type="submit" value="Senden" />
</form>
{/block}