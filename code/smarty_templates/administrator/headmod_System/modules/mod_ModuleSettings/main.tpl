{extends file=$inh_path} {block name='content'}
<h2 class="module-header">Moduleinstellungen</h2>

<fieldset class="smallContainer">
	<legend>Module</legend>
	<div class="moduletree"></div>
</fieldset>

<fieldset class="smallContainer">
	<legend>Details</legend>
	<div class="moduledetails simpleForm">
		<p>Kein Modul ausgewählt</p>
	</div>
</fieldset>

{/block}

{block name=js_include append}
<script src="{$path_js}/vendor/jstree/jquery.jstree.js"></script>
<script src="{$path_js}/administrator/System/ModuleSettings/main.js"></script>
{/block}