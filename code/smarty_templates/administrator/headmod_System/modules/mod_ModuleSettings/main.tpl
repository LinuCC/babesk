{extends file=$inh_path} {block name='content'}
<h2 class="moduleHeader">Moduleinstellungen</h2>

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

<script src="{$path_js}/jstree/jquery.jstree.js"></script>
<script src="{$path_smarty_tpl}/administrator/headmod_System/modules/
	mod_ModuleSettings/main.js"></script>
{/block}
