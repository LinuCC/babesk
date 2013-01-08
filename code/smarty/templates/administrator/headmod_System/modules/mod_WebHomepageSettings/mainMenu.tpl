{extends file=$inh_path} {block name='content'}

<h3 class="moduleHeader">Benutzerhomepage-Einstellungen</h3>

<form action="index.php?section=System|WebHomepageSettings&amp;action=redirect" method="post">
	<input type="submit" value="Die Weiterleitung nach dem Login einstellen">
</form>

{/block}