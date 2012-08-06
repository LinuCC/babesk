{extends file=$inh_path} {block name="content"}

<h2 class='moduleHeader'>Details des Kurses "{$class.label}"</h2>

{literal}
<style type='text/css'  media='all'>
th {
	color: rgb(100,100,100);
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
		<td>{if isset($class.sumStatus.active)}{$class.sumStatus.active}{else}---{/if}</td>
	</tr>
	<tr>
		<th>Wartend:</th>
		<td>{if isset($class.sumStatus.waiting)} {$class.sumStatus.waiting} {else}---{/if}</td>
	</tr>
	<tr>
		<th>Wunsch:</th>
		<td>{if isset($class.sumStatus.request)} {$class.sumStatus.request} {else}---{/if}</td>
	</tr>
	<tr>
		<th>Schüler-Registrierungen erlaubt:</th>
		<td>{if $class.registrationEnabled}<b>Ja</b>{else}<b>Nein</b>{/if}</td>
	</tr>
	<tr>
		<th>Veranstaltungstag:</th>
		<td>{if $class.weekdayTranslated}{$class.weekdayTranslated}{else}---{/if}</td>
	</tr>
	<tr>
		<th>Teilnehmer:</th>
		<td>
		{if isset($class.users)}
			{foreach $class.users as $user}
				<a class="valueDiv"
				{if $user.status == 'active'} style="color: rgb(255, 50, 50);" 
				{else if $user.status == 'waiting'} style="color: rgb(50, 255, 50);" 
				{else if $user.status == 'request'} style="color: rgb(50, 50, 255);" {/if}
				 href="index.php?section=Kuwasys|Classes&action=changeLinkUserToClass&classId={$class.ID}&userId={$user.ID}">{$user.forename} {$user.name} -- {$user.status}</a><br>
			{/foreach}
			</div>
			{else}
			<b>keine Teilnehmer</b>
		{/if}
		</td>
	</tr>
</table>

{/block}