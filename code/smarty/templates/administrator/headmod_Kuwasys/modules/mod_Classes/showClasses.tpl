{extends file=$inh_path} {block name='content'}

<h2 class='moduleHeader'>Die Kurse</h2>

<table>
	<thead>
		<tr bgcolor='#33CFF'>
			<th align='center'>ID</th>
			<th align='center'>Name</th>
			<th align='center'>Registrierungen</th>
			<th align='center'>Maximale Registrierungen</th>
			<th align='center'>Schuljahr</th>
		</tr>
	</thead>
	<tbody>
		{foreach $classes as $class}
		<tr bgcolor='#FFC33'>
			<td align="center">{$class.ID}</td>
			<td align="center">{$class.label}</td>
			<td align="center">Noch nicht implementiert!</td>
			<td align="center">{$class.maxRegistration}</td>
			<td align="center">{$class.schoolYearLabel}</td>
			<td align="center" bgcolor='#FFD99'>
			<form action="index.php?section=Kuwasys|Classes&action=changeClass&ID={$class.ID}" method="post"><input type='submit' value='bearbeiten'></form>
			<form action="index.php?section=Kuwasys|Classes&action=deleteClass&ID={$class.ID}" method="post"><input type='submit' value='löschen'></form>
			</td>
		</tr>
		{/foreach}
	</tbody>
</table>

{/block}