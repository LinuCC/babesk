{extends file=$booklistParent}
{block name=search}
<form action="index.php?section=Schbas|Booklist&action=2" method="post"><input type='text' name='isbn_search'><input type='submit' value='Mit ISBN suchen'></form>
{/block}
{block name=content}
<table>
	<thead>
		<tr bgcolor='#33CFF'>
			<th align='center'>Fach</th>
			<th align='center'>Jahrgang</th>
			<th align='center'>Titel</th>
			<th align='center'>Autor</th>
			<th align='center'>Verlag</th>
			<th align='center'>ISBN</th>
			<th align='center'>Preis</th>
			<th align='center'>Bundle</th>
		</tr>
	</thead>
	<tbody>
	{foreach $bookcodes as $bookcode}
		<tr bgcolor='#FFC33'>
			<td align="center">{$bookcode.subject}</td>
			<td align="center">{$bookcode.class}</td>
			<td align="center">{$bookcode.title}</td>
			<td align="center">{$bookcode.author}</td>
			<td align="center">{$bookcode.publisher}</td>
			<td align="center">{$bookcode.isbn}</td>
			<td align="center">{$bookcode.price}</td>
			<td align="center">{$bookcode.bundle}</td>
			<td align="center" bgcolor='#FFD99'>
			<form action="index.php?section=Schbas|Booklist&action=2&ID={$bookcode.id}" method="post"><input type='submit' value='bearbeiten'></form>
			<form action="index.php?section=Schbas|Booklist&action=3&ID={$bookcode.id}" method="post"><input type='submit' value='löschen'></form>
			</td>
		</tr>
	{/foreach}
	</tbody>
</table>
{/block}