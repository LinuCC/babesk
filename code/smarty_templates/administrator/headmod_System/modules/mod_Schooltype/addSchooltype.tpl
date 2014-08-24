{extends file=$inh_path} {block name='content'}

<h2 class="module-header">Einen Schultyp hinzufügen</h2>

<form action="index.php?section=System|Schooltype&amp;action=addSchooltype"
	method='POST'>
	<label>Schultyp-Name: <input type="text" name="name" /></label>
	<input type="submit" value="hinzufügen" />
</form>
{/block}