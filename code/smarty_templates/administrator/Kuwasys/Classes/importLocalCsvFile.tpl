{extends $inh_path} {block name="content"}

<h2 class="module-header">Kurse per CSV-Datei importieren</h2>

Die CSV-Datei sollte folgende Schlüssel beinhalten:
<table>
	<tr bgcolor='#33CFF'>
		<td>label</td>
		<td>description</td>
		<td>maxRegistration</td>
		<td>registrationEnabled</td>
		<td>weekday</td>
		<td>schoolyearName</td>
		<td>classteacherName</td>
	</tr>
	<tr bgcolor='#33CFF'>
		<td>Name des Kurses</td>
		<td>Beschreibung des Kurses</td>
		<td>Maximal erlaubte Registrierungen</td>
		<td>Registrierungen erlaubt oder nicht (wenn erlaubt, dann auf "1" gesetzt, ansonsten auf "0")</td>
		<td>Der Veranstaltungstag in Englisch ("monday", "tuesday", "wednesday", "thursday")</td>
		<td>Der Name des Schuljahres, in dem der Kurs sein soll. Das Schuljahr muss schon in dem Programm vorhanden sein!</td>
		<td>Der Name des Kursleiters. Der Name muss aus "Vorname Nachname" bestehen.
			Der Kursleiter muss bereits in dem Programm vorhanden sein</td>
	</tr>
</table>
Bitte beachten sie dabei auch die Groß - und Kleinschreibung. Wählen sie das Semikolon (;) als Trennzeichen beim Speichern der Datei.
</div>
</div>
<div id="main">
<form action="index.php?section=Kuwasys|Classes&action=csvImport" enctype="multipart/form-data" method="post">
	<label>Bitte wählen sie die CSV-Datei aus: <input type="file" name="csvFile"></label>
	<input type="submit" value="Hochladen">
</form>
</div>
{/block}