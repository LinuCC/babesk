{extends file=$inh_path}

{block name=filling_content}

<div class="table-responsive">
	<table class="table table-hover table-striped">
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
					<td align="center" bgcolor='#FFD99'>
						<a class="btn btn-info btn-xs" href="index.php?section=Schbas|Booklist&action=2&ID={$bookcode.id}">bearbeiten</a>
						<a class="btn btn-danger btn-xs" href="index.php?section=Schbas|Booklist&action=3&ID={$bookcode.id}">löschen</a>
					</td>
				</tr>
			{/foreach}
		</tbody>
	</table>
</div>

{/block}