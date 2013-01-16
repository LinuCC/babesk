{extends file=$inh_path} {block name='content'}

<h2 class='moduleHeader'>Änderungsdetails von {$classname}</h2>

{if $dataPrimary}
<table class="dataTable">
	<thead>
		<th colspan='5'>
				Primärwünsche / Erstwünsche
			</th>
		<tr>
			<th align='center'>Schülername</th>
			<th align='center'>Klassenname</th>
			<th align='center'>Wochentag</th>
			<th align='center'> </th>
			<th align='center'> </th>
		</tr>
	</thead>
	<tbody>
		{foreach $dataPrimary as $row}
		<tr>
			<td align="center">{$row.username}</td>
			<td align="center">{$row.classLabel}</td>
			<td align="center">{$row.unitName}</td>
			<td align="center">
				<a href="index.php?section=Kuwasys|Classes&amp;action=assignUsersToClasses&amp;showClassDetails={$row.classId}&amp;toStatus=waiting&amp;id={$row.id}">Zu wartend</a>
			</td>
			<td align="center">
				<a href="index.php?section=Kuwasys|Classes&amp;action=assignUsersToClasses&amp;showClassDetails={$row.classId}&amp;toStatus=removed&amp;id={$row.id}">völlig entfernen</a>
			</td>
		</tr>
		{/foreach}
	</tbody>
</table>
{else}
<p>Keine Erstwünsche für diesen Kurs vorhanden.</p>
{/if}

<br /><br />

{if $dataSecondary}
<table class="dataTable">
	<thead>
		<tr>
			<th colspan='5'>
				Sekundärwünsche / Zweitwünsche
			</th>
		</tr>
		<tr>
			<th align='center'>Schülername</th>
			<th align='center'>Klassenname</th>
			<th align='center'>Wochentag</th>
			<th align='center'> </th>
			<th align='center'> </th>
		</tr>
	</thead>
	<tbody>
		{foreach $dataSecondary as $row}
		<tr>
			<td align="center">{$row.username}</td>
			<td align="center">{$row.classLabel}</td>
			<td align="center">{$row.unitName}</td>
			<td align="center">
				<a href="index.php?section=Kuwasys|Classes&amp;action=assignUsersToClasses&amp;showClassDetails={$row.classId}&amp;toStatus=active&amp;id={$row.id}">Zu aktiv</a>
			</td>
			<td align="center">
				<a href="index.php?section=Kuwasys|Classes&amp;action=assignUsersToClasses&amp;showClassDetails={$row.classId}&amp;toStatus=removed&amp;id={$row.id}">völlig entfernen</a>
			</td>
		</tr>
		{/foreach}
	</tbody>
</table>
{else}
<p>Keine Zweitwünsche für diesen Kurs vorhanden.</p>
{/if}
<br /><br />

{if $dataRemoved}
<table class="dataTable">
	<thead>
		<tr>
			<th colspan='5'>
				entfernte Wünsche
			</th>
		</tr>
		<tr>
			<th align='center'>Schülername</th>
			<th align='center'>Klassenname</th>
			<th align='center'>Wochentag</th>
			<th align='center'> </th>
			<th align='center'> </th>
		</tr>
	</thead>
	<tbody>
		{foreach $dataRemoved as $row}
		<tr>
			<td align="center">{$row.username}</td>
			<td align="center">{$row.classLabel}</td>
			<td align="center">{$row.unitName}</td>
			<td align="center">
				<a href="index.php?section=Kuwasys|Classes&amp;action=assignUsersToClasses&amp;showClassDetails={$row.classId}&amp;toStatus=active&amp;id={$row.id}">Zu aktiv</a>
			</td>
			<td align="center">
				<a href="index.php?section=Kuwasys|Classes&amp;action=assignUsersToClasses&amp;showClassDetails={$row.classId}&amp;toStatus=waiting&amp;id={$row.id}">Zu wartend</a>
			</td>
		</tr>
		{/foreach}
	</tbody>
</table>
{else}
<p>Keine entfernten Einträge für diesen Kurs vorhanden.</p>
{/if}
<br />
<a href="index.php?section=Kuwasys|Classes&amp;action=assignUsersToClasses&amp;showClasses=true">zurück</a>
{/block}