{extends file=$UserParent}{block name=content}

<script src="../smarty/templates/administrator/headmod_System/modules/
	mod_User/displayAll.js">
</script>

<style type="text/css">

.columnsToShow, .filterRow, .sortRow {
	margin: 5px 0 5px 0;
}

</style>

<a id="wacken" href="test.html">Download Me!</a>

<div class="accordion">

	<h3>Filter</h3>
	<div class="filter">
	</div>
	<h3>Sortierung</h3>
	<div class="sort">
	</div>
	<h3>Spaltenanzeige</h3>
	<div id="columnsToShowWrapper">
	</div>
	<h3>Einstellungen</h3>
	<div class="additionalSettings">
		<div>
			Benutzer pro Seite:
			<input id="usersPerPage" type="text" size="2" value="10" /></div>
		</div>
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



{/block}