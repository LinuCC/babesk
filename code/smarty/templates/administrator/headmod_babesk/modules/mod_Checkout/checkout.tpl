{extends file=$checkoutParent}{block name=content}
{section name=meal_name loop=$meal_names}
<h4>Bestellt: {$meal_names[meal_name]}</h4>
{/section}
<a href="index.php?section=babesk|Checkout&{$sid}">weiter zur n&auml;chsten Bestellung</a>
{/block}