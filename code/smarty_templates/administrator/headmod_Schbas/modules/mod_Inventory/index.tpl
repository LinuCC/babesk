{extends file=$inventoryParent}{block name=content}

<h3 class="moduleHeader">Inventarmenü</h3>

<fieldset>
	<legend>Generell</legend>
	<ul class="submodulelinkList">
		<li>
			<a href="index.php?section=Schbas|Inventory&action={$action['show_inventory']}">Inventarliste</a>
		</li>
		<li>
			<a href="index.php?section=Schbas|Inventory&action={$action['add_inventory']}">Inventar hinzuf&uuml;gen</a>
		</li>
		<li>
			<a href="index.php?section=Schbas|Inventory&action={$action['del_inventory']}">Inventar mit Barcode l&ouml;schen</a>
		</li>
	</ul>
</fieldset>

{/block}