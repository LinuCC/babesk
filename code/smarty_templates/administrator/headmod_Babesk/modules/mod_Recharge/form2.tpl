{extends file=$base_path}{block name=content}
<h3 class="module-header">Bitte Betrag Eingeben</h3>
{if $isSoliRecharge}
<p class="alert alert-success">
	Der Benutzer hat ein gültiges Teilhabepaket
</p>
{else}
<p class="alert alert-warning">
	Der Benutzer hat <b>kein</b> gültiges Teilhabepaket
</p>
{/if}
<p class="alert alert-info">
	Der Benutzer kann maximal noch <b>{$max_amount}&euro;</b> aufladen!
</p>
<form action="index.php?module=administrator|Babesk|Recharge|RechargeCard" method="post">
	<div class="form-group">
		<label for="amount">Betrag</label>
		<input type="text" id="amount" class="form-control" name="amount"
			autofocus />
	</div>
	<input type="hidden" value="{$uid}" name="uid">
	<input type="submit" class="btn btn-default" value="Submit" />
</form>

{/block}
