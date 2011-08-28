<h2 id="menu_header">Hauptmen&uuml;</h2>

{section name=module loop=$modules}
<div class="menu_item"><h4><a href="index.php?section={$modules[module]}&{$sid}">{$module_names[$modules[module]]}</a></h4></div>
{/section}
<div id="close"></div>
