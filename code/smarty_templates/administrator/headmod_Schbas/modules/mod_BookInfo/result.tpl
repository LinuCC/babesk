{extends file=$checkoutParent}{block name=content}
<h4>Kontoinformation</h4>
UserID: {$userID} {if $locked} <font color="red"><b>(gesperrt!)</b></font>{/if}<br>
Vorname: {$forename}<br>
Name: {$name}<br>
Klasse: {$class}
<hr>
<h4>Buchinformation</h4>
BuchID: {$bookID}<br>
Fach: {$subject}<br>
Klasse: {$class}<br>
Titel: {$title}<br>
Autor: {$author}<br>
Herausgeber: {$publisher}<br>
{/block}