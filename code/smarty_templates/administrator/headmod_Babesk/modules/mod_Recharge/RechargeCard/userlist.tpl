{extends file=$base_path}
{block name=html_snippets append}

<script type="text/template" id="user-table-template">
	<thead>
		<tr>
			<th>Kartennummer</th>
			<th>Vorname</th>
			<th>Nachname</th>
			<th>Benutzername</th>
			<th>Guthaben</th>
			<th>Selektor</th>
		</tr>
	</thead>
	<tbody>
		<% for(var i = 0; i < users.length; i++) { %>
			<tr data-user-id="<%= users[i].id %>" tabindex="1">
				<td class="cardnumber"> <%= users[i].cardnumber %> </td>
				<td class="forename"> <%= users[i].forename %> </td>
				<td class="name"> <%= users[i].name %> </td>
				<td class="username"> <%= users[i].username %> </td>
				<td class="credits"> <%= users[i].credit.toFixed(2) %> € </td>
				<td class="selector" data-selector="<%= (i + 1) % 10 %>">
					<a class="btn btn-default btn-xs">Shift + <%= (i + 1) % 10 %></a>
				</td>
			</tr>
		<% } %>
	</tbody>
</script>

{/block}


{block name=popup_dialogs append}

<div class="modal fade" id="credits-change-modal" aria-hidden="true"
	tabindex="-1" role="dialog">
	<div class="modal-dialog">
		<div class="modal-content">
			<form id="credits-change-form">
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal"
						aria-hidden="true">&times;</button>
					<h4 class="modal-title">Guthaben verändern</h4>
				</div>
				<div class="modal-body">
					<label for="credits-change-input">
						Neues Guthaben von <span class="username"></span>:
					</label>
					<div class="input-group">
						<div class="input-group-addon">
							<span class="icon icon-euro"></span>
						</div>
						<input type="text" id="credits-change-input" class="form-control"
							placeholder="Guthaben eingeben..." />
						<div class="input-group-addon">
							Vorher: <span class="credits-before"></span>
						</div>
					</div>
				</div>
				<div class="modal-footer">
					<button type="button" class="btn btn-default" data-dismiss="modal">
						Abbrechen
					</button>
					<input type="submit" class="btn btn-primary apply" value="Speichern">
				</div>
			</form>
		</div>
	</div>
</div>

{/block}


{block name=filling_content}

<div class="alert alert-info">
	<button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
	<strong>Benutzerauswahl</strong> Sie können die Benutzer, nachdem sie sie gesucht haben, entweder in der Tabelle anklicken, oder mit Shift und einer Zahl (zB Shift + 1) einen Benutzer anwählen. Dies funktioniert nur mit der Zahlenreihe über den Buchstaben.
</div>

<div class="row">
	<div class="center-block">
		<div class="col-sm-12 col-md-6 col-lg-5 text-center">
			<span class="input-group filter-container">
				<input id="filter" type="text" class="form-control"
					placeholder="Suchen (Benutzername oder Kartennummer)"
					title="{t}Search (Enter to commit){/t}" autofocus />
				<span class="input-group-btn">
					<button id="search-submit" class="btn btn-default">
						<span class="icon icon-search"></span>
					</button>
				</span>
			</span>
		</div>
		<div class="col-sm-12 col-md-6 col-lg-5 col-lg-offset-2">
			<div id="page-select" class="pull-right"></div>
		</div>
	</div>
</div>

<div>
	<table id="user-table" class="table table-striped table-responsive table-hover">
	</table>
</div>

{/block}


{block name=js_include append}

<script type="text/javascript"
	src="{$path_js}/paginator/jquery.bootpag.min.js">
</script>

<script type="text/javascript"
	src="{$path_js}/administrator/Babesk/Recharge/RechargeCard/userlist.js">
</script>

{/block}