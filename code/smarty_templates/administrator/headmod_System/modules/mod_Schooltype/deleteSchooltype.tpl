{extends file=$inh_path} {block name='content'}

<h2 class="module-header">Einen Schultyp löschen</h2>

<b>Wollen sie den Schultypen "{$schooltype.name}" wirklich löschen?</b>

<form action="index.php?section=System|Schooltype&amp;action=deleteSchooltype&amp;ID={$schooltype.ID}" method='POST'>
	<input type="submit" value="NICHT löschen!" name="nonono"/>
	<input type="submit" value="löschen!" name="deletePls"/>
</form>
{/block}