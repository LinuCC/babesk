{extends file=$inh_path} {block name='content'}

<h2 class='moduleHeader'>Schultypen</h2>

<form action='index.php?section=System|Schooltype&amp;action=addSchooltype' method='POST'>
	<input type='submit' value="Einen Schultyp hinzufügen" />
</form>

{if isset($schooltypes) and count($schooltypes)}
	<table class='dataTable'>
		<thead>
			<tr>
				<th align='center'>ID</th>
				<th align='center'>Name</th>
			</tr>
		</thead>
		<tbody>
			{foreach $schooltypes as $schooltype}
			<tr>
				<td align="center">{$schooltype.ID}</td>
				<td align="center">{$schooltype.name}</td>
				<td align="center">
				<form action="index.php?section=System|Schooltype&action=changeSchooltype&ID={$schooltype.ID}" method="post"><input type='submit' value='bearbeiten'></form>
				<form action="index.php?section=System|Schooltype&action=deleteSchooltype&ID={$schooltype.ID}" method="post"><input type='submit' value='löschen'></form>
				</td>
			</tr>
			{/foreach}
		</tbody>
	</table>
{/if}

{/block}