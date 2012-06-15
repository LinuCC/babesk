<h3>Neue Preisklasse erstellen</h3>

<p>Preisklassen geben den Preis für bestimmte Mahlzeitgruppen an. <br>
Zum Beispiel wenn ein teueres vegetarisches Menü angeboten wird,<br>
erstellen sie eine neue Preisklasse um die Preise für die Gruppen zu ändern.<br> 
Soli-Bestellungen erhalten ein separates Modul,<br>
sie brauchen nicht in Preisklassen mit einbezogen zu werden.</p>

<form action="index.php?module=Babesk&action=addPriceclass" method="post">
<label>Standard-Preis: <input type="text" name="n_price"></label>
<p style="font-size: small;">Der Standard-Preis gibt den Preis an, der für die Gruppen benutzt wird, deren Felder
sie leer lassen.</p><br><br>
<label>Name der Preisklasse: <input type="text" name="name"></label><br><br>
{foreach $groups as $group} 
	<b>======Gruppe: {$group.name}======</b><br> 
	<label>Preis für die Gruppe: <input type="text" name="group_price{$group.ID}" size="5"><br><br>
	</label> {/foreach}
	<input type="submit" value="Fortfahren" name="goOn">
	<input type="submit" value="Eine weitere Preisklasse hinzufügen" name="addAnother">
</form>