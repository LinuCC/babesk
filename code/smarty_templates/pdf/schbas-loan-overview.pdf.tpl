<div style="page-break-inside:avoid">
	<h2 align="center">
		{$coverLetter->getTitle()}
	</h2>
	<p style="text-align: right;">
		{$letterDate}
	</p>
	{$coverLetter->getText()}
</div>

<div style="page-break-inside:avoid">
	<h2 align="center">
		Lehrbücher Jahrgang {$gradelevel}
	</h2>
	<table border="0" bordercolor="#FFFFFF" style="background-color:#FFFFFF" width="100%" cellpadding="0" cellspacing="1">
		<tr style="font-weight:bold; text-align:center;">
			<th>Fach</th>
			<th>Titel</th>
			<th>Verlag</th>
			<th>ISBN-Nr.</th>
			<th>Preis</th>
		</tr>
		{foreach $books as $book}
			<tr>
				<td>
					{$book->getSubject()->getName()}
				</td>
				<td>
					{$book->getTitle()}
				</td>
				<td>
					{$book->getPublisher()}
				</td>
				<td>
					{$book->getIsbn()}
				</td>
				<td align="right">
					{$book->getPrice()} €
				</td>
			</tr>
		{/foreach}
	</table>
	<br>
	{$textOne->getText()}
	<br><br>
	<table style="border:solid" width="75%" cellpadding="2" cellspacing="2">
		<tr>
			<td>Leihgebühr: </td>
			<td>{$feeNormal} Euro</td>
		</tr>
		<tr>
			<td>(3 und mehr schulpflichtige Kinder:</td>
			<td>{$feeReduced} Euro)</td>
		</tr>
		<tr>
			<td>Kontoinhaber:</td>
			<td>{$bankData[0]}</td>
		</tr>
		<tr>
			<td>Kontonummer:</td>
			<td>{$bankData[1]}</td>
		</tr>
		<tr>
			<td>Bankleitzahl:</td>
			<td>{$bankData[2]}</td>
		</tr>
		<tr>
			<td>Kreditinstitut:</td>
			<td>{$bankData[3]}</td>
		</tr>
	</table>
</div>

{if $textTwo->getText() || $textThree->getText()}
<div style="page-break-inside:avoid">
	<h2 align="center">
		Weitere Informationen
	</h2>
	{if $textTwo->getText()}
		<h3>
			{$textTwo->getTitle()}
		</h3>
		{$textTwo->getText()}
	{/if}
	{if $textThree->getText()}
		<h3>
			{$textThree->getTitle()}
		</h3>
		{$textThree->getText()}
	{/if}
</div>
{/if}