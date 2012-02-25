{extends file=$base_path} {block name=content}
<h2 id="menu_header">Hauptmen&uuml;</h2>

{section name=module loop=$modules}
<div class="menu_item">
	<h4>
		<a href="index.php?section={$modules[module]}&{$sid}">{$module_names[$modules[module]]}</a>
	</h4>
</div>
{/section}
</div>

<div id="close"></div>
{/block}
{block name=link}<!-- We are already in Main Menu, dont want a linkto same site -->{/block}
