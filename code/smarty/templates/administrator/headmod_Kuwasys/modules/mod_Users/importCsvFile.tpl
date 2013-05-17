{extends file=$inh_path} {block name='content'}
<style type='text/css'  media='all'>
/*Table should not be over the Border of the Line of the main-block*/

.errorCount {
	float: right;
	margin-right: 20px;
}

</style>

<h2 class="moduleHeader">CSV-Import-Test</h2>

<div class="smallContainer">
<input id="csvFileupload" type="file" name="files[]" data-url="index.php?section=Kuwasys|Users&amp;action=csvImport" />
</div>
<p class="smallContainer">Die Datei wurde noch nicht hochgeladen oder war fehlerhaft. Wenn sie die Datei jetzt hochladen, wird sie geprüft, es verändert sich nichts. Sie haben danach bei fehlerfreier Datei die Möglichkeit, die Datenbank zu füllen.</p>

<span class="accordion">
	<h3>Vorschau</h3>
	<div class="csvUpload preview">
		<p>
			Datei wurde noch nicht hochgeladen
		</p>
	</div>
	<h3>Fehler <span class="errorCount">0</span></h3>
	<div class="csvUpload error">
		<p>
			Die Datei wurde noch nicht hochgeladen
		</p>
	</div>
	<h3>Felderkonfiguration</h3>
	<div class="csvUpload fieldSettings">
		<p>
			Datei wurde noch nicht hochgeladen
		</p>
	</div>
	<h3>Weitere Konfiguration</h3>
	<div class="csvUpload moreSettings">
		<p>
			<label>
				Feld-Delimiter:
				<input id="csvDelimiter" type="text" maxLength="1" size="1" value=";" />
			</label>
		</p>
	</div>
</span>

<script src="../include/js/jqueryFileUpload/jquery.ui.widget.js"></script>
<script src="../include/js/jqueryFileUpload/jquery.iframe-transport.js"></script>
<script src="../include/js/jqueryFileUpload/jquery.fileupload.js"></script>
<script src="../include/js/csvFileUploader.js"></script>

<script type="text/javascript">

$(document).ready(function() {
	var uploader = new csvFileUploader();
	$('.accordion').accordion({
		collapsible: true,
		heightStyle:"content",
	});
});

</script>

{/block}