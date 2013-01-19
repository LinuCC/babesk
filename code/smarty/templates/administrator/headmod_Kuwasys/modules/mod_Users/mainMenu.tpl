#{extends file=$inh_path} {block name='content'}

<style type='text/css'  media='all'>
/*Table should not be over the Border of the Line of the main-block*/

fieldset {
	margin-left: 5%;
	margin-right: 5%;
	margin-bottom: 30px;
	border: 2px dashed rgb(100,100,100);
}
</style>

<h2 class="moduleHeader">Hauptmenü des Benutzer-Moduls</h2>

<fieldset>
<legend>Benutzer verändern</legend>
<form action='index.php?section=Kuwasys|Users&action=addUser' method='post'>
	<input type='submit' value='einen neuen Schüler hinzufügen'>
</form>
<form action='index.php?section=Kuwasys|Users&action=csvImport' method='post'>
	<input type='submit' value='Schüler per CSV importieren'>
</form>
<form action='index.php?section=Kuwasys|Users&action=resetPasswords' method='post'>
	<input type='submit' value='Alle Passwörter der Schüler zurücksetzen'>
</form>
</fieldset>

<fieldset>
<legend>Benutzer anzeigen</legend>
<form action='index.php?section=Kuwasys|Users&action=showUsersGroupedByYearAndGrade' method='post'>
	<input type='submit' value='Die Schüler geordnet anzeigen'>
</form>
<form action='index.php?section=Kuwasys|Users&action=showWaitingUsers' method='post'>
	<input type='submit' value='Wartende Schüler anzeigen ("kommutierte Schülerliste")'>
</form>
<form action='index.php?section=Kuwasys|Users&action=showUsers' method='post'>
	<input type='submit' value='alle Schüler anzeigen'>
</form>
</fieldset>

<fieldset>
	<legend>Sonstiges</legend>
	<form action='index.php?section=Kuwasys|Users&action=printParticipationConfirmationForAll' method='post'>
		<input type='submit' value='Für alle Schüler die Teilnahmebestätigungen ausdrucken'>
	</form>
</fieldset>

{/block}