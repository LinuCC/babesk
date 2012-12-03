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
<form action='index.php?section=Kuwasys|Grade&action=addGrade' method='post'>
	<label>Jahrgangsstufe:<input type='text' name='year' class='moduleFormulars'></label> <br><br>
	<label>Label:<input type='text' name='label' class='moduleFormulars'></label> <br><br>
	<label>Schuljahr:
		<select name='schoolyear' size='1'>
		{foreach $schoolyears as $schoolyear}
			<option
				value='{$schoolyear.ID}'
				{if $schoolyear.active}selected='selected'{/if}>
				{$schoolyear.label}
			</option>
		{/foreach}
	</select>
	</label><br><br>
	<input type='submit' value='Hinzufügen'>
</form>
</div>
{/block}