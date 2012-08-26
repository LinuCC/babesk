{extends file=$ForeignLanguageParent}{block name=content}
<table width=100%>
<tr><th align='center'>{$navbar}</th></tr>
</table>


<form action="index.php?section=System|ForeignLanguage&action=4"
	method="post" onsubmit="submit()">
	<fieldset>
<table>
	<thead>
		<tr bgcolor='#33CFF'>
			<th align='center'><a href="index.php?section=System|ForeignLanguage&action=3&filter=ID">ID</a></th>
			<th align='center'><a href="index.php?section=System|ForeignLanguage&action=3&filter=forename">Vorname</a></th>
			<th align='center'><a href="index.php?section=System|ForeignLanguage&action=3&filter=name">Name</a></th>
			<th align='center'><a href="index.php?section=System|ForeignLanguage&action=3&filter=username">Benutzername</a></th>
			<th align='center'><a href="index.php?section=System|ForeignLanguage&action=3&filter=birthday">Geburtsdatum</a></th>		
			<th align='center'>Fremdsprachen<br />
				{foreach from=$foreignLanguages item=foreignLanguage name=zaehler}
		{$foreignLanguage}&nbsp;
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
				{foreach from=$foreignLanguages item=foreignLanguage name=zaehler}
		<input type="checkbox" name="{$user.ID}[]" value="{$foreignLanguage}" {if $user.foreign_language|strstr:$foreignLanguage}checked{/if} />
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