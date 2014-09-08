{extends file=$base_path}{block name=content}
<h3 class="module-header">Bitte Karte Scannen</h3>
<form action="index.php?module=administrator|Babesk|Recharge|RechargeCard"
	method="post">
	<div class="form-group">
		<label for="card-id">Karten ID</label>
		<input type="text" id="card-id" class="form-control" name="card_ID"
			maxlength="10" autofocus />
	</div>
	<input type="submit" class="btn btn-default" value="Einen Betrag aufladen" />
</form>

<script type="text/javascript">
$(document).ready(function() {
	$('input[name=card_ID]').focus();
});
</script>

{/block}
