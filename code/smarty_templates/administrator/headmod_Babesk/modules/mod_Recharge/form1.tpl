{extends file=$base_path}{block name=content}
<h3>Bitte Karte Scannen</h3>
<form action="index.php?module=administrator|Babesk|Recharge|RechargeCard"
	method="post">
	<fieldset>
		<label>Karten ID</label>
			<input type="text" name="card_ID" maxlength="10" autofocus /><br />
	</fieldset>
	<input type="submit" value="Submit" />
</form>

<script type="text/javascript">
$(document).ready(function() {
	$('input[name=card_ID]').focus();
});
</script>

{/block}
