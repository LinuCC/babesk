{extends file=$base_path}{block name=content}
<h3>Bitte Betrag Eingeben</h3>
<p>Der Benutzer kann maximal noch {$max_amount}&euro; aufladen!</p>
<form action="index.php?module=administrator|Babesk|Recharge|RechargeCard" method="post">
	<fieldset>
		<label>Betrag</label>
			<input type="text" name="amount" autofocus /><br />
	</fieldset>
	<input type="hidden" value="{$uid}" name="uid">
	<input type="submit" value="Submit" />
</form>

<script type="text/javascript">

var isSoliRecharge = {if $isSoliRecharge}true{else}false{/if};

{literal}
$(document).ready(function() {
	if(isSoliRecharge) {
		adminInterface.successShow(
			'Der Karteninhaber hat ein gültiges Teilhabepaket');
	}
	else {
		adminInterface.messageShow(
			'Der Karteninhaber hat <b>KEIN</b> gültiges Teilhabepaket');
	}
});
</script>
{/literal}

{/block}
