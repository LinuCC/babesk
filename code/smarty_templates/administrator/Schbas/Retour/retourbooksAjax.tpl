	<table class="table table-responsive table-striped table-hover">
		<thead>
			<tr>
				<th>Titel</th>
				<th>Author</th>
				<th>Publisher</th>
				<th>Inventarnummer</th>
			</tr>
		</thead>
		<tbody>
			{foreach $data as $retourbook}
				{$exemplar = $retourbook->getExemplars()->first()}
			<tr>
				<td>{$retourbook->getTitle()}</td>
				<td>{$retourbook->getAuthor()}</td>
				<td>{$retourbook->getPublisher()}</td>
				<td>
					{$retourbook->getSubject()->getAbbreviation()}
					{$exemplar->getYearOfPurchase()}
					{$retourbook->getClass()}
					{$retourbook->getBundle()}
					/
					{$exemplar->getExemplar()}
				</td>
			</tr>
			{/foreach}
		</tbody>
	</table>
