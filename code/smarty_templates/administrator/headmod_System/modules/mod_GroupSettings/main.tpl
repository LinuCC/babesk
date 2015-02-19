{extends file=$inh_path} {block name='content'}

<h2 class="module-header">Gruppeneinstellungen</h2>

<div class="treeButtons">
	<button id="groupAdd">Gruppe hinzufügen</button>
	<button id="groupRename">Gruppe umbenennen</button>
	<button id="groupRemove">Gruppe löschen</button>
</div>

<fieldset class="smallContainer">
	<legend>Gruppen</legend>
	<div class="groupTree">
	</div>
</fieldset>

<div>
	<button id="groupChangeRights">Rechte abrufen / verändern</button>
</div>

<fieldset class="smallContainer grouprights">
	<legend>Rechte der Gruppe <span class="selectedGroupName"></span></legend>
	<div class="grouprights">
	</div>
</fieldset>

<style type="text/css">

.jstree .modNotAllowed {
	color: #8f0d0b;
	background-color: #ffE0Df;
}

.jstree .modAllowed {
	color: #25530f;
	background-color: #F0ffE6;
}

.jstree .changeNotAllowed {
	color: #777777;
	font-style: italic;
}

</style>

{/block}

{block name="js_include" append}
<script src="{$path_js}/jstree/jquery.jstree.js">
</script>
<script src="{$path_js}/administrator/System/GroupSettings/righttree.js">
</script>
<script src="{$path_js}/administrator/System/GroupSettings/grouptree.js">
</script>
<script src="{$path_js}/administrator/System/GroupSettings/main.js">
</script>
{/block}