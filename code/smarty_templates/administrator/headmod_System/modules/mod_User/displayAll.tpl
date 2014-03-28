{extends file=$UserParent}{block name=filling_content}

<div class="row">
	<div class="col-sm-2 col-md-2 text-center">
		<a class="btn btn-default">{t}Columns{/t}</a>
	</div>
	<div class="col-sm-7 col-md-8 text-center">
		<ul class="pagination">
			<li><a>&laquo;</a></li>
			<li><a>1</a></li>
			<li><a>2</a></li>
			<li><a>3</a></li>
			<li><a>4</a></li>
			<li><a>5</a></li>
			<li><a>6</a></li>
			<li><a>7</a></li>
			<li><a>8</a></li>
			<li><a>9</a></li>
			<li><a>&raquo;</a></li>
		</ul>
	</div>
	<div class="col-sm-3 col-md-2 form-group">
		<div class="input-group elements-count" title="{t}Elements per page{/t}" >
		<span class="input-group-addon">
			<span class="icon icon-Settings"></span>
		</span>
		<input type="text" maxlength="3" class="form-control" />
		</div>
	</div>
</div>

<div class="">
<table class="table">
	<tr>
		<th>#</th>
		<th>Vorname</th>
		<th>Nachname</th>
	</tr>
	<tr>
		<td>5</td>
		<td>Pascal</td>
		<td>Ernst</td>
	</tr>
</table>
</div>
{/block}

{block name=style_include append}
<link rel="stylesheet" href="{$path_css}/administrator/System/User/display-all.css" type="text/css" />
{/block}

{block name=content}

<style type="text/css">

.columnsToShow, .filterRow, .sortRow {
	margin: 5px 0 5px 0;
}

</style>

<div class="tabs centered">
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

<link rel="stylesheet" href="{$path_js}/jstree/themes/apple/style.css" />

{/block}

{block name="js_include" append}
<script src="{$path_smarty_tpl}/administrator/headmod_System/modules/
	mod_User/displayAll.js">
</script>
{/block}
