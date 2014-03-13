{extends file=$soliParent} {block name=content}
<form action="index.php?section=Babesk|Soli&action=1" method="post">
<h4>Name:</h4>
	<select name="UID">
	{foreach $solis as $soli}
		<option value='{$soli.ID}'> {$soli.forename} {$soli.name}</option>
	{/foreach}
	</select><br>
	
	<h4>Gültigkeitsbereich:</h4>
	Von:<br>
	{html_select_date prefix='StartDate' end_year="+1"}<br>
	Bis:<br>
	{html_select_date prefix='EndDate' end_year="+1"}<br>
	<input type="submit" value="Absenden" />
</form>
{/block}