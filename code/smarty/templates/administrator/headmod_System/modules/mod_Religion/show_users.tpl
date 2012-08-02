{extends file=$ReligionParent}{block name=content}
<form action="index.php?section=System|Religion&action=4"
	method="post" onsubmit="submit()">
	<fieldset>
<table>
	<thead>
		<tr bgcolor='#33CFF'>
			<th align='center'>ID<br/>&nbsp;</th>
			<th align='center'>Vorname<br/>&nbsp;</th>
			<th align='center'>Name<br />&nbsp;</th>
			<th align='center'>Benutzername<br/>&nbsp;</th>
			<th align='center'>Geburtstag<br/>&nbsp;</th>	
			<th align='center'>Religionszugeh&ouml;rigkeit<br />
				{foreach from=$religions item=religion name=zaehler}
		{$religion}&nbsp;
		{/foreach}
			</th>	
		</tr>
	</thead>
	<tbody>
		{foreach $users as $user}
		<tr bgcolor='#FFC33'>
			<td align="center">{$user.ID}</td>
			<td align="center">{$user.forename}</td>
			<td align="center">{$user.name}</td>
			<td align="center">{$user.username}</td>
			<td align="center">{$user.birthday}</td>
			<td align="center">
				{foreach from=$religions item=religion name=zaehler}
		<input type="radio" name="{$user.ID}" value="{$religion}" {if {$religion}=={$user.religion}}checked{/if} />
		{/foreach}
			</td>
			<td align="center" bgcolor='#FFD99'>
			</td>
		</tr>
		{/foreach}
	</tbody>
</table>
	<br> <input id="submit" onclick="submit()" type="submit" value="Speichern" />
</form>

{/block}