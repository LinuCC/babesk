<h3>Neue Preisklasse erstellen</h3>
<form action="index.php?section=priceclass&action=5" method="post">
<label>Standard-Preis: <input type="text" name="n_price"></label>
<p style="font-size: small;">Der Standard-Preis gibt den Preis an, der für die Gruppen benutzt wird, deren Felder
sie leer lassen.</p><br><br>
<label>Name der Preisklasse: <input type="text" name="name"></label><br><br>
{foreach $groups as $group} 
	<b>======Gruppe: {$group.name}======</b><br> 
	<label>Preis für die Gruppe: <input type="text" name="group_price{$group.ID}" size="5"><br><br>
	</label> {/foreach}
	<input type="submit" name="Hinzufügen">
</form>
