{extends file=$inventoryParent}{block name=content}
<p align="center">Wollen Sie den Eintrag wirklich löschen?</p>
<form align="center" action="index.php?section=Schbas|Inventory&action=3&ID={$id}" method="post">
	<input type="submit" value="Ja, ich möchte den Eintrag löschen" name="delete">
	<input type="submit" value="Nein, ich möchte den Eintrag NICHT löschen" name="not_delete">
</form>
{/block}