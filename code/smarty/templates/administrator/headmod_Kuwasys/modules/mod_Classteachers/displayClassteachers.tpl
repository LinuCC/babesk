{extends file=$inh_path} {block name='content'}

<h2 class='moduleHeader'>Die Kursleiter</h2>

<table>
	<thead>
		<tr bgcolor='#33CFF'>
			<th align='center'>ID</th>
			<th align='center'>Vorname</th>
			<th align='center'>Name</th>
			<th align='center'>Adresse</th>
			<th align='center'>Telefon</th>
			<th align='center'>Kurse leitend</th>
		</tr>
	</thead>
	<tbody>
		{foreach $classteachers as $classteacher}
		<tr bgcolor='#FFC33'>
			<td align="center">{$classteacher.ID}</td>
			<td align="center">{$classteacher.forename}</td>
			<td align="center">{$classteacher.name}</td>
			<td align="center">{$classteacher.address}</td>
			<td align="center">{$classteacher.telephone}</td>
			<td align="center">{$classteacher.classes}</td>
			</td>
			<td align="center" bgcolor='#FFD99'>
				<form action="index.php?module=administrator|Kuwasys|Classteachers|Change&amp;ID={$classteacher.ID}" method="post">
					<input type='submit' value='bearbeiten'>
				</form>
				<form action="index.php?module=administrator|Kuwasys|Classteachers|Delete&amp;ID={$classteacher.ID}" method="post">
					<input type='submit' value='löschen'>
				</form>
			</td>
		</tr>
		{/foreach}
	</tbody>
</table>

{/block}
