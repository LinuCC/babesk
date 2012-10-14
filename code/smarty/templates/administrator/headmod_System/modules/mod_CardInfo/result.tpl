{extends file=$checkoutParent}{block name=content}
<h4>Kontoinformation</h4>
Kartennummer: {$cardID} {if $locked} <font color="red"><b>(gesperrt!)</b></font>{/if}<br>
Vorname: {$forename}<br>
Name: {$name}<br>
Klasse: {$class}
{/block}