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
			isset($class.sumStatus.active)}{$class.sumStatus.active}{else}---{/if}</td>
	</tr>
	<tr>
		<th>Wartend:</th>
		<td>{if isset($class.sumStatus.waiting)} {$class.sumStatus.waiting}
			{else}---{/if}</td>
	</tr>
	<tr>
		<th>Wunsch:</th>
		<td>{if (isset($class.sumStatus.request1) || isset($class.sumStatus.request2))} {$class.sumStatus.request1 + $class.sumStatus.request2}
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
			$class.weekdayTranslated}{$class.weekdayTranslated}{else}---{/if}</td>
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
		{if isset($class.users) && count($class.users)} {foreach $class.users as $user}
		<tr>
			{$rowsOfSamePerson = count($user.classesOfSameDay)}
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
					{$user.statusTranslated}
				</a>
			</td>
			<td rowspan="{$rowsOfSamePerson}">{$user.gradeName}</td>
			<td rowspan="{$rowsOfSamePerson}">{$user.email}</td>
			<td rowspan="{$rowsOfSamePerson}">{$user.telephone}</td>
				{$counter = 0}
				{foreach $user.classesOfSameDay as $otherClass}
					{if $otherClass.ID != $class.ID}
						{if $counter}<tr>{/if}
						<td {if ($counter % 2)}style="background-color: #CC9933"{else}style="background-color: #DDAA33"{/if}>
							<a href="index.php?section=Kuwasys|Classes&action=showClassDetails&ID={$otherClass.ID}">{$otherClass.label}</a>
						</td>
						{if $counter}</tr>{/if}
					{else}
						{if $counter}<tr>{/if}
							<td>
							<!-- Void cell, to make sure rowspan is still correct even if class is the same as in classdetails -->
							---
							</td>
						{if $counter}</tr>{/if}
					{/if}
						{$counter = $counter + 1}
				{/foreach}
		</tr>
		{/foreach}
		{/if}
	</tbody>
</table>
{/block}
