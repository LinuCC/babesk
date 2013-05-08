{extends file=$inh_path} {block name='content'}

<h2 class='moduleHeader'>Die Klassen</h2>

<table>
	<thead>
		<tr bgcolor='#33CFF'>
			<th align='center'>ID</th>
			<th align='center'>Jahrgangsstufe</th>
			<th align='center'>Bezeichner</th>
			<th align='center'>Für Schuljahr</th>
			<th align='center'>Schultyp</th>
		</tr>
	</thead>
	<tbody>
		{foreach $grades as $grade}
		<tr bgcolor='#FFC33'>
			<td align="center">{$grade.ID}</td>
			<td align="center">{$grade.gradeValue}</td>
			<td align="center">{$grade.label}</td>
			<td align="center">{$grade.schoolyearLabel}</td>
			<td align="center">{$grade.schooltypeName}</td>
			<td align="center" bgcolor='#FFD99'>
			<form action="index.php?section=Kuwasys|Grade&action=changeGrade&ID={$grade.ID}" method="post"><input type='submit' value='bearbeiten'></form>
			<form action="index.php?section=Kuwasys|Grade&action=deleteGrade&ID={$grade.ID}" method="post"><input type='submit' value='löschen'></form>
			</td>
		</tr>
		{/foreach}
	</tbody>
</table>

{/block}