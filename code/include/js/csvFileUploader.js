/**
 * Some general functions allowing to import Data as Csv
 */

$(document).ready(function() {

	csvFileUploader = function (isPreview) {

		var upSelf = this;
		upSelf.isPreview = isPreview;
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
				checkForErrors(data.result);
				res = JSON.parse(data.result);
				viewUpdate(res);
			},

			change: function(e, data) {
				/*we want the csvLimiter selected at the time the File gets
				uploaded, not the time this object gets constructed*/
				$('#csvFileupload').fileupload(
					'option',
					'formData',
					{
						"isPreview": true,
						"csvDelimiter": $('#csvDelimiter').val()
					}
					);
			},

			error: function (e, data) {
				alert('yey');
				console.log(data);
			}
		});

		/**
		 * Updates the content of various Containers of the Browser
		 * @param  {Array} res The result from the Server
		 */
		function viewUpdate(res) {

			errorViewUpdate(res);
			$('.preview p').html(res.preview);
			csvColumnsViewUpdate(res.csvColumns);
		}

		function errorViewUpdate(res) {

			if(res.errors != '') {
				$('.error p').html(res.errors);
			}
			else {
				$('.error p').html('Keine Fehler gefunden');
			}
			$('.errorCount').html(res.errorCount);
		}

		function csvColumnsViewUpdate(res) {

			var input = 'Bitte anwählen, welche Spalten leergelassen werden können<br />';
			console.log(res);
			for(var column in res) {
				var str = '<label><input type="checkbox" name="allowedVoidFields" value="' + res[column] + '" />'
					+ res[column] + '</label><br />';
				input += str;
			}
			if(input != '') {
				$('.fieldSettings p').html(input);
			}
			else {
				$('.fieldSettings p').html("Es konnten keine Felder gefunden werden?!");
			}
		}

		function checkForErrors(res) {
			if(res == 'voidCsv') {
				alert('In der CSV-Datei konnten keine Daten gefunden werden!');
			}
			if(res == 'tinyCsv') {
				alert('Die CSV-Datei hat nur eine Spalte! Falscher Delimiter?');
			}
			if(res == 'errorNoFile') {
				alert('Es wurde keine Datei hochgeladen!');
			}
			if(res == 'voidCsv') {
				alert('Es wurden falsche Einstellungen erkannt!');
			}
			if(res == 'wrongCsvStructure') {
				alert('Die CSV-Struktur ist nicht lesbar!');
			}
		}
	}

});
