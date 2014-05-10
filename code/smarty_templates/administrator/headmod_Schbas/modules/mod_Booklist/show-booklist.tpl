{extends file=$inh_path}

{block name=html_snippets append}

<script type="text/template" id="booklist-row-template">
	<tr>
		<td><%= subject %></td>
		<td><%= gradelevel %></td>
		<td><%= author %></td>
		<td><%= title %></td>
		<td><%= publisher %></td>
		<td><%= isbn %></td>
		<td><%= price %></td>
		<td><%= bundle %></td>
		<td>LOOOOL</td>
		<td>
		<a class="btn btn-info btn-xs" href="index.php?section=Schbas|Booklist&action=2&ID=<%= id %>" title="Bucheinstellungen">
				<span class="icon icon-Settings"></span>
		</a>
		<a class="btn btn-danger btn-xs" href="index.php?section=Schbas|Booklist&action=3&ID=<%= id %>" title="Buch löschen">
			<span class="icon icon-error"></span>
		</a>
		</td>
	</tr>
</script>

<script type="text/template" id="paginator-template">
	<li class="disabled"><a class="first-page">&laquo;</a></li>
	<% for(var i = startPage; i <= pagecount && i < startPage + amountDisplayed; i++) { %>
		<li><a><%= i %></a></li>
	<% } %>
	<li class="disabled"><a class="last-page">&raquo;</a></li>
</script>

{/block}

{block name=filling_content}

<div class="row">
	<div class="center-block">
		<div class="col-sm-4 col-md-3 text-center">
			<span class="input-group filter-container"
				title="{t}Search (Enter to commit){/t}" >
				<span class="input-group-addon">
					<span class="icon icon-search"></span>
				</span>
				<input id="filter" type="text" class="form-control"
					placeholder="{t}Search...{/t}" />
			</span>
		</div>
		<div class="col-sm-8 col-md-6 text-center">
			<ul id="page-select" class="pagination">
			</ul>
		</div>
		<div class="col-sm-12 col-md-3 form-group">
				<div class="input-group books-per-page-container pull-right"
					title="{t}Rows per page{/t}" >
					<span class="input-group-addon">
						<span class="icon icon-Settings"></span>
					</span>
					<input id="books-per-page" type="text" maxlength="3" class="form-control"
						value="10" />
				</div>
		</div>
	</div>
</div>

<div>
	<table id="booklist" class="table table-hover table-striped table-responsive">
		<thead>
			<tr>
				<th>Fach</th>
				<th>Jahrgang</th>
				<th>Titel</th>
				<th>Autor</th>
				<th>Verlag</th>
				<th>ISBN</th>
				<th>Preis</th>
				<th>Bundle</th>
				<th>letzte Inventarnummer</th>
				<th>Optionen</th>
			</tr>
		</thead>
		<tbody>
		</tbody>
	</table>
</div>

{/block}


{block name=js_include append}

<script src="{$path_js}/administrator/Schbas/Booklist/show-booklist.js">
</script>

{/block}