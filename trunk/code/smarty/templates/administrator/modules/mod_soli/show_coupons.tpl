{extends file=$soliParent}{block name=content}
<table cellpadding='10' cellspacing='10'>
	<thead> 
		<tr> 
			<th align="center">ID</th>
			<th align="center">Benutzername</th>
			<th align="center">Startdatum</th>
			<th align="center">Enddatum</th>
		</tr>
	</thead>
	
	<tbody>
	{foreach $coupons as $coupon}
		<tr>
			<td align="center">{$coupon['ID']}</td>
			<td align="center">{$coupon['username']}</td>
			<td align="center">{$coupon['startdate']}</td>
			<td align="center">{$coupon['enddate']}</td>
			<td align="center"><form action='index.php?section=soli&action=5&ID={$coupon["ID"]}' method='post'>
				<input type="submit" value='löschen'>
			</form></td>
		</tr>
	{/foreach}
	</tbody>

</table>
{/block}