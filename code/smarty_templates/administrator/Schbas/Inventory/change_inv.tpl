{extends file=$inventoryParent}{block name=content}

<form action="index.php?section=Schbas|Inventory&action=2&ID={$invdata.id}"
	method="post">
	<fieldset>
		<legend>Buchdaten</legend>
		<label>ID des Exemplars: {$invdata.id}</label><br> <br>
		<label>Fach: {$bookdata.subject}</label> <br> <br>
		<label>Klasse: {$bookdata.class}</label><br> <br>
		<label>Titel: {$bookdata.title}</label><br> <br>
		<label>Autor: {$bookdata.author}</label><br> <br>
		<label>Verlag: {$bookdata.publisher}</label><br> <br>
		<label>ISBN: {$bookdata.isbn}</label><br> <br>
		<label>Preis: {$bookdata.price}</label><br> <br>
		<div class="form-group">
			<label for="year-of-purchase">Kaufjahr: </label>
			<input type="text" id="year-of-purchase" class="form-control" name="purchase" maxlength="10" value="{$invdata.year_of_purchase}"/>
		</div>
		<div class="form-group">
			<label for="exemplar">Exemplarnummer:</label>
			<input type="text" id="exemplar" class="form-control"
				name="exemplar" maxlength="10" value="{$invdata.exemplar}"/>
		</div>
	</fieldset>
	<input id="submit" class="btn btn-primary" type="submit" value="Speichern" />
</form>

{/block}