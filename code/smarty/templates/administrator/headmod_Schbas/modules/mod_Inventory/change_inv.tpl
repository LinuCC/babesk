{extends file=$inventoryParent}{block name=content}

<form action="index.php?section=Schbas|Inventory&action=2&ID={$invdata.id}"
	method="post">
	<fieldset>
		<legend>Buchdaten</legend>
		<label>ID des Exemplars:<input type="text" name="id" maxlength="10"
			width="10" value={$invdata.id}></label><br> <br> 
		<label>Fach: {$bookdata.subject}</label> <br> <br> 
		<label>Klasse: {$bookdata.class}</label><br> <br> 
		<label>Titel: {$bookdata.title}</label><br> <br> 
		<label>Autor: {$bookdata.author}</label><br> <br> 
		<label>Verlag: {$bookdata.publisher}</label><br> <br> 
		<label>ISBN: {$bookdata.isbn}</label><br> <br> 
		<label>Preis: {$bookdata.price}</label><br> <br> 
		<label>Kaufjahr: <input type="text" name="purchase" maxlength="10" value="{$invdata.year_of_purchase}"/></label><br> <br> 
		<label>Exemplarnummer:<input type="text" name="exemplar" maxlength="10" value="{$invdata.exemplar}"/>
		</label>
	</fieldset>
	<br>
	<br> <input id="submit" type="submit" value="Submit" />
</form>

{/block}