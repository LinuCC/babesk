{extends file=$inh_path} {block name='content'}

<h2 class='module-header'>Liste der ausgeliehenen B&uuml;cher ({$className})</h2>

<fieldset id="grade-list">
	<legend>Klassen</legend>
	{$listOfClasses}
</fieldset>

<table class="table table-responsive table-striped table-hover">
	<tr>
		<th align="center">Name</th>
		<th align="center">Vorname</th>
		<th align="center">Ausgeliehende B&uuml;cher</th>
	</tr>

	{for $i=0 to $nr}
	<tr>
		<td align="left">{$name[$i]}</td>
		<td align="left">{$forename[$i]}</td>
		<td align="left">{$books[$i]}</td>
	</tr>
	{/for}
</table>


{/block}
