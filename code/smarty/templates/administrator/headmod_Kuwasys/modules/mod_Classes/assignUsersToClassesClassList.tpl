{extends file=$inh_path} {block name='content'}

<h2 class='moduleHeader'>Die zu ändernden Kurse</h2>

<table class="dataTable">
	<thead>
		<tr>
			<th align='center'>Kursname</th>
			<th align='center'>Anzahl Zuweisungen</th>
			<th align='center'>Wochentag</th>
		</tr>
	</thead>
	<tbody>
		{foreach $classes as $class}
		<tr>
			<td align="center">
				<a href="index.php?section=Kuwasys|Classes&amp;action=assignUsersToClasses&amp;showClassDetails={$class.classId}">{$class.classLabel}</a>
			</td>
			<td align="center">{$class.activeCount}</td>
			<td align="center">{$class.unitName}</td>
		</tr>
		{/foreach}
	</tbody>
</table>

{/block}