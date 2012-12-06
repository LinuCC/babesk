{extends file=$booklistParent}
{block name=search}
<form action="index.php?section=Schbas|Booklist&action=2" method="post"><input type='text' name='isbn_search'><input type='submit' value='Mit ISBN suchen'></form>
{/block}
{block name=content}
<table width=100%>
<tr><th align='center'>{$navbar}</th></tr>
</table>
<table>
	<thead>
		<tr bgcolor='#33CFF'>
			<form name="filterFach" action="index.php?section=Schbas|Booklist&action=1" method="post"><input type="hidden" name="filter" value="subject"><th align='center'><a href="#" onclick="document.filterFach.submit();">Fach</a></th></form>
			<form name="filterJahrgang" action="index.php?section=Schbas|Booklist&action=1" method="post"><input type="hidden" name="filter" value="class"><th align='center'><a href="#" onclick="document.filterJahrgang.submit();">Jahrgang</a></th></form>
			<form name="filterTitel" action="index.php?section=Schbas|Booklist&action=1" method="post"><input type="hidden" name="filter" value="title"><th align='center'><a href="#" onclick="document.filterTitel.submit();">Titel</a></th></form>
			<form name="filterAutor" action="index.php?section=Schbas|Booklist&action=1" method="post"><input type="hidden" name="filter" value="author"><th align='center'><a href="#" onclick="document.filterAutor.submit();">Autor</a></th></form>
			<form name="filterVerlag" action="index.php?section=Schbas|Booklist&action=1" method="post"><input type="hidden" name="filter" value="publisher"><th align='center'><a href="#" onclick="document.filterVerlag.submit();">Verlag</a></th></form>
			<form name="filterISBN" action="index.php?section=Schbas|Booklist&action=1" method="post"><input type="hidden" name="filter" value="isbn"><th align='center'><a href="#" onclick="document.filterISBN.submit();">ISBN</a></th></form>
			<th align='center'>Preis</th>
			<form name="filterBundle" action="index.php?section=Schbas|Booklist&action=1" method="post"><input type="hidden" name="filter" value="bundle"><th align='center'><a href="#" onclick="document.filterBundle.submit();">Bundle</a></th></form>
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