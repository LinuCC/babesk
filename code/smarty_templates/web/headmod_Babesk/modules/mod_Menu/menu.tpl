{extends file=$inh_path}{block name=content}

<div id="order">
    <h3><a href="index.php?section=Babesk|Order">Bestellen</a></h3>
</div>
<p><b>Bestellungen:</b></p>
{$error}
{foreach $meal as $meal2}
<p>{$meal2.date}: {$meal2.name}  {if $meal2.cancel}<a href="index.php?section=Babesk|Cancel&id={$meal2.orderID}">Abbestellen</a>{/if}</p>
{/foreach}
{/block}