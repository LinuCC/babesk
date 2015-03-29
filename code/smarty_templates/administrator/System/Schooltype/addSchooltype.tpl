{extends file=$inh_path} {block name='content'}

<h3 class="module-header">Einen Schultyp hinzufügen</h3>

<form action="index.php?section=System|Schooltype&amp;action=addSchooltype"
	method='POST' role="form">
	<div class="form-group">
		<label for="name">Schultyp-Name</label>
		<input type="text" id="name" class="form-control" name="name" />
	</div>
	<input class="btn btn-primary" type="submit" value="hinzufügen" />
</form>
{/block}