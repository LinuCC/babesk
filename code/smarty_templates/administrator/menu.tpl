{extends file=$base_path} {block name=content}

<link rel="stylesheet"
	href="../smarty/templates/administrator/css/adminModuleMenu.css"
	type="text/css" />

<script src="../smarty/templates/administrator/moduleMenu.js"></script>

<!-- ACTUAL HTML -->

<h2 id="menu_header">Hauptmen&uuml;</h2>

<div class="clearfix HeadItemContainer">
	{foreach $headmodules as $headmodule}
		<div class="HeadItem" id="{$headmodule->getName()}">
			<a class="HeadItemText" href="#" tabindex="1">
				{$modulepath = $moduleGenMan->modulePathGet($headmodule)}
				{_g('modulepath_'|cat:$modulepath)}
			</a>
		</div>
	{/foreach}
</div>
<div class="moduleWrapper clearfix">

	<div id="ToolTip" style="text-align:center">
	<b>Bitte w&auml;hlen Sie ein Modul aus</b>
	</div>

	{foreach $headmodules as $headmodule}
		{foreach $headmodule->getChilds() as $module}
			<div class="menu_item" id="{$headmodule->getName()}|{$module->getName()}">
				<div class="menuItemCell">
					<a href="index.php?section={$headmodule->getName()}|{$module->getName()}" tabindex="1">
						{$modulepath = $moduleGenMan->modulePathGet($module)}
						{_g('modulepath_'|cat:$modulepath)}
					</a>
				</div>
			</div>
		{/foreach}
	{/foreach}
</div>

{/block}
{block name=link}<!-- We are already in Main Menu, dont want a linkto same site -->{/block}
