<form action="index.php?section=soli&action=2" method="post">
Name:
	<select name="name">
	{foreach item=x from=$solis}
	<option value='{$x}'> {$x}</option>
	{/foreach}
	</select><br>
Verfallsdatum:
{html_select_date end_year="+1"}<br>
	<input type="submit" value="Absenden" />
</form>