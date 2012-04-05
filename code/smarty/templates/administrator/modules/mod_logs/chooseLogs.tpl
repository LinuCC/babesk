{extends file=$base_path}{block name=content}
<h3>Logs</h3>
<p>Bitte Kategorie der Logs ausw&auml;hlen:</p>
<!-- seems like sid is not used anymore, throw it out -->
<form action="index.php?section=logs&action=choose_sev&{$sid}" method="post">
	<fieldset>
		<legend>Kategorie</legend>
		<select name="Category" size="1">
		{section name=category loop=$categories}
            <option>{$categories[category]}</option>
        {/section}
        </select>
	</fieldset>
	<input type="submit" value="Submit" />
</form>


{/block}