{extends file=$retourParent}{block name=content}
<h3 class="module-header">Retour</h3>
<h4>Bitte Karte scannen oder Benutzernamen eingeben</h4>
<form action="index.php?section=Schbas|Retour&amp;{$sid}" method="post"
	class="form-horizontal">
	<div class="form-group">
		<label class="col-sm-2 control-label" for="card_IDInput">Karte oder Benutzername:</label>
		<div class="col-sm-10">
			<input id="card_IDInput" class="form-control" type="text" name="card_ID" size="20" maxlength="50" autofocus />
		</div>
	</div>
	<input class="btn btn-primary" type="submit" value="Abschicken" />
</form>
{/block}
