{extends file=$UserParent}{block name=content}

<style type="text/css">

.columnsToShow, .filterRow, .sortRow {
	margin: 5px 0 5px 0;
}

</style>

<div class="accordion">

	<h3>Spaltenanzeige</h3>
	<div id="columnsToShowWrapper">
	</div>
	<h3>Filter</h3>
	<div class="filter">
	</div>
	<h3>Sortierung</h3>
	<div class="sort">
	</div>
	<h3>Einstellungen</h3>
	<div class="additionalSettings">
		<div>
			Benutzer pro Seite:
			<input id="usersPerPage" type="text" size="2" value="10" />
		</div>
		<div>
			<input id="refreshPage" type="button" size="2"
				value="Tabelle aktualisieren" />
		</div>
	</div>
	<h3>Abschieds-PDF-Dateien der gelöschten Nutzer</h3>
	<div class="deletedUserPdf">
		<p class="noUsersDeleted">keine Benutzer gelöscht</p>
	</div>
</div>

<div class="pageSelect blueButtons">
</div>

<table class="users dataTable">
	<thead>
	</thead>
	<tbody>
	</tbody>
</table>

<link rel="stylesheet" href="/resources/demos/style.css" />
<script src="../smarty/templates/administrator/headmod_System/modules/
	mod_User/displayAll.js">
</script>

{/block}
