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
		Hier können sie Kurse mithilfe einer CSV-Datei importieren.<br />
		Diese lassen sich zum Beispiel von Microsoft Excel exportieren.<br />
		Die erste Zeile besteht dabei aus den Namen der Spalten, danach repräsentiert jede Zeile einen Kurs.<br />
		Die Benennung der Spalten sieht wie folgt aus (Groß- und Kleinschreibung sowie Leerzeichen wichtig!):<br />
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
		Beim Exportieren mithilfe ihres Programms achten sie bitte darauf, dass als Separator das Semicolon (";") benutzt wird.<br />
		Allerdings dürfen die jeweiligen Felder selber keine Semikolons enthalten!<br />
		Eine richtige CSV-Datei würde zum Beispiel so aussehen:
		<fieldset class="blockyField">
			name;description;maxRegistration;classteacher;day<br />
			Wir machen Wacken;Dies ist eine Beschreibung;25;Frank Elstner;Montag
		</fieldset>
	</p>
</fieldset>

{/block}
