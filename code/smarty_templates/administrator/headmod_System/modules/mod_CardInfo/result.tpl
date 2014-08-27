{extends file=$checkoutParent}{block name=content}
<h3 class="module-header">Kontoinformation</h3>
Kartennummer: {$cardID} {if $locked} <font color="red"><b>(gesperrt!)</b></font>{/if}<br>
Vorname: {$forename}<br>
Name: {$name}<br>
Klasse: {$class}
{/block}