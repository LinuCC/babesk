{extends file=$checkoutParent}{block name=content}
<h4>Kontoinformation</h4>
Kartennummer: {$cardID} {if $locked} <font color="red"><b>(gesperrt!)</b></font>{/if}<br>
Vorname: {$forename}<br>
Name: {$name}<br>
Klasse: {$class}<br>
Benutzername: {$username}<br>
<form action="index.php?section=Gnissel|GCardInfo&lostcard={$cardID}" method="post">
<input type="submit" value="Als Verloren melden" />
{/block}