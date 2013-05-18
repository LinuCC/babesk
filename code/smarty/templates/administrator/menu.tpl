{extends file=$base_path} {block name=content}

<link rel="stylesheet"
	href="../smarty/templates/administrator/css/adminModuleMenu.css"
	type="text/css" />

<script src="../smarty/templates/administrator/moduleMenu.js"></script>
<style type='text/css'></style>

<!-- ACTUAL HTML -->

<h2 id="menu_header">Hauptmen&uuml;</h2>

<div class="clearfix HeadItemContainer">
	{foreach $head_modules as $headmod}
		<div class="HeadItem" id="{$headmod.name}">
			<a class="HeadItemText" href="#">{$headmod.display_name}</a>
		</div>
	{/foreach}
</div>
<div class="moduleWrapper clearfix">

	<div id="ToolTip" style="text-align:center">
	<b>Bitte w&auml;hlen Sie ein Modul aus</b>
	</div>

	{section name=module loop=$modules}
	<div class="menu_item" id="{$modules[module]}">
		<div class="menuItemCell">
			<a href="index.php?section={$modules[module]}&{$sid}">{$module_names[$modules[module]]}</a>
		</div>
	</div>
	{/section}
</div>

{/block}
{block name=link}<!-- We are already in Main Menu, dont want a linkto same site -->{/block}
