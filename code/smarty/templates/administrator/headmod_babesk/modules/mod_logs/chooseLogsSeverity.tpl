{extends file=$logsParent}{block name=content}
Bitte Gewichtung der Logs ausw&auml;hlen.

<form action="index.php?section=logs&action=show&Category={$category}"
	method="post">
	<select name="Severity" size="1"> {section name=severity
		loop=$severity_levels}
		<option>{$severity_levels[severity]}</option> {/section}
	</select><br><br>
	<input type='submit' value='Logs anzeigen'>
</form>

{/block}