{extends file=$inventoryParent}{block name=content}
<table width=100%>
<tr><th align='center'>{$navbar}</th></tr>
</table>
<table>
	<thead>
		<tr bgcolor='#33CFF'>
			<th align='center'>ID</th>
			<th align='center'>Buchcode</th>
		</tr>
	</thead>
	<tbody>
	{foreach $bookcodes as $bookcode}
		<tr bgcolor='#FFC33'>
			<td align="center">{$bookcode.id}</td>
			<td align="center">{$bookcode.code}</td>
			<td align="center" bgcolor='#FFD99'>
			<form action="index.php?section=Schbas|Inventory&action=2&ID={$bookcode.id}" method="post"><input type='submit' value='bearbeiten'></form>
			<form action="index.php?section=Schbas|Inventory&action=3&ID={$bookcode.id}" method="post"><input type='submit' value='löschen'></form>
			</td>
		</tr>
	{/foreach}
	</tbody>
</table>
{/block}