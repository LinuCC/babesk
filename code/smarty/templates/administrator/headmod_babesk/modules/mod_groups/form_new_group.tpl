{extends file=$groupsParent}{block name=content}
<form action='index.php?section=groups&action=1' method='post'>
	<fieldset>
	<legend><b>Daten der Gruppe:</b></legend>
	<label>Name der Gruppe:<input type="text" name='groupname' size='20'/> </label><br>
	<label>Maximales Guthaben:<input type='text' name='max_credit' size='5'>Euro</label><br>
	</fieldset>
	
	<fieldset>
	<legend><b>Preisklassen:</b></legend>
	
	<label>Standard-Preis: <input type="text" name="n_price"></label>
	<p style="font-size: small;">Der Standard-Preis gibt den Preis an, der für die Gruppen benutzt wird, deren Felder
	sie leer lassen.</p><br><br>
	
	{foreach $priceclasses as $priceclass}
		<h4>Preis f&uuml;r {$priceclass.name}:</h4>
		<label><input type="text" name="pc_price{$priceclass.pc_ID}" size="5">Euro</label><br>
		<input type="hidden" name="pc_name{$priceclass.pc_ID}" value='{$priceclass.name}'>
	{/foreach}
	</fieldset>
	
	<input type="submit" value="Gruppe hinzufügen">
</form>
{/block}