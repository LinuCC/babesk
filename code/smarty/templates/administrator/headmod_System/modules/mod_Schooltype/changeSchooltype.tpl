{extends file=$inh_path} {block name='content'}

<h2 class="moduleHeader">Einen Schultyp ändern</h2>

<form action="index.php?section=System|Schooltype&amp;action=changeSchooltype&amp;ID={$schooltype.ID}" method='POST'>
	<label>Schultyp-Name: <input type="text" name="name"
		value="{$schooltype.name}" /></label>
	<input type="submit" value="ändern" />
</form>
{/block}