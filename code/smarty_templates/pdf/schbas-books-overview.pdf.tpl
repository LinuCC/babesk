<i align="center">{$date}</i><br>
<table border="1" cellpadding="5">
	<thead>
	</thead>
	<tbody>
		{foreach $usersWithBooks as $unit}
			<tr>
				<td width="200">
					{$unit.user->getForename()} {$unit.user->getName()}
				</td>
				<td width="430">
					{foreach $unit.books as $book}
						&middot; {$book->getTitle()}<br>
					{/foreach}
				</td>
			</tr>
		{/foreach}
	</tbody>
</table>