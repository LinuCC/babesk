{extends file=$inventoryParent}{block name=content}

<form action="index.php?section=Schbas|Inventory&action={$action['show_inventory']}" method="post">
	<input type="submit" value="Inventarliste">
</form><br>

{/block}