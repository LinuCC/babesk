$(document).ready(function() {

	displayAll();

	function displayAll() {

		var columns = [
			{
				name: 'ID',
				displayName: 'Id',
				isDisplayed: true
			},
			{
				name: 'forename',
				displayName: 'Vorname',
				isDisplayed: true
			},
			{
				name: 'name',
				displayName: 'Nachname',
				isDisplayed: true
			},
			{
				name: 'username',
				displayName: 'Benutzername',
				isDisplayed: true
			},
			{
				name: 'password',
				displayName: 'Passwort',
				isDisplayed: false
			},
			{
				name: 'email',
				displayName: 'Emailadresse',
				isDisplayed: false
			},
			{
				name: 'telephone',
				displayName: 'Telefonnummer',
				isDisplayed: false
			},
			{
				name: 'birthday',
				displayName: 'Geburtstag',
				isDisplayed: false
			},
			{
				name: 'last_login',
				displayName: 'letzter Login',
				isDisplayed: false
			},
			{
				name: 'login_tries',
				displayName: 'Einlogversuche',
				isDisplayed: false
			},
			{
				name: 'first_passwd',
				displayName: 'Ist erstes Passwort',
				isDisplayed: false
			},
			{
				name: 'locked',
				displayName: 'Ist gesperrt',
				isDisplayed: false
			},
			{
				name: 'GID',
				displayName: 'Preisgruppen-Id',
				isDisplayed: false
			},
			{
				name: 'credit',
				displayName: 'Guthaben',
				isDisplayed: false
			},
			{
				name: 'soli',
				displayName: 'Ist Soli',
				isDisplayed: false
			},
			{
				name: 'schoolyears',
				displayName: 'Schuljahre',
				isDisplayed: false
			},
			{
				name: 'grades',
				displayName: 'Klassen',
				isDisplayed: false
			},
			{
				name: 'activeGrade',
				displayName: 'aktive Klasse',
				isDisplayed: false
			}
		];

		var activePage = 1;
		var amountPages = 0;

		columnsToShowUpdate();
		columnToggleDisplayListBuild();
		$('.column-switch').bootstrapSwitch();
		$('[title]').tooltip();
		newDataFetch();

		$('#page-select').on('click', 'a', function(ev) {
			$this = $(this);
			if($this.hasClass('first-page')) {
				var page = 1;
			}
			else if($this.hasClass('last-page')) {
				var page = amountPages;
			}
			else {
				var page = $(this).text();
			}
			newDataFetch(page);
		});

		$('#relative-pager-prev').on('click', function(ev) {
			if(activePage > 1) {
				newDataFetch(activePage - 1);
			}
		});

		$('#relative-pager-next').on('click', function(ev) {
			if(activePage < amountPages) {
				newDataFetch(activePage + 1);
			}
		});

		$('#column-show-form-submit').on('click', function(ev) {
			columnsToShowUpdate();
			newDataFetch();
			$('#table-columns-modal').modal('hide');
		});

		$('#users-per-page').on('keyup', function(ev) {
			if(ev.which == 13) {
				newDataFetch();
			}
		});

		function pageSelectorUpdate(pagecount) {

			amountPages = pagecount;

			var amountSelectorsDisplayed = 9;
			var startPage = activePage - Math.floor(amountSelectorsDisplayed / 2);
			if(startPage < 1) {
				startPage = 1;
			}
			if(activePage == 1) {
				$pager= $(
					'<li class="disabled"><a class="first-page">&laquo;</a></li>'
				);
				$('#relative-pager-prev').addClass('disabled');
			}
			else {
				$pager= $('<li><a class="first-page">&laquo;</a></li>');
				$('#relative-pager-prev').removeClass('disabled');
			}
			for(var i = startPage; i <= pagecount && i < startPage + amountSelectorsDisplayed; i++) {
				if(i == activePage) {
					$pager.append('<li class="active"><a>' + i + '</a></li>');
				}
				else {
					$pager.append('<li><a>' + i + '</a></li>');
				}
			}
			if(activePage == pagecount) {
				$pager.append(
					'<li class="disabled"><a class="last-page">&raquo;</a></li>'
				);
				$('#relative-pager-next').addClass('disabled');
			}
			else {
				$pager.append('<li><a class="last-page">&raquo;</a></li>');
				$('#relative-pager-next').removeClass('disabled');
			}
			$('#page-select').html($pager.outerHtml());
		}

		function columnsToShowUpdate() {
			$.each($('#column-show-form [id^="column-show-"]'), function(ind, el) {
				$el = $(el);
				var name = $el.attr('id').replace('column-show-', '');
				var isActive = $el.bootstrapSwitch('state');
				$.each(columns, function(i, col){
					if(col.name == name) {
						col.isDisplayed = isActive;
					}
				});
			});
		}

		/**
		 * Builds the list allowing the users to choose which columns to display
		 */
		function columnToggleDisplayListBuild() {
			var $colTpl = $('#column-show-template div.form-group');
			var $colList = $('#column-show-form');
			$.each(columns, function(ind, el) {
				var $col = $colTpl.clone();
				$col.children('label').attr('for', 'column-show-' + el.name)
					.html(el.displayName);
				$checkbox = $col.children('div input');
				$checkbox.attr('id', 'column-show-' + el.name);
				if(el.isDisplayed) {
					$checkbox.prop('checked', true);
				}
				if(el.name == 'ID') {
					$checkbox.prop('disabled', true);
				}
				$colList.append($col);
			});
		}

		/**
		 * Fetches userdata from the Server, takes care of filters, sortables etc
		 *
		 * It sends the server information of how to order and filter the users,
		 * also how many users the server is supposed to send and at which user to
		 * start returning them. If successful, the userData-content-Table gets
		 * Rebuild and the active page changed.
		 */
		function newDataFetch(pagenum) {

			if(pagenum == undefined) {
				pagenum = activePage;
			}

			var sortFor = '';
			var filterForColumn = '';
			var filterForValue = '';
			var columnsToFetch = [];
			$.each(columns, function(ind, el) {
				if(el.isDisplayed) {
					columnsToFetch.push(el.name);
				}
			});

			$.ajax({
				type: "POST",
				url: "index.php?module=administrator|System|User|FetchUserdata",
				data: {
					'usersPerPage': $('#users-per-page').val(),
					'pagenumber': pagenum,
					'sortFor': sortFor,
					'filterForCol': filterForColumn,
					'filterForVal': filterForValue,
					'columnsToFetch': columnsToFetch
				},

				success: function(data) {
					console.log(data);
					try {
						data = JSON.parse(data);
					} catch(e) {
						toastr['error']('Konnte die Server-antwort nicht parsen!');
					}
					if(data.value == 'data') {
						activePage = pagenum;
						tableRefresh(data.users);
						pageSelectorUpdate(data.pagecount);
					}
					else if(data.value == 'error') {
						toastr['error'](data.message);
					}
					else {
						toastr['error']('Unbekannte Serverantwort');
					}
				},

				error: function(error) {
					fatalError();
				}
			});
		};

		function tableRefresh(userData) {
			tableClear();
			tableFill(userData);
		};

		function tableClear() {
			$('#user-table').html('<thead></thead><tbody></tbody>');
		}

		function tableFill(userData) {
			//Sets the TableHead
			// var columnHeader = selectedColumnLabelsGet();
			var headRow = '<tr>';
			$.each(userData[0], function(index, columnName) {
				var respectiveColumnEntryArr = $.grep(columns, function(el) {
						return index == el.name;
				});
				if(respectiveColumnEntryArr[0] != undefined) {
					var respectiveColumnEntry = respectiveColumnEntryArr[0];
					headRow += '<th>' + respectiveColumnEntry.displayName + '</th>';
				}
				else {
					headRow += '<th>' + index + '(Not found in columns)' + '</th>';
				}
			});
			headRow += '<th>Optionen</th>';
			headRow += '</tr>';
			$('#user-table thead').append(headRow);

			$settingsColTpl = $('#list-user-settings-template');
			//Sets the TableBody
			$.each(userData, function(index, user) {
				row = String('<tr id="user_{0}">').format(user.ID);
				$.each(user, function(colIndex, column) {
					row += '<td>' + column + '</td>';
				});
				$settingsCol = $settingsColTpl.clone();
				$settingsCol.children('a.user-action-settings')
					.attr('href', 'index.php?module=administrator|System|User|\
						DisplayChange&ID=' + user.ID).attr('title', 'Nutzereinstellungen');
				$settingsCol.children('a.user-action-delete')
					.attr('title', 'Nutzer löschen');
				row += '<td>' + $settingsCol.html() + '</td>';
				row += '</tr>';
				$('#user-table tbody').append(row);

				//refresh tooltips
				$('[title]').tooltip();
			});
		}

	};
});