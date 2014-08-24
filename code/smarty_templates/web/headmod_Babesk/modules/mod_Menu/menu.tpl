{extends file=$inh_path}{block name=content}

<div class="col-md-offset-2 col-md-8">
	<p><b>Bestellungen:</b></p>
	{if $error}<div class="alert alert-danger">{$error}</div>{/if}
	{foreach $meal as $meal2}
	<p>{$meal2.date}: {$meal2.name}  {if $meal2.cancel}<a href="index.php?section=Babesk|Cancel&id={$meal2.orderID}">Abbestellen</a>{/if}</p>
	{/foreach}

	<div id="order">
		<a class="btn btn-primary" href="index.php?section=Babesk|Order">Bestellen</a>
	</div>
</div>
{/block}