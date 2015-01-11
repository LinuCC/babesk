{extends file=$inh_path} {block name='content'}

<h2 class="module-header">{t}Upload a file{/t}</h2>

<form action="index.php?module=administrator|Kuwasys|Classes|CsvImport|Review"
	method="post" enctype="multipart/form-data">

	<label for="csvFile">{t}File:{/t}</label>
	<input type="file" name="csvFile" id="csvFile"><br />
	<input type="submit" class="btn btn-primary" value="{t}Create Preview{/t}">
</form>

<fieldset class="smallContainer">
	<legend>{t}Help:{/t}</legend>
	<p>
		Hier können sie Kurse mithilfe einer Spreadsheet-Datei importieren.<br />
		Die können zum Beispiel in Microsoft Excel erstellt werden.
		Die erste Zeile besteht dabei aus den Namen der Spalten, danach repräsentiert jede Zeile einen Kurs.<br />
		Die Benennung der Spalten sieht wie folgt aus:<br>
		<table class="table">
			<tbody>
				<tr>
					<td>
						name
					</td>
					<td>
						Der Name des Kurses
					</td>
				</tr>
				<tr>
					<td>
						description
					</td>
					<td>
						Die Beschreibung des Kurses
					</td>
				</tr>
				<tr>
					<td>
						maxRegistration
					</td>
					<td>
						Die maximale Anzahl an Registrierungen des Kurses
					</td>
				</tr>
				<tr>
					<td>
						classteacher
					</td>
					<td>
						Der / die Kursleiter des Kurses.
						Mehrere Kursleiter müssen mit einem Komma (",") unterteilt werden.
					</td>
				</tr>
				<tr>
					<td>
						day
					</td>
					<td>
						Der Tag an dem der Kurs stattfindet.
						Mehrere Tage müssen mit einem Komma unterteilt werden.
					</td>
				</tr>
				<tr>
					<td>
						isOptional
					</td>
					<td>
						Ob der Kurs optional ist.
						Eine Eins ("1") für ja, eine Null ("0") für nein.
					</td>
				</tr>
			</tbody>
		</table>
		<p>
			<div class="alert alert-info"><b>Die richtige Schreibweise</b> der ersten Zeile ist sehr wichtig, da die Datei sonst nicht gelesen werden kann.
			Achten sie also bitte auf Groß- und Kleinschreibung sowie darauf dass keine Leerzeichen davor oder dahinter sind!</div>
			Eine richtige Spreadsheet-Datei würde zum Beispiel so aussehen:
		</p>
		<table class="table table-bordered">
			<tbody>
				<tr>
					<td>
						name
					</td>
					<td>
						description
					</td>
					<td>
						maxRegistration
					</td>
					<td>
						classteacher
					</td>
					<td>
						day
					</td>
					<td>
						isOptional
					</td>
				</tr>
				<tr>
					<td>
						Schiffe Bauen
					</td>
					<td>
						Hier bauen wir Schiffe!
					</td>
					<td>
						25
					</td>
					<td>
						Max Mustermann
					</td>
					<td>
						Montag, Dienstag
					</td>
					<td>
						0
					</td>
				</tr>
				<tr>
					<td>
						Angeln
					</td>
					<td>
						Hier kann man angeln...
					</td>
					<td>
						10
					</td>
					<td>
						Andrea Schiffmacher, Hans Horst
					</td>
					<td>
						Mittwoch
					</td>
					<td>
						1
					</td>
				</tr>
			</tbody>
		</table>
	</p>
</fieldset>

{/block}
