<h3>Administrator Gruppe hinzuf&uuml;gen</h3>
<p>Bitte den Namen der Administrator Gruppe sowie die erlaubten Module eingeben</p>
<form action="index.php?section=admin&action=3" method="post">
	<fieldset>
		<label for="groupname">Gruppenname:</label>
        <input type="text" name="groupname" /><br><br>
		<p>W&auml;hlen sie die erlaubten Module aus:</p>
		<!-- {section name=module loop=$modules}
            <input type="checkbox" name="modules[]" value="{$modules[module]}" />{$module_names[$modules[module]]}<br />
        {/section} -->
        {html_checkboxes name="modules" options=$modules separator="<br>"}       
	</fieldset>
	<input type="submit" value="Submit" />
</form>
