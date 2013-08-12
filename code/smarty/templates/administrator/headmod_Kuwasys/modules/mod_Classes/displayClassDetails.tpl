{extends file=$inh_path} {block name="content"}

<h2 class='moduleHeader'>Details des Kurses "{$class.label}"</h2>

{literal}
<style type='text/css' media='all'>
th {
	color: rgb(100, 100, 100);
	font-weight: bold;
	padding-right: 10px;
}

td {
	text-align: center;
}

</style>
{/literal}


<table>
	<tr>
		<th>ID:</th>
		<td>{$class.ID}</td>
	</tr>
	<tr>
		<th>Name:</th>
		<td>{$class.label}</td>
	</tr>
	<tr>
		<th>Beschreibung:</th>
		<td>{$class.description}</td>
	</tr>
	<tr>
		<th>Maximale Registrierungen:</th>
		<td>{$class.maxRegistration}</td>
	</tr>
	<tr>
		<th>Aktiv:</th>
		<td>{if
			isset($class.activeCount)}{$class.activeCount}{else}---{/if}</td>
	</tr>
	<tr>
		<th>Wartend:</th>
		<td>{if isset($class.waitingCount)} {$class.waitingCount}
			{else}---{/if}</td>
	</tr>
	<tr>
		<th>Wunsch:</th>
		<td>{if (isset($class.request1Count) || isset($class.request2Count))} {$class.request1Count + $class.request2Count}
			{else}---{/if}</td>
	</tr>
	<tr>
		<th>Schüler-Registrierungen erlaubt:</th>
		<td>{if $class.registrationEnabled}<b>Ja</b>{else}<b>Nein</b>{/if}
		</td>
	</tr>
	<tr>
		<th>Veranstaltungstag:</th>
		<td>{if
			$class.unitTranslatedName}{$class.unitTranslatedName}{else}---{/if}</td>
	</tr>
</table>

<table class="dataTable">
	<thead>
		<tr >
			<th colspan="6">Teilnehmer:</th>
		</tr>
		<tr>
			<th>Name:</th>
			<th>Art der Kurswahl:</th>
			<th>Klasse:</th>
			<th>Email-Adresse:</th>
			<th>Telefonnummer:</th>
			<th>Kurse desselben Tages:</th>
		</tr>
	</thead>
	<tbody>
		{if isset($users) && count($users)} {foreach $users as $user}
		<tr>
			{$rowsOfSamePerson = 1}
			<td rowspan="{$rowsOfSamePerson}">
			<!-- Link to UserDetails -->
			<a href="index.php?section=Kuwasys|Users&action=showUserDetails&ID={$user.ID}">
				{$user.forename} {$user.name}
			</a>
			</td>
			<td rowspan="{$rowsOfSamePerson}"
				{if $user.status == "active"}style="background-color: #99FF99"
				{else if $user.status == "waiting"}style="background-color: #FF9999"
				{/if}
				>
				<!-- Link to "move user to another Class" -->
				<a href="index.php?section=Kuwasys|Users&action=moveUserByClass&classIdOld={$class.ID}&userId={$user.ID}">
					{if $user.statusTranslated}
						{$user.statusTranslated}
					{else}Fehler!{/if}
				</a>
			</td>
			<td rowspan="{$rowsOfSamePerson}">{$user.gradename}</td>
			<td rowspan="{$rowsOfSamePerson}">{$user.email}</td>
			<td rowspan="{$rowsOfSamePerson}">{$user.telephone}</td>
						<td {if ($counter % 2)}style="background-color: #CC9933"{else}style="background-color: #DDAA33"{/if}>
				{foreach $user.classesOfSameDay as $cKey => $otherClass}
							<a href="index.php?module=administrator|Kuwasys|Classes|DisplayClassDetails&amp;ID={$otherClass.ID}">{$otherClass.label}</a>
							{*$blubb is used so the value of end($user.classesOfSameDay) does not get outputted*}
							{$blubb = end($user.classesOfSameDay)}
							{*check if it is not the last element*}
							{if $cKey ==! key($user.classesOfSameDay)}<hr />{/if}
				{/foreach}
						</td>
			<td rowspan="{$rowsOfSamePerson}"><a href="index.php?module=administrator|Kuwasys|Classes|UnregisterUser&amp;jointId={$user.jointId}">Abmelden</a></td>
		</tr>
		{/foreach}
		{/if}
	</tbody>
</table>
{/block}
