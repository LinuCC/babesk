$(document).ready(function() {

	logsFetch();

	$('#log-table tbody').on('click', 'button.log-additional-data-display',
		function(ev) {
			console.log(ev);
			var target = $(ev.currentTarget).attr('data-target');
			console.log(target);
			var content = $(target).html();
			if(content.length) {
				bootbox.alert(content, function(){});
			}
			else {
				toastr['error']('Zum Log gibt es keine weitere Daten.');
			}
	});

	function logsFetch() {

		$.postJSON(
			'index.php?module=administrator|System|Logs|ShowLogs',
			{
				'getData': true,
				'logsPerPage': $('#logs-per-page').val()
			},
			logsToTable
		);

		function logsToTable(res) {
			console.log(res);
			if(res) {
				var $tablebody = $('#log-table tbody');
				var templHtml = $('#logRowTemplate').html();
				$.each(res.data.logs, function(ind, logData) {
					var colHtml = microTmpl(templHtml, logData);
					$tablebody.append(colHtml);
				});
				// $('.collapse').collapse({'toggle': false});
			}
			else {
				toastr['error'](res.data, 'Fehler beim Abrufen der Logs');
			}
		}
	}

});