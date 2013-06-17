{extends file=$inh_path} {block name='content'}


<h2 class="moduleHeader">Gruppeneinstellungen</h2>

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
	<button id="groupSubmit" style="">
		Gruppenänderungen ausführen
	</button>
</div>

<fieldset class="smallContainer groupRights">
	<legend>Rechte der Gruppe <span class="selectedGroupName"></span></legend>
	<div class="groupRights">
		<p>Noch nicht eingebaut!</p>
	</div>
</fieldset>

<script src="../include/js/jstree/jquery.jstree.js">
</script>
<script src="../smarty/templates/administrator/headmod_System/modules/
	mod_GroupSettings/main.js">
</script>

{/block}
