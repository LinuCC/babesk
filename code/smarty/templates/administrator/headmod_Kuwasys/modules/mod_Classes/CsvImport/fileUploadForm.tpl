{extends file=$inh_path} {block name='content'}

<h2 class="moduleHeader">{_g('Upload a file')}</h2>

<form action="index.php?module=administrator|Kuwasys|Classes|CsvImport|Review"
	method="post" enctype="multipart/form-data">

	<label for="csvFile">{_g('File:')}</label>
	<input type="file" name="csvFile" id="csvFile"><br />
	<input type="submit" value="{_g('Create Preview')}">
</form>

<fieldset class="smallContainer">
	<legend>{_g('Help:')}</legend>
	<p>
		Hier können sie Kurse mithilfe einer CSV-Datei importieren.<br />
		Diese lassen sich zum Beispiel von Microsoft Excel exportieren.<br />
		Die erste Zeile besteht dabei aus den Namen der Spalten, danach repräsentiert jede Zeile einen Kurs.<br />
		Die Benennung der Spalten sieht wie folgt aus (Groß- und Kleinschreibung wichtig!):<br />
		<ul>
			<li>"name": Der Name des Kurses.</li>
			<li>"description": Die Beschreibung des Kurses</li>
			<li>"maxRegistration": Die maximale Anzahl an Registrierungen des Kurses</li>
			<li>"classteacher": Der / die Kursleiter des Kurses. Mehrere Kursleiter müssen mit einem Komma (",") unterteilt werden.</li>
			<li>"day": Der Tag an dem der Kurs stattfindet.</li>
		</ul>
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