{extends file=$inh_path} {block name='content'}

<h2 class="module-header">Die wartenden Benutzer</h2>

<table class="dataTable">
	<tr>
		<th>Schülername</th>
		<th>Kursname</th>
		<th>Kursleiter</th>
		<th>Veranstaltungstag</th>
		<th>max. Anzahl der Teilnehmer</th>
		<th>Anzahl der Teilnehmer</th>
	</tr>
	{foreach $users as $user}
	<tr>
		<td rowspan="{count($user.classes)}">{$user.forename} {$user.name}</td>
		{$counter = 0}
		{foreach $user.classes as $class}
		{if $counter > 0}<tr>{/if}
		
		<td>{$class.label}</td>
		<td>{$user.classteachers.$counter.forename} {$user.classteachers.$counter.name} <br></td>
		<td>{$class.weekday}</td>
		<td>{$class.maxRegistration}</td>
		<td>{$class.activeParticipants}</td>
		{if $counter > 0}</tr>{/if}{$counter = $counter + 1}
		{/foreach}
	</tr>
	{/foreach}
</table>

{/block}