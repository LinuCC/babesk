{extends file=$inventoryParent}{block name=content}

<form action="index.php?section=Schbas|Inventory&action={$action['show_inventory']}" method="post">
	<input type="submit" value="Inventarliste">
</form><br>
<form action="index.php?section=Schbas|Inventory&action={$action['add_inventory']}" method="post">
	<input type="submit" value="Inventar Hinzuf&uuml;gen">
</form><br>




{/block}