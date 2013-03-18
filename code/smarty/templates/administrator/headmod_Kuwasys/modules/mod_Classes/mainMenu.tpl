{extends file=$inh_path} {block name='content'}

<h2 class='moduleHeader'>Hauptmen&uuml; der Kursverwaltung</h2>

{if !$isClassRegistrationGloballyEnabled}<p>Kursregistrierungen sind nicht erlaubt!</p>
{else}<p>Kursregistrierungen sind erlaubt</p>
{/if}<br>

<form action='index.php?section=Kuwasys|Classes&amp;action=addClass' method='post'>
	<input type='submit' value='einen neuen Kurs hinzuf&uuml;gen'>
</form>
<form action='index.php?section=Kuwasys|Classes&amp;action=csvImport' method='post'>
	<input type='submit' value='Kurse per CSV-Datei importieren'>
</form>
<form action='index.php?section=Kuwasys|Classes&amp;action=toggleGlobalClassRegistrationEnabled' method='post'>
	<input type='submit' value='Bei allen Kursen Registrierungen erlauben / nicht erlauben'>
</form>
<form action='index.php?section=Kuwasys|Classes&amp;action=showClass' method='post'>
	<input type='submit' value='Die Kurse anzeigen'>
</form>
<form action='index.php?section=Kuwasys|Classes&amp;action=createClassTable'
	method='post'>
	<input type='submit' value='Die Kurs&uuml;bersichten erstellen'>
</form>
<form action='index.php?section=Kuwasys|Classes&amp;action=assignUsersToClasses' method='post'>
	<input type='submit' value='Sch&uuml;ler gew&auml;hlten Kursen zuweisen'>
</form>


{/block}