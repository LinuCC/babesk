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
		{foreach $classTeachers as $classTeacher}
		<tr bgcolor='#FFC33'>
			<td align="center">{$classTeacher.ID}</td>
			<td align="center">{$classTeacher.forename}</td>
			<td align="center">{$classTeacher.name}</td>
			<td align="center">{$classTeacher.address}</td>
			<td align="center">{$classTeacher.telephone}</td>
			<td align="center">	{if isset($classTeacher.classLabel) && count($classTeacher.classLabel)}
									{foreach $classTeacher.classLabel as $classLabel}
										{$classLabel}<br>
									{/foreach}
								{/if}
			</td>
			<td align="center" bgcolor='#FFD99'>
			<form action="index.php?section=Kuwasys|ClassTeacher&action=changeClassTeacher&ID={$classTeacher.ID}" method="post"><input type='submit' value='bearbeiten'></form>
			<form action="index.php?section=Kuwasys|ClassTeacher&action=deleteClassTeacher&ID={$classTeacher.ID}" method="post"><input type='submit' value='löschen'></form>
			</td>
		</tr>
		{/foreach}
	</tbody>
</table>

{/block}