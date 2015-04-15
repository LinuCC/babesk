{extends file=$inh_path} {block name='content'}

<h3 class="module-header">Weiterverlinkung nach dem Login</h3>

<form action="index.php?section=System|WebHomepageSettings&amp;action=redirect" method="post">
	<label>Sekunden, nach der der Benutzer weitergeleitet werden soll (Delayzeit):<br><input type="text" name="time" value="{$delay}"></label><br>
	<label>Modulpfad (als Headmodul|Modul) zur Weiterleitung:<br><input type="text" name="target" value="{$target}"></label><br><br>
	<input type="submit" value="Weiterleitung einrichten">
</form>

{/block}