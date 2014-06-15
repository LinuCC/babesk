{extends file=$inh_path} {block name='content'}

<h3 class="moduleHeader">Benutzerhomepage-Einstellungen</h3>

<form action="index.php?section=System|WebHomepageSettings&amp;action=redirect" method="post">
	<input type="submit" value="Die Weiterleitung nach dem Login einstellen">
</form>
<form action="index.php?section=System|WebHomepageSettings&amp;action=helptext" method="post">
	<input type="submit" value="Hilfetext auf der Loginseite einrichten">
</form>
    <form action="index.php?section=System|WebHomepageSettings&amp;action=maintenance" method="post">
        <input type="submit" value="Den Wartungsmodus einstellen">
    </form>
{/block}