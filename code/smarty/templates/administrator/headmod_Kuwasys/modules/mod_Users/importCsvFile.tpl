{extends file=$inh_path} {block name='content'}

<h2 class="moduleHeader">CSV-Import-Test</h2>

<div class="csvUpload">

	<div class="smallContainer uploadButton">
		<input id="csvFileupload" type="file" name="files[]" data-url="index.php?section=Kuwasys|Users&amp;action=csvImport" />
	</div>

	<p id="infotext" class="smallContainer">Die Vorschaufunktion ist an. Das heißt, es wird zuerst eine Vorschau beim Dateihochladen erstellt, ohne die Datenbank zu verändern.</p>

	<span class="accordion">
		<h3>Vorschau</h3>
		<div class="preview">
			<p>
				Datei wurde noch nicht hochgeladen
			</p>
		</div>
		<h3>Fehler <span class="errorCount noAction">---</span></h3>
		<div class="error">
			<p>
				Die Datei wurde noch nicht hochgeladen
			</p>
		</div>
		<h3>Felderkonfiguration</h3>
		<div class="fieldSettings">
			<p>
				Datei wurde noch nicht hochgeladen
			</p>
		</div>
		<h3>Weitere Konfiguration</h3>
		<div class="moreSettings">
			<p>
				<div>
					Feld-Delimiter:
					<input id="csvDelimiter" type="text" maxLength="1" size="1" value="," />
				</div>
				<div>
					Einspaltig erlaubt:
					<input id="isSingleColumnAllowed" type="checkbox" />
				</div>
				<div>
					Datei nur als Vorschau benutzen:
					<input id="isPreview" type="checkbox" checked="checked" />
				</div>
			</p>
		</div>
	</span>

</div>

<script src="../include/js/jqueryFileUpload/jquery.ui.widget.js"></script>
<script src="../include/js/jqueryFileUpload/jquery.iframe-transport.js">
	</script>
<script src="../include/js/jqueryFileUpload/jquery.fileupload.js"></script>
<script src="../include/js/CsvFileUploader.js"></script>
<script type="text/javascript">

$(document).ready(function() {
	var uploader = new CsvFileUploader();
	$('.accordion').accordion({
		collapsible: true,
		heightStyle:"content",
	});

	$('#isPreview').on('click', function(event) {
		if(!$(this).prop('checked')) {
			uploader.previewOff();
		}
		else {
			uploader.previewOn();
		}
	});
});

</script>

{/block}