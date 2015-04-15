{extends file=$inventoryParent}{block name=content}
<table>
<tr><th align='center'>{$navbar}</th></tr>
</table>
<table class="table table-striped table-responsive table-hover">
	<thead>
		<tr>
			<th>ID</th>
			<th>Buchcode</th>
			<th>Optionen</th>
		</tr>
	</thead>
	<tbody>
	{foreach $bookcodes as $bookcode}
		<tr>
			<td>{$bookcode.id}</td>
			<td>{$bookcode.code}</td>
			<td>
				<form class="pull-left" action="index.php?section=Schbas|Inventory&action=2&ID={$bookcode.id}" method="post">
					<input type='submit' class="btn btn-default btn-xs" value='bearbeiten'>
				</form>
				<form action="index.php?section=Schbas|Inventory&action=3&ID={$bookcode.id}" method="post">
					<input type='submit' class="btn btn-danger btn-xs" value='löschen'>
				</form>
			</td>
		</tr>
	{/foreach}
	</tbody>
</table>
{/block}