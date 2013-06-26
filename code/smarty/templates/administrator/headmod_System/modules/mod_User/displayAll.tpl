{extends file=$UserParent}{block name=content}

<style type="text/css">

.columnsToShow, .filterRow, .sortRow {
	margin: 5px 0 5px 0;
}

</style>

  <script>
  $(function() {
    $( "#tabs" ).tabs().addClass( "ui-tabs-vertical ui-helper-clearfix" );
    $( "#tabs li" ).removeClass( "ui-corner-top" ).addClass( "ui-corner-left" );
  });
  </script>

<div id="tabs">
  <ul>
    <li><a href="#tabs-1">Ausgabeoptionen</a></li>
    <li><a href="#tabs-2">Benutzerliste</a></li>
  </ul>
  <div id="tabs-1">
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
</div>
  <div id="tabs-2">
<div class="pageSelect blueButtons">
</div>

<table class="users dataTable">
	<thead>
	</thead>
	<tbody>
	</tbody>
</table>
</div>
  </div>

<link rel="stylesheet" href="/resources/demos/style.css" />
<script src="../smarty/templates/administrator/headmod_System/modules/
	mod_User/displayAll.js">
</script>

{/block}
