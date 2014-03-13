{extends file=$inventoryParent}{block name=content}

<form action="index.php?section=Schbas|Inventory&action={$action['show_inventory']}" method="post">
	<input type="submit" value="Inventarliste">
</form><br>
<form action="index.php?section=Schbas|Inventory&action={$action['add_inventory']}" method="post">
	<input type="submit" value="Inventar hinzuf&uuml;gen">
</form><br>
<form action="index.php?section=Schbas|Inventory&action={$action['del_inventory']}" method="post">
	<input type="submit" value="Inventar mit Barcode l&ouml;schen">
</form><br>



{/block}