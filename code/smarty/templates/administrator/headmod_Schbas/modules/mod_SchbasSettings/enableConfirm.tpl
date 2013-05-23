{extends file=$schbasSettingsParent}{block name=content}


<h3>Formulare freischalten:</h3>
<br>
<form action='index.php?section=Schbas|SchbasSettings&action=9'
	method="POST">
	Freischalten? <input type="checkbox" name="enable" value="1" {if $enabled == 1}checked{/if}/><br>
	<input id="submit" type="submit" value="Speichern" />
</form>
{/block}