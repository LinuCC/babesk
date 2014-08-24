{extends $inh_path} {block name="content"}

<h2 class="module-header">Kursleiter per CSV-Datei importieren</h2>

<div class="main">
Die CSV-Datei sollte folgende Schlüssel beinhalten:
<table class="dataTable">
	<tr>
		<th>forename</th>
		<th>name</th>
		<th>wholeName</th>
		<th>adress</th>
		<th>telephone</th>
	</tr>
	<tr>
		<td>Der Vorname des Kursleiters</td>
		<td>Der Name des Kursleiters</td>
		<td>Der ganze Name (Vor- und Nachname, mit einem Leerzeichen getrennt) des Kursleiters. Nur benutzen, wenn es kein forename und name gibt!</td>
		<td>Die Adresse</td>
		<td>Die Telefonnummer</td>
	</tr>
</table>
Bitte beachten sie dabei auch die Groß - und Kleinschreibung. Wählen sie das Semikolon (;) als Trennzeichen beim Speichern der Datei.
Wenn sie Kursleiter bereits in der CSV-Datei Kursen zuweisen wollen, beachten sie bitte, dass dies nur für Kurse des aktuell aktivierten
Jahrgangs möglich ist.
</div>

<form action="index.php?section=Kuwasys|ClassTeacher&action=csvImport" enctype="multipart/form-data" method="post">
	<label>Bitte wählen sie die CSV-Datei aus: <input type="file" name="csvFile"></label>
	<input type="submit" value="Hochladen">
</form>

{/block}