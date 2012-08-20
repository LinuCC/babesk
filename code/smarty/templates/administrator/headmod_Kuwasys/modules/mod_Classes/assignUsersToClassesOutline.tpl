{extends file=$inh_path} {block name='content'}

<style type='text/css'  media='all'>
fieldset {
	border: 1px solid #000000;
}
</style>


<h2 class="moduleHeader">Übersicht über die Zuweisungen</h2>
<br>

<fieldset>
<h5>Kurszuweisungen als Aktiv:</h5>
	{if !count($requestsPassed)} <b>Keine Veränderungen</b> {else}
	<table>
		<tr bgcolor='#33CFF'>
			<th>LinkID</th>
			<th>BenutzerID</th>
			<th>KlassenID</th>
			<th>Benutzername</th>
			<th>Kursname</th>
		</tr>
		{foreach $requestsPassed as $requestPassed}
		<tr bgcolor='#FFC33'>
			<td>{$requestPassed.jointId}</td>
			<td>{$requestPassed.userId}</td>
			<td>{$requestPassed.classId}</td>
			<td>{$requestPassed.username}</td>
			<td>{$requestPassed.classname}</td>
		</tr>
		{/foreach}
	</table>
	{/if}
</fieldset>

<fieldset>
	<h5>Kurszuweisungen als Wartend:</h5>

	{if !count($requestsNotPassed)} <b>Keine Veränderungen</b> {else}
	<table>
		<tr bgcolor='#33CFF'>
			<th>LinkID</th>
			<th>BenutzerID</th>
			<th>KlassenID</th>
			<th>Benutzername</th>
			<th>Kursname</th>
		</tr>
		{foreach $requestsNotPassed as $requestPassed}
		<tr bgcolor='#FFC33'>
			<td>{$requestPassed.jointId}</td>
			<td>{$requestPassed.userId}</td>
			<td>{$requestPassed.classId}</td>
			<td>{$requestPassed.username}</td>
			<td>{$requestPassed.classname}</td>
		</tr>
		{/foreach}
	</table>
	{/if}
</fieldset>
<form
	action="index.php?section=Kuwasys|Classes&action=assignUsersToClasses"
	method="post">
	<input type="submit" name="jointChangesConfirmed"
		value="Veränderungen durchführen">
</form>

{/block}
