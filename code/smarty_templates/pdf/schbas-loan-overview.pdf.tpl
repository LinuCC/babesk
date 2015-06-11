<h2 align="center">
	Übersicht der Leih- und Kaufexemplare
</h2>
<p align="center">
	Für {$user->getForename()} {$user->getName()} in {$schoolyear->getLabel()}
</p>
<p align="center">
	Erstellt am {$letterDate}
</p>

<div style="page-break-inside:avoid">
	<h4 align="center">
		Leihbücher
	</h4>
	<table cellpadding="0" cellspacing="1">
		<thead>
			<tr style="font-weight:bold; text-align:center;">
				<th width="40">Fach</th>
				<th width="270">Titel</th>
				<th>Verlag</th>
				<th>ISBN-Nr.</th>
				<th width="50">Preis</th>
			</tr>
		</thead>
		<tbody>
			{foreach $booksToLoan as $book}
			<tr>
				<td width="40">
					{$book->getSubject()->getName()}
				</td>
				<td width="270">
					{$book->getTitle()}
				</td>
				<td>
					{$book->getPublisher()}
				</td>
				<td>
					{$book->getIsbn()}
				</td>
				<td align="right" width="50">
					{number_format($book->getPrice(), 2)} €
				</td>
			</tr>
			{/foreach}
		</tbody>
	</table>
</div>

<div style="page-break-inside:avoid">
	<h4 align="center">
		Selbstkäufe
	</h4>
	<table cellpadding="0" cellspacing="1">
		<thead>
			<tr style="font-weight:bold; text-align:center;">
				<th width="40">Fach</th>
				<th width="270">Titel</th>
				<th>Verlag</th>
				<th>ISBN-Nr.</th>
				<th width="50">Preis</th>
			</tr>
		</thead>
		<tbody>
			{foreach $booksToBuy as $book}
			<tr>
				<td width="40">
					{$book->getSubject()->getName()}
				</td>
				<td width="270">
					{$book->getTitle()}
				</td>
				<td>
					{$book->getPublisher()}
				</td>
				<td>
					{$book->getIsbn()}
				</td>
				<td align="right" width="50">
					{number_format($book->getPrice(), 2)} €
				</td>
			</tr>
			{/foreach}
		</tbody>
	</table>
</div>

{* <div style="page-break-inside:avoid">
	<h4 align="center">
		Ausgeliehene Bücher
	</h4>
	<table cellpadding="0" cellspacing="1">
		<thead>
			<tr style="font-weight:bold; text-align:center;">
				<th width="40">Fach</th>
				<th width="270">Titel</th>
				<th>Verlag</th>
				<th>ISBN-Nr.</th>
				<th width="50">Preis</th>
			</tr>
		</thead>
		<tbody>
			{foreach $booksLend as $book}
			<tr>
				<td width="40">
					{$book->getSubject()->getName()}
				</td>
				<td width="270">
					{$book->getTitle()}
				</td>
				<td>
					{$book->getPublisher()}
				</td>
				<td>
					{$book->getIsbn()}
				</td>
				<td align="right" width="50">
					{number_format($book->getPrice(), 2)} €
				</td>
			</tr>
			{/foreach}
		</tbody>
	</table>
</div> *}