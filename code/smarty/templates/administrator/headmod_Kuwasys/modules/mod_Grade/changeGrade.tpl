{extends file=$inh_path}
{block name='content'}

<style type='text/css'  media='all'>
div.moduleFormulars {
	width:350px;
	margin:0 auto;
}

select, input.moduleFormulars {
	float:right;
}
</style>

<h2 class='moduleHeader'>Eine Klasse verändern</h2>
<br>
<div class='moduleFormulars'>
<form action='index.php?module=administrator|Kuwasys|Grade|ChangeGrade&ID={$grade.ID}' method='post'>
	<label>Label:
		<input type='text' name='gradelabel' value='{$grade.label}' class='moduleFormulars'>
	</label> <br><br>
	<label>Jahrgangsstufe:<input type='text' name='gradelevel' value='{$grade.gradelevel}' class='moduleFormulars'></label> <br><br>
	{if count($schooltypes)}
	<label>
		Schultyp:
		<select name='schooltype' size='1'>
		{foreach $schooltypes as $schooltype}
		<option value='{$schooltype.ID}'
			{if $schooltype.ID = $grade.schooltypeId}
				selected='selected'
			{/if}>
			{$schooltype.name}
		</option>
		{/foreach}
		</select>
	</label>
	<br><br>
	{/if}
	<input type='submit' value='Ändern'>
</form>
</div>
{/block}
