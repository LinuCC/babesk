{extends file=$inh_path} {block name='content'}

<h3 class="module-header">Benutzerhomepage-Einstellungen</h3>

<form action="index.php?section=System|WebHomepageSettings&amp;action=redirect" method="post">
	<input class="btn btn-default" type="submit" value="Die Weiterleitung nach dem Login einstellen">
</form>
<form action="index.php?section=System|WebHomepageSettings&amp;action=helptext" method="post">
	<input class="btn btn-default" type="submit" value="Hilfetext auf der Loginseite einrichten">
</form>
    <form action="index.php?section=System|WebHomepageSettings&amp;action=maintenance" method="post">
        <input class="btn btn-default" type="submit" value="Den Wartungsmodus einstellen">
    </form>
{/block}