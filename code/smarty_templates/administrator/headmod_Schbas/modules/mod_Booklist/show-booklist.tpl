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
		<a class="btn btn-info btn-xs" href="index.php?section=Schbas|Booklist&action=2&ID=<%= id %>">bearbeiten</a>
			<a class="btn btn-danger btn-xs" href="index.php?section=Schbas|Booklist&action=3&ID=<%= id %>">löschen</a>
		</td>
	</tr>


</script>

{/block}

{block name=filling_content}

<a class="btn btn-danger">button</a>

<div class="table-responsive">
	<table id="booklist" class="table table-hover table-striped">
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
			{foreach $booksPaginator as $book}
				<tr>
					<td>
						{if $book->getSubject()} {$book->getSubject()->getName()} {/if}
					</td>
					<td>{$book->getClass()}</td>
					<td>{$book->getTitle()}</td>
					<td>{$book->getAuthor()}</td>
					<td>{$book->getPublisher()}</td>
					<td>{$book->getIsbn()}</td>
					<td>{$book->getPrice()|string_format:"%.2f"}</td>
					<td>{$book->getBundle()}</td>
					<td></td>
					<td>
						<a class="btn btn-info btn-xs" href="index.php?section=Schbas|Booklist&action=2&ID={$bookcode.id}">bearbeiten</a>
						<a class="btn btn-danger btn-xs" href="index.php?section=Schbas|Booklist&action=3&ID={$bookcode.id}">löschen</a>
					</td>
				</tr>
			{/foreach}
		</tbody>
	</table>
</div>

{/block}


{block name=js_include append}

<script src="{$path_js}/administrator/Schbas/Booklist/show-booklist.js">
</script>

{/block}