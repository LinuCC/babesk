{extends file=$checkoutParent}{block name=content}

<style type="text/css">

a.nextOrder {
	padding: 10px 10px 15px 10px;
	margin-top: 10px;
	border-radius: 5px;
	border: 1px solid #006699;
}

</style>

<p>Bestellt:</p>
<ul>
{section name=meal_name loop=$meal_names}
	<li>
		{if strpos($meal_names[meal_name], 'Die Bestellung wurde schon abgeholt') === false}
			<h4>{$meal_names[meal_name]}</h4><br /><br />
		{else}
			<p class="error">{$meal_names[meal_name]}</p><br />
			<script type="text/javascript">
				adminInterface.errorShow('Die Bestellung wurde schon abgeholt!');
			</script>
		{/if}
	</li>
{/section}
</ul>
<a class="nextOrder" href="index.php?section=Babesk|Checkout&amp;{$sid}">
	weiter zur n&auml;chsten Bestellung
</a>
{/block}
