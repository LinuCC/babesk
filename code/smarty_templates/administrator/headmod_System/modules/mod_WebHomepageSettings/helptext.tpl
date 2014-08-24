{extends file=$inh_path} {block name='content'}

<h3 class="module-header">Hilfetext auf der Logiseite</h3>

<form action="index.php?section=System|WebHomepageSettings&amp;action=helptext" method="post">
	<label>Hilfetext, der beim Klicken auf den Link "Hilfe" angezeigt wird:<br><textarea name="helptext" cols="50" rows="10">{$helptext}</textarea></label><br>
	<label>Hinweis: Bleibt der Hilfetext leer, wird kein Link angezeigt auf der Loginseite.</label><br>
	<input type="submit" value="Hilfetext einrichten">
</form>

{/block}