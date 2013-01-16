{extends file=$inh_path} {block name='content'}

<h2 class='moduleHeader'>Die zu ändernden Kurse</h2>
{if $tempTableExists}
<p>Hier werden die Benutzer ihren Kursen zugeordnet. Sie haben bereits eine
	temporäre Sitzung erstellt.
	Wollen sie fortfahren oder eine neue Zuordnung durchführen?<br /><br />
<form action="index.php?section=Kuwasys|Classes&amp;action=assignUsersToClasses"
	method="post">
	<input type="submit" name="tempTableResetNotConfirmed" value="Die Tabelle NICHT zurücksetzen und mit der Sitzung weitermachen">
	<input type="submit" name="tempTableResetConfirmed" value="Die Tabelle zurücksetzen!"><br />
</form>
</p>
{else}
<p>
	Hier werden die Benutzer ihren Kursen zugeordnet. Wenn sie fortfahren, werden temporäre Daten erstellt, wo sie zuerst die Änderungen einsehen und Anpassungen vornehmen können, bevor die Schüler unwiederbringlich den Kursen zugeordnet werden
<form action="index.php?section=Kuwasys|Classes&amp;action=assignUsersToClasses"
	method="post">
	<input type="submit" name="tempTableResetConfirmed" value="Die Daten neu erstellen"><br />
</form>
</p>
{/if}
{/block}