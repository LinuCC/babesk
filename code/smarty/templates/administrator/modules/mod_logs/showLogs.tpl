<h3>Logs</h3>
<p>Bitte Kategorie und Gewichtung der Logs ausw&auml;hlen:</p>
<form action="index.php?section=logs&action=show&{$sid}" method="post">
	<fieldset>
		<legend>Kategorie</legend>
		<select name="Category" size="1">
		{section name=category loop=$categories}
            <option>{$categories[category]}</option>
        {/section}
        </select>
        <legend>Gewichtung</legend>
        <select name="Severity" size="1">
        {section name=severity loop=$severity_levels}
            <option>{$severit_levels[severity]}</option>
        {/section}
        </select>
	</fieldset>
	<input type="submit" value="Submit" />
</form>

