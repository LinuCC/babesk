{extends file=$inventoryParent}{block name=content}
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
		</tr>
	{/foreach}
	</tbody>
</table>
{/block}