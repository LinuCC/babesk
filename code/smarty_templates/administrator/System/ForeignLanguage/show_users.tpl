{extends file=$ForeignLanguageParent}{block name=content}
<table width=100%>
<tr><th align='center'>{$navbar}</th></tr>
</table>


<form action="index.php?section=System|ForeignLanguage&action=4"
	method="post" onsubmit="submit()">
<table class="table table-striped table-hover">
	<thead>
		<tr>
			<th><a href="index.php?section=System|ForeignLanguage&action=3&filter=ID">ID</a></th>
			<th><a href="index.php?section=System|ForeignLanguage&action=3&filter=forename">Vorname</a></th>
			<th><a href="index.php?section=System|ForeignLanguage&action=3&filter=name">Name</a></th>
			<th><a href="index.php?section=System|ForeignLanguage&action=3&filter=username">Benutzername</a></th>
			<th><a href="index.php?section=System|ForeignLanguage&action=3&filter=birthday">Geburtsdatum</a></th>
			<th>Fremdsprachen<br />
				{foreach from=$foreignLanguages item=foreignLanguage name=zaehler}
		{$foreignLanguage}&nbsp;
		{/foreach}
			</th>
		</tr>
	</thead>
	<tbody>
		{foreach $users as $user}
		<tr>
			<td>{$user.ID}</td>
			<td>{$user.forename}</td>
			<td>{$user.name}</td>
			<td>{$user.username}</td>
			<td>{$user.birthday}</td>
			<td>
				{foreach from=$foreignLanguages item=foreignLanguage name=zaehler}
		<input type="checkbox" name="{$user.ID}[]" value="{$foreignLanguage}" {if $user.foreign_language|strstr:$foreignLanguage}checked{/if} />
		&nbsp;&nbsp;
		{/foreach}
			</td>
			<td align="center">
			</td>
		</tr>
		{/foreach}
	</tbody>
</table>
<input id="submit" class="btn btn-default" onclick="submit()" type="submit" value="Speichern" />
</form>

{/block}