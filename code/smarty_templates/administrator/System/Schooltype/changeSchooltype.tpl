{extends file=$inh_path} {block name='content'}

<h3 class="module-header">Einen Schultyp ändern</h3>

<form action="index.php?section=System|Schooltype&amp;action=changeSchooltype&amp;ID={$schooltype.ID}" method='POST'>
	<div class="form-group">
		<label for="name">Schultyp-Name</label>
		<input type="text" id="name" class="form-control" name="name" value="{$schooltype.name}" />
	</div>
	<input class="btn btn-primary" type="submit" value="ändern" />
	<a class="btn btn-default"
		href="index.php?module=administrator|System|Schooltype">Abbrechen</a>
</form>
{/block}