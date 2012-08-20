{extends file=$inh_path} {block name='content'}

<h2 class='moduleHeader'>Hauptmenü der Kursverwaltung</h2>

{if !$isClassRegistrationGloballyEnabled}<p>Kursregistrierungen sind nicht erlaubt!</p>
{else}<p>Kursregistrierungen sind erlaubt</p>
{/if}<br>

<form action='index.php?section=Kuwasys|Classes&action=addClass' method='post'>
	<input type='submit' value='einen neuen Kurs hinzufügen'>
</form>
<form action='index.php?section=Kuwasys|Classes&action=csvImport' method='post'>
	<input type='submit' value='Kurse per CSV-Datei importieren'>
</form>
<form action='index.php?section=Kuwasys|Classes&action=toggleGlobalClassRegistrationEnabled' method='post'>
	<input type='submit' value='Bei allen Kursen Registrierungen erlauben / nicht erlauben'>
</form>
<form action='index.php?section=Kuwasys|Classes&action=showClass' method='post'>
	<input type='submit' value='Die Kurse anzeigen'>
</form>
<form action='index.php?section=Kuwasys|Classes&action=assignUsersToClasses' method='post'>
	<input type='submit' value='Schüler gewählten Kursen zuweisen'>
</form>


{/block}