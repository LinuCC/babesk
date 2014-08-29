{extends file=$base_path}
{block name=html_snippets append}

<script type="text/template" id="user-table-template">
	<thead>
		<tr>
			<th>Vorname</th>
			<th>Nachname</th>
			<th>Benutzername</th>
			<th>Kartennummer</th>
		</tr>
	</thead>
	<tbody>
		<% for(var i = 0; i < users.length; i++) { %>
			<tr>
				<td> <%= users[i].forename %> </td>
				<td> <%= users[i].name %> </td>
				<td> <%= users[i].username %> </td>
				<td> <%= users[i].cardnumber %> </td>
			</tr>
		<% } %>
	</tbody>
</script>

{/block}


{block name=filling_content}

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