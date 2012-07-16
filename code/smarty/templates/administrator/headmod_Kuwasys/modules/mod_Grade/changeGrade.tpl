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
<form action='index.php?section=Kuwasys|Grade&action=changeGrade&ID={$grade.ID}' method='post'>
	<label>Label:<input type='text' name='label' value='{$grade.label}' class='moduleFormulars'></label> <br><br>
	<label>Jahrgangsstufe:<input type='text' name='year' value='{$grade.gradeValue}' class='moduleFormulars'></label> <br><br>
	<label>Schuljahr:
		<select name='schoolyear' size='1'>
		{foreach $schoolyears as $schoolyear}
			<option 
				value='{$schoolyear.ID}' 
				{if $schoolyear.ID == $grade.schoolyearId}selected='selected'{/if}>
				{$schoolyear.label}
			</option>
		{/foreach}
	</select>
	</label><br><br>
	<input type='submit' value='Ändern'>
</form>
</div>
{/block}