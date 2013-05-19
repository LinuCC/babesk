/**
 * Some general functions allowing to import Data as Csv
 */

/**
 * Contains functions allowing uploading a CSVFile and showing errors and so on
 *
 * It connects to predefined HTML-elements
 *
 * @author Pascal Ernst <pascal.cc.ernst gmail.com>
 */
CsvFileUploader = function () {

	csvLimiter = '';

	/**
	 * Uploads the CSV-File selected by the User and handles all of the data
	 * incoming from the server
	 */
	$('#csvFileupload').fileupload({

		multipart: true,
		paramName: "csvFile",
		// dataType: 'text/csv',

		done: function (e, data) {
			console.log(data.result);
			if(checkForErrors(data.result)) {
				res = JSON.parse(data.result);
				viewUpdate(res);
			}
		},

		change: function(e, data) {
			fileuploadUpdate();
		},

		error: function (e, data) {
			alert('Ein Fehler ist beim Hochladen der CSV-Datei aufgetreten!');
		}
	});

	/**
	 * Updates the additional form-Data send with fileuploader when uploading a
	 * file
	 */
	function fileuploadUpdate() {

		var voidColumnAllowed = voidColumnsAllowedAsJsonFetch();
		var isPreview = $('#isPreview').prop('checked');

		/*we want the csvLimiter selected at the time the File gets
		uploaded, not the time this object gets constructed*/
		$('#csvFileupload').fileupload(
			'option',
			'formData',
			{
				"isSingleColumnAllowed":
					$('#isSingleColumnAllowed').prop('checked'),
				"voidColumnAllowed": JSON.stringify(voidColumnAllowed),
				"isPreview": isPreview,
				"csvDelimiter": $('#csvDelimiter').val()
			}
		);
	}

	/**
	 * Gets the values of the Checkboxes defining if columns can be void
	 * @return {Array} An Array containing Objects with the Name: Value Pair
	 */
	function voidColumnsAllowedAsJsonFetch() {

		var voidColumnAllowed = new Array();

		$('.voidColumnAllowed').each(function(index) {
			voidColumnAllowed[index] = new Object();
			voidColumnAllowed[index][$(this).attr('id')] =
				$(this).prop('checked');
		});

		return voidColumnAllowed;
	}

	/**
	 * Updates the content of various Containers of the Browser
	 * @param  {Array} res The result from the Server
	 */
	function viewUpdate(res) {

		errorViewUpdate(res);
		$('.preview p').html(res.preview);
		csvColumnsViewUpdate(res.csvColumns, res.keysAllowedVoid);
	}

	/**
	 * Updates the Use's Errorview
	 * @param  {Object} res The Returned Object of the PHP-Server
	 */
	function errorViewUpdate(res) {

		if(res.errors != '') {
			$('.error p').html(res.errors);
		}
		else {
			$('.error p').html('Keine Fehler gefunden');
		}

		if(res.errorCount > 0) {
			$('.errorCount').removeClass('noError');
			$('.errorCount').removeClass('noAction');
		}
		else {
			$('.errorCount').addClass('noError');
			$('.errorCount').removeClass('noAction');
		}
		$('.errorCount').html(res.errorCount);
	}

	/**
	 * Updates the User's form of csvColumns that are allowed to be void
	 * @param  {Object} res A result-Object from the Ajax-Call
	 */
	function csvColumnsViewUpdate(res, keysAllowedVoid) {

		var input = 'Bitte anwählen, welche Spalten leergelassen werden können<br />';

		for(var column in res) {
			isChecked = voidColumnAllowedAlreadyChecked(res[column], keysAllowedVoid);
			input += csvColumnsViewHtmlCreate(res[column], isChecked);
		}
		if(input != '') {
			$('.fieldSettings p').html(input);
		}
		else {
			$('.fieldSettings p').html("Es konnten keine Felder gefunden werden?!");
		}
	}

	/**
	 * Creates the HTML-Code for a Checkbox
	 * @param  {Boolean} isChecked if the Checkbox should be checked or not
	 * @return {string} The finished HTML-Code for a checkbox
	 */
	function csvColumnsViewHtmlCreate(column, isChecked) {

		var str = '<label><input type="checkbox" class="voidColumnAllowed" name="allowedVoidFields" value="' + column + '" '
			+ 'id="' + column;

		if(isChecked) {
			str = str + '" checked="checked" />'
		}
		else {
			str = str + '" />'
		}

		str = str + column + '</label><br />';

		return str;
	}

	/**
	 * Checks if the columnName is in the Array keysAllowedVoid as a Key
	 * @param  {string} columnName The name of the Key to search for
	 * @param  {string} keysAllowedVoid The Array to search the Key in
	 * @return {boolean} true if the key was found, false if not
	 */
	function voidColumnAllowedAlreadyChecked(columnName, keysAllowedVoid) {

		for(var key in keysAllowedVoid) {
			if(key == columnName) {
				return true;
			}
		}
		return false;
	}

	/**
	 * Checks for Errors returned by the Server and handles them
	 * @param  {Object} res The Object returned by the Server
	 * @return {Boolean} True if no Error occured, else false
	 */
	function checkForErrors(res) {
		ret = false;
		if(res == 'voidCsv') {
			alert('In der CSV-Datei konnten keine Daten gefunden werden!');
		}
		else if(res == 'tinyCsv') {
			alert('Die CSV-Datei hat nur eine Spalte! Falscher Delimiter?');
		}
		else if(res == 'errorNoFile') {
			alert('Es wurde keine Datei hochgeladen!');
		}
		else if(res == 'voidCsv') {
			alert('Es wurden falsche Einstellungen erkannt!');
		}
		else if(res == 'wrongCsvStructure') {
			alert('Die CSV-Struktur ist nicht lesbar!');
		}
		else {
			ret = true;
		}

		return ret;
	}
}

/**
 * Set the preview-Mode off and allow the User to change the Database with
 * the Csv
 */
CsvFileUploader.prototype.previewOff = function() {

	$('.csvUpload #infotext').html('Die Vorschaufunktion ist aus. Das heißt, die CSV-Daten werden sofort auf die Datenbank gespielt falls keine Fehler auftreten sollten.');
}

/**
 * Set the preview-Mode on and allow the User to test if the CSV is
 * correctly read
 */
CsvFileUploader.prototype.previewOn = function() {

	$('.csvUpload #infotext').html('Die Vorschaufunktion ist an. Das heißt, es wird zuerst eine Vorschau beim Dateihochladen erstellt, ohne die Datenbank zu verändern.');
}

interface = new AdminInterface();

interface.messageShow('Hallo! Ich bin Schadenfroh!');