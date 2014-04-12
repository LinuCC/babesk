$(document).ready(function() {

	(function() {

		var activePage = 1;

		logsFetch();

		$('#log-table tbody').on('click', 'button.log-additional-data-display',
			function(ev) {
				var target = $(ev.currentTarget).attr('data-target');
				var content = $(target).html();
				if(content.length) {
					bootbox.alert(content, function(){});
				}
				else {
					toastr['error']('Zum Log gibt es keine weitere Daten.');
				}
		});

		$('#page-select').on('click', 'li', function(ev) {
			ev.preventDefault();
			if($(ev.currentTarget).hasClass('disabled')) {
				return;
			}
			$button = $(ev.currentTarget).children('a');
			activePage = parseInt($button.attr('pagenum'));
			logsFetch();
		});

		$('#logs-per-page').on('keyup', function(ev) {
			activePage = 1;
			logsFetch();
		});

		$('#filter').on('keyup', function(ev) {
			if(ev.keyCode == 13) {
				activePage = 1;
				logsFetch();
			}
		});

		$('#category-select, #severity-select').on('change', function(ev) {
			activePage = 1;
			logsFetch();
		});

		function logsFetch() {

			tableLoadingStatus(true);
			request();

			function request() {

				var selectorsFetch = (
					$('#severity-select').children().length == 0 &&
					$('#category-select').children().length == 0
				);
				$.postJSON(
					'index.php?module=administrator|System|Logs|ShowLogs',
					{
						'getData': true,
						'fetchCategories': selectorsFetch,
						'fetchSeverities': selectorsFetch,
						'activePage': activePage,
						'logsPerPage': $('#logs-per-page').val(),
						'filter': $('#filter').val(),
						'category': $('#category-select option:selected').val(),
						'severity': $('#severity-select option:selected').val()
					},
					update
				);
			}

			function tableLoadingStatus(val) {
				if(val) {
					$('#log-table').animate({opacity: 0.5}, 50);
				}
				else {
					$('#log-table').animate({opacity: 1}, 50);
				}
			}

			function tableClear() {
				$('#log-table tbody').html('');
			}

			function update(res) {
				if(res.state == "success") {
					tableLoadingStatus(false);
					tableClear();
					logsToTable(res.data.logs);
					paginationUpdate(res.data.count);
					tableSizeUpdate(parseFloat(res.data.tableSize));
					if(typeof res.data.severities != 'undefined') {
						severitiesUpdate(res.data.severities);
					}
					if(typeof res.data.categories != 'undefined') {
						categoriesUpdate(res.data.categories);
					}
				}
				else {
					toastr['error'](res.data, 'Fehler beim Abrufen der Logs');
				}
			}

			function tableSizeUpdate(size) {
				if(size != false) {
					$('#table-size').text(size);
					if(size > 50) {
						toastr['info'](
							'Die Logs nehmen viel (' + size + ' Mb) Speicher ein.\
							Es wird empfohlen, unwichtige Logs zu löschen',
							'Viel Speicher'
						);
						if(size > 500) {
							$('#table-size-container span.label')
								.removeClass('label-success')
								.addClass('label-danger');
						}
						else {
							$('#table-size-container span.label')
								.removeClass('label-success')
								.addClass('label-warning');
						}
					}
				}
				else {
					toastr['error']('Konnte die Tabellengröße nicht abrufen.');
				}
			}

			function logsToTable(logs) {
				//update table
				var $tablebody = $('#log-table tbody');
				var templHtml = $('#logRowTemplate').html();
				$.each(logs, function(ind, logData) {
					logData['additionalData'] = JSON.stringify(
						logData['additionalData']
					);
					var colHtml = microTmpl(templHtml, logData);
					$tablebody.append(colHtml);
				});
			}

			function paginationUpdate(logsCount) {
				var perPages = $('#logs-per-page').val();
				var pageCount = Math.floor(logsCount / perPages);
				if(pageCount == 0) {
					pageCount = 1;
				}
				var data = {
					"minPage": (activePage > 3) ? activePage - 3 : 1,
					"maxPage": (activePage < pageCount - 3) ? activePage + 3 : pageCount,
					"pageCount": pageCount,
					"activePage": activePage
				};
				var pagHtml = microTmpl($('#logPaginationTemplate').html(), data);
				$('#page-select').html(pagHtml);
			}

			function severitiesUpdate(sev) {
				selectorUpdate(sev, '#severity-select');
			}

			function categoriesUpdate(cat) {
				selectorUpdate(cat, '#category-select');
			}

			function selectorUpdate(elements, container) {
				$container = $(container);
				$container.html('<option value="0">Nicht filtern</option>');
				$.each(elements, function(ind, el) {
					$container.append('<option value="' + ind +'">' + el + '</option>');
				});
			}
		}

	})();
});