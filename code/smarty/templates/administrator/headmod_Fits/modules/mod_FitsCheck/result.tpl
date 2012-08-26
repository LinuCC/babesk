{extends file=$checkoutParent}{block name=content}
{if $has_Fits}
<div style="background-color:#00ff00;color:#ffffff;text-align:center;text-decoration:none">
	<align="center"><h2>PC-Erlaubnis ok!</h2></align>
	</div>
{else}
	<div style="background-color:#ff0000;color:#ffffff;text-align:center;text-decoration:blink">
	<align="center"><h2>Warnung: Keine PC-Erlaubnis!</h2></align>
	</div>
{/if}
{/block}