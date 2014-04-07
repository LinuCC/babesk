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

<h2 class='moduleHeader'>Eine Klasse hinzufügen</h2>
<br>
<div class='moduleFormulars'>
<form action='index.php?module=administrator|System|Grade|AddGrade' method='post'>
	<label>Jahrgangsstufe:<input type='text' name='gradelevel' class='moduleFormulars'></label> <br><br>
	<label>Label:
		<input type='text' name='gradelabel' class='moduleFormulars'>
	</label> <br /><br />
	{if count($schooltypes)}
	<label> Schultyp:
		<select name='schooltype' size='1'>
		{foreach $schooltypes as $schooltype}
		<option value='{$schooltype.ID}'>
			{$schooltype.name}
		</option>
		{/foreach}
		</select>
	</label>
	{/if}
	<br><br>
	<input type='submit' value='Hinzufügen'>
</form>
</div>
{/block}
