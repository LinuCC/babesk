{extends file=$inh_path}

{block name=html_snippets}

<script type="text/template" id="rowTemplate">
	<tr>
		<td><%= username %></td>
		<td><%= grade %></td>
		<td><%= origStatusname %></td>
		<td class="other-requests">
			<% if(otherRequests.length) { %>
				<div class="list-group">
					<% for(var i = 0; i < otherRequests.length; i++) { %>
						<a class="list-group-item
							<% if(otherRequests[i].statusname == 'active') { %>
								list-group-item-success
							<% } else if(otherRequests[i].statusname == 'removed'){ %>
								list-group-item-danger
							<% } else if(otherRequests[i].statusname == 'waiting'){ %>
								list-group-item-warning
							<% } %>
						"
							href="index.php?module=administrator|Kuwasys|KuwasysUsers|AssignUsersToClasses|Classdetails&classId=<%= otherRequests[i].classId %>&categoryId=<%= otherRequests[i].categoryId %>">
							<%= otherRequests[i].label %>
						</a>
					<% } %>
				</div>
			<% } else { %>
			<% } %>
		</td>
		<td class="options">
			<div class="btn-group">
				<button data-toggle="modal" href="#moveStatusDialog"
					class="btn btn-xs btn-info moveStatus" userid="<%= userId %>"
					title="Status verändern">
					<span class="icon icon-edit"></span>
				</button>
				<button data-toggle="modal" href="#moveClassDialog"
					class="btn btn-xs btn-info moveClass" userid="<%= userId %>"
					title="Zu Kurs verschieben">
					<span class="icon icon-move"></span>
				</button>
			</div>
		</td>
	</tr>
</script>

{/block}


{block name=popup_dialogs append}

<!-- Dialog to add a new user to the class -->
<div class="modal fade" id="addUserDialog" tabindex="-1" role="dialog"
	aria-hidden="true">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
				<h4 class="modal-title">Benutzer hinzufügen</h4>
			</div>
			<div class="modal-body">
				<div class="input-group form-group" data-toggle="tooltip"
					title="Benutzername">
					<div class="input-group-addon">
						<span class="icon icon-user"></span>
					</div>
					<input type="text" name="username" id="inputUsername" class="form-control" placeholder="Benutzername">
				</div>
				<div class="input-group form-group" data-toggle="tooltip"
					title="Status">
					<span class="input-group-addon">
						<span class="icon icon-clipboard"></span>
					</span>
					<select name="status" id="inputStatus" class="form-control">
						<option value="active" >
							Aktiv
						</option>
						<option value="waiting" >
							Wartend
						</option>
						<option value="removed" >
							Nicht in diesem Kurs
						</option>
					</select>
				</div>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-default" data-dismiss="modal">
					Abbrechen
				</button>
				<button id="addUserDialogSubmit" type="button" class="btn btn-primary">
					Hinzufügen
				</button>
			</div>
		</div><!-- /.modal-content -->
	</div><!-- /.modal-dialog -->
</div><!-- /.modal -->

<!-- Dialog to change the status of the user -->
<div class="modal fade" id="moveStatusDialog" tabindex="-1" role="dialog"
	aria-hidden="true" userid="0">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
				<h4 class="modal-title">Neuen Status wählen</h4>
			</div>
			<div class="modal-body">
				<div class="input-group form-group" data-toggle="tooltip" title="Neuer Status">
					<span class="input-group-addon">
						<span class="icon icon-clipboard"></span>
					</span>
					<select name="status" id="inputStatus" class="form-control">
						<option value="active" >
							Aktiv
						</option>
						<option value="waiting" >
							Wartend
						</option>
						<option value="removed" >
							Nicht in diesem Kurs
						</option>
					</select>
				</div>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-default" data-dismiss="modal">
					Abbrechen
				</button>
				<button id="moveStatusDialogSubmit" type="button"
					class="btn btn-primary">
					Durchführen
				</button>
			</div>
		</div><!-- /.modal-content -->
	</div><!-- /.modal-dialog -->
</div><!-- /.modal -->

<!-- Dialog for moving the user to another class -->
<div class="modal fade" id="moveClassDialog" tabindex="-1" role="dialog"
	aria-hidden="true" userid="0" >
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
				<h4 class="modal-title">Benutzer zu Kurs verschieben</h4>
			</div>
			<div class="modal-body">
				<div class="input-group form-group" data-toggle="tooltip"
					title="Neuer Kurs">
					<span class="input-group-addon">
						<span class="icon icon-listelements"></span>
					</span>
					<select name="class" id="inputClass" class="form-control">
						{foreach $classes as $class}
							<option value='{ldelim}"classId":{$class.ID},"categoryId":{$class.categoryId}{rdelim}'>
								{$class.label} ({$class.categoryName})
							</option>
						{/foreach}
					</select>
				</div>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-default" data-dismiss="modal">
					Abbrechen
				</button>
				<button id="moveClassDialogSubmit" type="button"
					class="btn btn-primary">
					Übernehmen
				</button>
			</div>
		</div><!-- /.modal-content -->
	</div><!-- /.modal-dialog -->
</div><!-- /.modal -->

{/block}


{block name="content"}

<h3 class="module-header">
	{t}Classdetails of Class{/t} {$class.label}
	<span>({$class.categoryName})</span>
</h3>

<a class="btn btn-default pull-right" data-toggle="modal"
	href='#addUserDialog'>
	Benutzer hinzufügen
</a>

<h4><span class="user-count-active"></span>aktive Schüler</h4>

<table class="table table-striped table-hover table-responsive"
	id="activeUsers">
</table>

<h4><span class="user-count-waiting">0 </span>wartende Schüler</h4>

<table class="table table-striped table-hover table-responsive"
	id="waitingUsers">
</table>

<h4><span class="user-count-removed">0 </span>entfernte Schüler</h4>

<table class="table table-striped table-hover table-responsive"
	id="removedUsers">
</table>

<a class="btn btn-primary pull-right" href="index.php?module=administrator|Kuwasys|KuwasysUsers|AssignUsersToClasses|Overview">
	Zurück zu Kursübersichten
</a>

{/block}


{block name=style_include append}

<link rel="stylesheet" href="{$path_css}/administrator/Kuwasys/KuwasysUsers/AssignUsersToClasses/classdetails.css" type="text/css" />
<link rel="stylesheet" href="{$path_js}/jquery-ui-smoothness.css" type="text/css" />

{/block}


{block name=js_include append}

<script type="text/javascript">
	var classId = {$classId};
	var categoryId = {$categoryId};
</script>

<script src="{$path_js}/jquery-ui-1.10.4.only-autocomplete.min.js"> </script>
<script src="{$path_js}/administrator/Kuwasys/KuwasysUsers/AssignUsersToClasses/classdetails.js">
</script>

{/block}
