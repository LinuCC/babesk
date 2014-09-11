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
				name: 'cardnumber',
				displayName: 'Kartennummer',
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
			},
			{
				name: 'religion',
				displayName: 'Religionen',
				isDisplayed: false
			},
			{
				name: 'foreign_language',
				displayName: 'Fremdsprachen',
				isDisplayed: false
			},
			{
				name: 'special_course',
				displayName: 'Oberstufenkurse',
				isDisplayed: false
			}
		];

		var activePage = 1;
		var amountPages = 0;
		var colToSort = {};
		var sortMethod = 'ASC';
		//All special_course s
		var specialCourses = [];
		var foreignLanguages = [];
		var religions = [];

		columnsToShowSetByCookies();
		$('#search-select-menu').multiselect({
			buttonContainer: '<div class="btn-group" />',
			includeSelectAllOption: true,
			buttonWidth: 150,
			selectAllText: 'Alle auswählen',
			numberDisplayed: 1
		});
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

		// When searching or entering a new row-count, refresh on enter
		$('#users-per-page, #filter').on('keyup', function(ev) {
			activePage = 1;   //Reset pagenumber to 1
			ev.preventDefault();
			if(ev.which == 13) {
				newDataFetch();
			}
		});

		$('#search-submit').on('click', function(ev) {
			activePage = 1;   //Reset pagenumber to 1
			newDataFetch();
		});

		$('#user-table').on('click', 'thead th > a.column-header', function(ev) {
			var $link = $(ev.target);
			var columnName = $link.text();
			//Get column-entry that fits the clicked column-header
			var column = $.grep(columns, function(el, ind) {
				if(el.displayName == columnName) {
					return el;
				}
			})[0];
			if(colToSort.name == column.name) {
				//Change sorting method
				sortMethod = (sortMethod == 'ASC') ? 'DESC' : 'ASC';
			}
			else {
				//Change column to sort
				sortMethod = 'ASC';
				colToSort = column;
			}
			newDataFetch();
		});

		$('#user-table').on('click', '#user-checkbox-global',
			function(ev) {
				var checkboxes = $('.user-checkbox');
				checkboxes.prop('checked', $('#user-checkbox-global').prop('checked'));
		});

		$('#user-table').on('click', '.user-action-delete', function(ev) {
			var userId = $(this).closest('tr').attr('id').replace('user_', '');
			bootbox.dialog({
				message: 'Wollen sie den Schüler wirklich löschen?',
				buttons: {
					danger: {
						label: "Ja",
						className: "btn-danger",
						callback: function() {
							deleteUser(userId);
						}
					},
					default: {
						label: "Abbrechen",
						className: "btn-primary",
						callback: null
					},
				}
			});
		});

		$('#user-table').on('click', '.foreign-language-checkbox', function(ev) {
			var $checkbox = $(ev.target);
			console.log($checkbox.prop('checked'));
			$.postJSON(
				'index.php?module=administrator|System|User|DisplayAll&setForeignLanguage',
				{
					'inForeignLanguage': $checkbox.prop('checked'),
					'foreignLanguage': $checkbox.attr('foreignLanguage'),
					'userId': $checkbox.closest('tr').attr('userid')
				},
				function(res) {
					console.log(res);
					if(res['state'] == 'error') {
						toastr.error(res['data']);
					}
					if(res['value'] == 'error') {
						toastr.error(res['message']);
					}
				}
			);
		});
		$('#user-table').on('click', '.religion-checkbox', function(ev) {
			var $checkbox = $(ev.target);
			console.log($checkbox.prop('checked'));
			$.postJSON(
				'index.php?module=administrator|System|User|DisplayAll&setReligion',
				{
					'inReligion': $checkbox.prop('checked'),
					'religion': $checkbox.attr('religion'),
					'userId': $checkbox.closest('tr').attr('userid')
				},
				function(res) {
					console.log(res);
					if(res['state'] == 'error') {
						toastr.error(res['data']);
					}
					if(res['value'] == 'error') {
						toastr.error(res['message']);
					}
				}
			);
		});
		$('#user-table').on('click', '.special-course-checkbox', function(ev) {
			var $checkbox = $(ev.target);
			console.log($checkbox.prop('checked'));
			$.postJSON(
				'index.php?module=administrator|System|User|DisplayAll&setSpecialCourse',
				{
					'inCourse': $checkbox.prop('checked'),
					'specialCourse': $checkbox.attr('specialcourse'),
					'userId': $checkbox.closest('tr').attr('userid')
				},
				function(res) {
					console.log(res);
					if(res['state'] == 'error') {
						toastr.error(res['data']);
					}
					if(res['value'] == 'error') {
						toastr.error(res['message']);
					}
				}
			);
		});

		$('#user-table').on('click', 'tbody > tr', function(ev) {

			var $target = $(ev.target);
			//If a button or input was clicked and its not the selection-checkbox,
			//dont select the user
			if($target.filter('button,input,a').not('input.user-checkbox').length) {
				return;
			}
			var $this = $(this);
			var $box = $this.children('td').children('input[type="checkbox"]');
			//Dont toggle checkbox two times if checkbox is clicked
			if(!$target.is('input[type="checkbox"]')) {
				$box.prop('checked', !$box.prop('checked'));
			}
			$this.toggleClass('selected');
			$('button#selected-action-button').removeClass('btn-default')
				.addClass('btn-warning');
		});

		$('#selected-action-button').on('click', function(ev) {
			$.post(
				'index.php?module=administrator|System|User|DisplayAll|Multiselection|ActionsGet',
				{},
				function(res) {
					$('#multiselection-actions-container').html(res);
				},
				'html'
			);
		});

		function deleteUser(userId) {

			$.ajax({
				type: "POST",
				url: 'index.php?module=administrator|System|User|Delete&ID={0}'.format(userId),
				data: {
				},
				success: function(data) {
					try {
						data = JSON.parse(data);
					} catch(e) {
						toastr['error']('Konnte die Serverantwort nicht parsen.');
					}
					if(data.value == 'success') {
						toastr['success']('Benutzer erfolgreich gelöscht.');
						userDeletePdfEntryAdd(data.pdfId, data.forename, data.name);
						newDataFetch();
					}
					else if(data.value == 'error') {
						toastr['error'](data.message);
					}
					else {
						toastr['error']('Konnte die Serverantwort nicht lesen.');
					}
				},

				error: function(error) {
					toastr['error']('Konnte den Server nicht erreichen.');
				}
			});
		}

		/**
		 * When a user got deleted, allow the admin to download a created pdf
		 */
		function userDeletePdfEntryAdd(pdfId, forename, name) {

			$contentParent = $('#deleted-user-pdf-form');
			// $newEntry = $('#deleted-user-pdf-template').clone();

			//if there is the yet-no-users-deleted Message, remove it
			if($contentParent.find('p.no-users-deleted').length != 0) {
				$contentParent.html('');
				$('#deleted-user-pdf-modal-btn')
					.removeClass('btn-default')
					.addClass('btn-warning');
			}

			var newEntryHtml = microTmpl(
				$('#deleted-user-pdf-template').html(),
				{
					"pdfId": pdfId,
					"forename": forename,
					"name": name
				}
			);

			$contentParent.append(newEntryHtml);
		}

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
				$pager= $('<li><a href="#" class="first-page">&laquo;</a></li>');
				$('#relative-pager-prev').removeClass('disabled');
			}
			for(var i = startPage; i <= pagecount && i < startPage + amountSelectorsDisplayed; i++) {
				if(i == activePage) {
					$pager.append('<li class="active"><a href="#">' + i + '</a></li>');
				}
				else {
					$pager.append('<li><a href="#">' + i + '</a></li>');
				}
			}
			if(activePage == pagecount) {
				$pager.append(
					'<li class="disabled"><a href="#" class="last-page">&raquo;</a></li>'
				);
				$('#relative-pager-next').addClass('disabled');
			}
			else {
				$pager.append('<li><a href="#" class="last-page">&raquo;</a></li>');
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
			columnsToSearchSelectUpdate();
		}

		function columnsToSearchSelectUpdate() {
			var $menu = $('#search-select-menu');
			$menu.html('');
			$.each(columns, function(ind, col) {
				if(col.isDisplayed) {
					$menu.append(
						'<option value="' + col.name + '" selected>' +
						col.displayName + '</option>'
					);
				}
			});
			$menu.multiselect('rebuild');
			$('button.multiselect').attr('title', 'Zu durchsuchende Spalten');
		}

		function columnsToSearchSelectedGet() {
			var filterForColumns = $('#search-select-menu').val();
			//Handle select-all checkbox; we do not need to know it is selected
			var pos = $.inArray('multiselect-all', filterForColumns);
			if(pos > -1) {
				filterForColumns.splice(pos, 1);
			}
			return filterForColumns;
		}

		function columnsToShowSetByCookies() {

			$.each(columns, function(ind, el) {
				var cookieVal = $.cookie('UserlistColumnDisplay' + el['name']);
				if(cookieVal != 'undefined' && cookieVal != undefined) {
					columns[ind]['isDisplayed'] = (cookieVal == 'true') ? true : false;
				}
			});
		}

		function cookiesSetByColumnsToShow() {

			$.each(columns, function(ind, el) {
				$.cookie('UserlistColumnDisplay' + el['name'], el['isDisplayed']);
			});
		}

		/**
		 * Builds the list allowing the users to choose which columns to display
		 */
		function columnToggleDisplayListBuild() {
			var $colList = $('#column-show-form');
			$.each(columns, function(ind, el) {
				var colHtml = microTmpl($('#column-show-template').html(), el);
				$colList.append(colHtml);
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

			if(!specialCourses.length) {
				getAllSpecialCourses();
			}
			if(!religions.length) {
				getAllReligions();
			}
			if(!foreignLanguages.length) {
				getAllForeignLanguages();
			}

			if(pagenum == undefined) {
				pagenum = activePage;
			}

			if(colToSort.name !== 'undefined' && colToSort.name !== undefined) {
				var sortFor = colToSort.name;
			}
			else {
				var sortFor = '';
			}
			var filterForColumns = columnsToSearchSelectedGet();
			var filterForValue = $('#filter').val();
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
					'sortMethod' : sortMethod,
					'filterForVal': filterForValue,
					'filterForColumns': filterForColumns,
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
			cookiesSetByColumnsToShow();
		};

		function tableClear() {
			$('#user-table').html('<thead></thead><tbody></tbody>');
		}

		/**
		 * Fills the Userstable with data
		 * @param  {Object} userData The data with which to fill the table
		 */
		function tableFill(userData) {
			//Sets the TableHead
			// var columnHeader = selectedColumnLabelsGet();
			var headRow = '<tr><th><input id="user-checkbox-global" type="checkbox" /></th>';
			var specialCoursePos = -1;
			var religionPos = -1;
			var foreignLanguagePos = -1;
			if(userData.length != 0){
				$.each(userData[0], function(index, columnName) {
					if(index == 'special_course') {
						specialCoursePos = index;
					}
					if(index == 'foreign_language') {
						foreignLanguagePos = index;
					}
					if(index == 'religion') {
						religionPos = index;
					}
					var respectiveColumnEntryArr = $.grep(columns, function(el) {
							return index == el.name;
					});
					// Compare columns given by the server with all possible columns
					if(respectiveColumnEntryArr[0] != undefined) {
						var respectiveColumnEntry = respectiveColumnEntryArr[0];
						headRow += '<th><a href="#" class="column-header">'
							+ respectiveColumnEntry.displayName
							+ '</a></th>';
					}
					else {
						headRow += '<th>' + index + '(Not found in columns)' + '</th>';
					}
				});
				headRow += '<th>Optionen</th>';
				headRow += '</tr>';
				$('#user-table thead').append(headRow);

				//Sets the TableBody
				$.each(userData, function(index, user) {
					row = String(
							'<tr id="user_{0}" userid="{0}">\
								<td>\
									<input class="user-checkbox" user-id="{0}" type="checkbox"/>\
								</td>'
						).format(user.ID);
					$.each(user, function(colIndex, column) {
						if(colIndex == specialCoursePos) {
							column = special_course(column);
						}
						if(colIndex == foreignLanguagePos) {
							column = foreign_language(column);
						}
						if(colIndex == religionPos) {
							column = religion(column);
						}
						row += '<td>' + column + '</td>';
					});
					var settingsColHtml = microTmpl(
						$('#list-user-settings-template').html(),
						user
					);
					row += settingsColHtml;
					row += '</tr>';
					$('#user-table tbody').append(row);
					//refresh tooltips
					$('[title]').tooltip();

				});
			}
		}

		/**
		 * Allows direct editing of the special courses
		 * @todo  HOTFIX - rework display-all and make it better!
		 */
		function special_course(courseStr) {
			var courses = courseStr.split("|");
			var newCourseStr = '';
			var $courseSel = $(
				'<span class="special-course-selector"><div></div>\
					<input type="checkbox" class="special-course-checkbox"></span>'
			);
			var $checkedCourseSel = $courseSel.clone();
			$checkedCourseSel.find('input').attr('checked', true);
			$.each(specialCourses, function(ind, el) {
				if($.inArray(el, courses) != -1) {
					var $content = $checkedCourseSel.clone();
				}
				else {
					var $content = $courseSel.clone();
				}
				$content.find('input').attr('specialcourse', el);
				$content.find('div').html(el);
				newCourseStr += $content.outerHtml();
			});
			return newCourseStr;
		}

		function getAllSpecialCourses() {
			jQuery.ajaxSetup({async:false});
			$.postJSON(
				'index.php?module=administrator|System|User\
					&getAllSpecialCourses',
				{},
				function(res) {
					if(res['state'] == 'success') {
						specialCourses = res['data'];
					}
					else {
						toastr[res['state']](res['data']);
					}
				}
			);
			jQuery.ajaxSetup({async:true});
		}
		/**
		 * Allows direct editing of the foreign languages
		 * @todo  HOTFIX - rework display-all and make it better!
		 */
		function foreign_language(langStr) {
			var languages = langStr.split("|");
			var newLangStr = '';
			var $langSel = $(
				'<span class="foreign-language-selector"><div></div>\
					<input type="checkbox" class="foreign-language-checkbox"></span>'
			);
			var $checkedLangSel = $langSel.clone();
			$checkedLangSel.find('input').attr('checked', true);
			$.each(foreignLanguages, function(ind, el) {
				if($.inArray(el, languages) != -1) {
					var $content = $checkedLangSel.clone();
				}
				else {
					var $content = $langSel.clone();
				}
				$content.find('input').attr('foreignlanguage', el);
				$content.find('div').html(el);
				newLangStr += $content.outerHtml();
			});
			return newLangStr;
		}

		function getAllForeignLanguages() {
			jQuery.ajaxSetup({async:false});
			$.postJSON(
				'index.php?module=administrator|System|User\
					&getAllForeignLanguages',
				{},
				function(res) {
					if(res['state'] == 'success') {
						foreignLanguages = res['data'];
					}
					else {
						toastr[res['state']](res['data']);
					}
				}
			);
			jQuery.ajaxSetup({async:true});
		}
		/**
		 * Allows direct editing of the religions
		 * @todo  HOTFIX - rework display-all and make it better!
		 */
		function religion(religionStr) {
			var givenReligions = religionStr.split("|");
			var newReligionStr = '';
			var $religionSel = $(
				'<span class="religion-selector"><div></div>\
					<input type="checkbox" class="religion-checkbox"></span>'
			);
			var $checkedReligionSel = $religionSel.clone();
			$checkedReligionSel.find('input').attr('checked', true);
			$.each(religions, function(ind, el) {
				if($.inArray(el, givenReligions) != -1) {
					var $content = $checkedReligionSel.clone();
				}
				else {
					var $content = $religionSel.clone();
				}
				$content.find('input').attr('religion', el);
				$content.find('div').html(el);
				newReligionStr += $content.outerHtml();
			});
			return newReligionStr;
		}

		function getAllReligions() {
			jQuery.ajaxSetup({async:false});
			$.postJSON(
				'index.php?module=administrator|System|User\
					&getAllReligions',
				{},
				function(res) {
					if(res['state'] == 'success') {
						religions = res['data'];
					}
					else {
						toastr[res['state']](res['data']);
					}
				}
			);
			jQuery.ajaxSetup({async:true});
		}

		/**
		 * Returns an array of ids of by multiselection selected users
		 */
		function getIdsOfSelectedUsers() {
			return $('input.user-checkbox:checked')
				.map(function() {return $(this).attr('user-id')}).get();
		}

		/**
		 * Contains functions to handle the multiselection-actions
		 */
		(function() {
			$('#multiselection-actions-container').on(
				'click', '.multiselection-action-submit',
				function(ev) {
					var $container = $(ev.currentTarget)
						.closest(".multiselection-action-view");
					var data = compileInput($container);
					data['_multiselectionSelectedOfUsers'] = getIdsOfSelectedUsers();
					$.postJSON(
						'index.php?module=administrator|System|User|DisplayAll|\
						Multiselection|ActionExecute',
						data,
						onExecuteSuccess
					);
				}
			);

			/**
			 * Searches for all inputs in the container and puts values into Object
			 * @return {Object} The Object containing all input- and select-data
			 */
			function compileInput($inputContainer) {
				var inputData = {};
				//Load all Inputs
				$.each($inputContainer.find('input'), function(ind, input) {
					inputData[$(input).attr('name')] = $(input).val();
				});
				//Load all Selects
				$.each($inputContainer.find('select'), function(ind, select) {
					var selectedOptions = $(select).find('option:selected');
					var selOptions = [];
					//Load every selected option of the select
					$.each(selectedOptions, function(opInd, option) {
						selOptions.push($(option).val());
					});
					inputData[$(select).attr('name')] = selOptions;
				});
				return inputData;
			}

			function onExecuteSuccess(res) {
				console.log(res.value);
				toastr[res.value](res.message);
				$('#multiselection-actions-modal').modal('hide');
				newDataFetch();
			}

		})();

	};
});
