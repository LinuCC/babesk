{extends file=$base_path}{block name=content}


<h3>Fits-Einstellungen bearbeiten:</h3>
<br>
<form action='index.php?section=Fits|FitsSettings'
	method="post">
	Kennwort: <input type="text" name="password" value="{$key}"></input><br>
	Schuljahr: <input type="text" name="schoolyear" value="{$year}"></input><br>
	Jahrgang: <input type="text" name="class" value="{$class}"></input><br>
	Alle Klassen im Jahrgang? <input type="checkbox" name="allClasses" value="bn" {if $allClasses}checked{/if} /><br>
	<input id="submit" onclick="submit()" type="submit" value="Speichern" />
</form>
{/block}