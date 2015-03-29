{extends file=$inventoryParent}
{block name=content}
	<h3 class="module-header">Inventar hinzufügen</h3>
	<form action="index.php?section=Schbas|Inventory&action=4" method="post">
		<div class="form-group">
			<label>Büchercodes:</label>
			<textarea id="bookcodes" class="form-control" name="bookcodes"
				cols="50" rows="10">
			</textarea>
		</div>
		<input type="submit" class="btn btn-default" value="Abschicken" />
	</form>
{/block}