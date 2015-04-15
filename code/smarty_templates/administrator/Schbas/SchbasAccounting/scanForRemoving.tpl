{extends file=$checkoutParent}{block name=content}
<h3 class="module-header">Antrag l&ouml;schen</h3>
<form role="form" action="index.php?section=Schbas|SchbasAccounting&action=userRemoveByID" method="post">
	<div class="form-group">
	<label for="user-id">Benutzer-ID</label>
		<input id="user-id" class="form-control" type="text" name="UserID" size="10" maxlength="10" autofocus />
		<small>Enter drücken, wenn Barcode eingescannt ist.</small>
	</div>
	<input class="btn btn-primary" type="submit" value="Senden" />
</form>
{/block}