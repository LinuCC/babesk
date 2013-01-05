{extends $inh_path} {block name="content"}

<h2 class="moduleHeader">Schüler per CSV-Datei importieren</h2>

<div class="main">
Die CSV-Datei sollte folgende Schlüssel beinhalten:
<table>
	<tr bgcolor='#33CFF'>
		<td>forename</td>
		<td>name</td>
		<td>username</td>
		<td>password</td>
		<td>email</td>
		<td>telephone</td>
		<td>birthday</td>
		<td>grade</td>
	</tr>
	<tr bgcolor='#FFC33'>
		<td>Der Vorname des Schülers</td>
		<td>Der Nachname des Schülers</td>
		<td>Der Benutzername des Schülers</td>
		<td>Das Password des Schülers, schon gehasht</td>
		<td>Die Emailadresse des Schülers</td>
		<td>Die Telefonadresse des Schülers</td>
		<td>Der Geburtstag des Schülers</td>
		<td>Der Klassenname des Schülers</td>
	</tr>
</table>
Alle Schlüssel können auch weggelassen werden, zum Beispiel brauchen sie nicht den
Benutzernamen des Schülers angeben wenn sie keinen setzen wollen.
Bitte beachten sie dabei auch die Groß - und Kleinschreibung. Außerdem dürfen kleine Leerzeichen in den Schlüsseln
vorhanden sein. Wählen sie das Semikolon (;) als Trennzeichen beim Speichern der Datei.
</div>

<form action="index.php?section=Kuwasys|Users&action=csvImport" enctype="multipart/form-data" method="post">
	<label>Bitte wählen sie die CSV-Datei aus: <input type="file" name="csvFile"></label>
	<input type="submit" value="Hochladen">
</form>

{/block}