
$(document).ready(function() {

	$("div.pageSelect").buttonset();
	$(document).tooltip();

	/**
	 * Hiding the settings, filters, sortables etc
	 */
	$('.accordion').accordion({
		collapsible: true,
		heightStyle:"content",
		active: 0 // Let user choose the ColumnsToShow first
	});

	/**
	 * The Inputfield has two Arrows on the right, for incrementing and
	 * decrementing the number in it
	 */
	$('#usersPerPage').spinner({
		min: 10,
		max: 500,
		step: 10,
		start: 10
	});

	/**
	 *
	 */
	$(".tabs").tabs();
	$( ".tabs li" ).addClass( "ui-corner-all" );

	$('table.users.dataTable').on('click', 'a.deleteUser', function(ev) {

		ev.preventDefault();
		$(this).attr('target', '_blank');
		toDelete = $(this).attr('id').replace('deleteUser_', '');

		$('body').prepend('<div id="deleteConfirm" title="Benutzer wirklich löschen?"><p><span class="ui-icon ui-icon-alert" style="float: left; margin: 0 7px 20px 0;""></span>Der Benutzer wird dauerhaft gelöscht. Sind sie wirklich sicher?</p></div>');

		$( "#deleteConfirm" ).dialog({
			resizable: false,
			height: 240,
			modal: true,
			buttons: {
				"Benutzer löschen": function() {
					deleteAjax(toDelete);
					$(this).dialog("close");
				},
				"doch nicht": function() {
					$(this).dialog("close");
				}
			}
		});
	});

	var deleteAjax = function(userId) {

		$.ajax({
			type: "POST",
			url: 'index.php?module=administrator|System|User|Delete&ID={0}'.format(userId),
			data: {
			},
			success: function(data) {

				try {
					data = JSON.parse(data);
				} catch(e) {
					adminInterface.errorShow(data);
				}
				if(data.value == 'success') {
					adminInterface.successShow(data.message);
					userDeletePdf(data.pdfId, data.forename, data.name);
					newDataFetch();
				}
				else if(data.value == 'error') {
					adminInterface.errorShow(data.message);
				}
				else {
					fatalError();
				}
			},

			error: function(error) {
				fatalError();
			}
		});
	}

	/**
	 * R.I.P. Appendix - Function in remembrance of those nice slide-ups
	 */
	$('table.users.dataTable').on('dblclick', function(ev) {
		$('table.users.dataTable').effect('shake');
	});

	$('table.users.dataTable').on('click', '[id^=actionDetailsForUser#]',
		function(ev) {
			var userId = $(this).attr('id')
				.replace('actionDetailsForUser#', '');
			var newContent = $(String(
				'<div class="userOptions"><a href="index.php?' +
				'module=administrator|System|User|DisplayChange&ID={0}" ' +
				'title="ändern"><img src="../images/status/edit_16.png"/>' +
				'</a><a id="deleteUser_{0}" href="#" class="deleteUser" ' +
				'target="_blank" title="löschen">' +
				'<img src="../images/status/delete.png"/></a></div>')
					.format(userId));

			tableentry = $(this).parent();
			tableentry.html(newContent);
	});

	/**
	 * Fetch new data and refresh the Table when different columns got
	 * selected to be displayed
	 */
	$('div#columnsToShowWrapper').on('change', 'input.columnSelect',
		function(ev) {
		newDataFetch();
		cols = selectedColumnIdsGet();
		$.cookie('selectedDisplayColumns', cols.join(','));
	});

	$('div.filter').on('change', 'input.columnFilter', function(ev) {

		$('.pageSelect input:checked').prop('checked', '');
		$('.pageSelect input#pageSelectFirst').prop('checked', 'checked');
		newDataFetch();
	});

	$('div.filter').on('click', 'input#filterSubmit', function(ev) {

		$('.pageSelect input:checked').prop('checked', '');
		$('.pageSelect input#pageSelectFirst').prop('checked', 'checked');
		newDataFetch();
	});

	$('div.sort').on('change', 'input.columnSort', function(ev) {

		$('.pageSelect input:checked').prop('checked', '');
		$('.pageSelect input#pageSelectFirst').prop('checked', 'checked');
		newDataFetch();
	});

	$('input#refreshPage').on('click', function(ev) {

		event.preventDefault();
		newDataFetch();
	});

	/**
	 * Fetch new data and refresh the Table when another page was selected
	 */
	$('div.pageSelect').on('change', function(ev) {
		newDataFetch();
	});

	/**
	 * Displays a fatal error
	 */
	var fatalError = function() {
		adminInterface.errorShow('Ein unbekannter Fehler ist aufgetreten!');
	};

	/**
	 * returns the current active page
	 */
	var currentPagenumberGet = function() {

		var selector = $('.pageSelect input:checked');
		var pagenum = 0;

		if($(selector).attr('id') == 'pageSelectFirst') {
			pagenum = 1;
		}
		else if($(selector).attr('id') == 'pageSelectLast') {
			pagenum = amountPages;
		}
		else {
			pagenum = ($('.pageSelect input:checked').val())
				? $('.pageSelect input:checked').val() : 1;
		}

		return pagenum;
	};

	var firstPageSelect = function() {

		$('.pageSelect input:checked').removeProp('checked', '');
		$('.pageSelect input#pageSelect1').prop('checked', 'checked');
		newDataFetch();
	}

	/**
	 * Fetches userdata from the Server, takes care of filters, sortables etc
	 *
	 * It sends the server information of how to order and filter the users,
	 * also how many users the server is supposed to send and at which user to
	 * start returning them. If successful, the userData-content-Table gets
	 * Rebuild and the active page changed.
	 */
	var newDataFetch = function() {

		var pagenum = currentPagenumberGet();
		var sortFor = valueSortForGet();
		var filterForColumn = valueFilterForGet();
		var filterForValue = $('input#filterInput').val();
		if(!filterForValue || !filterForValue) {
			filterForValue = '';
			filterForColumn = '';
		}

		$.ajax({
			type: "POST",
			url: "index.php?module=administrator|System|User|FetchUserdata",
			data: {
				'usersPerPage': $('#usersPerPage').val(),
				'pagenumber': pagenum,
				'sortFor': sortFor,
				'filterForCol': filterForColumn,
				'filterForVal': filterForValue,
				'columnsToFetch': selectedColumnIdsGet()
			},

			success: function(data) {

				try {
					data = JSON.parse(data);
				} catch(e) {
					adminInterface.errorShow(data);
				}


				if(data.value == 'data') {
					activePage = pagenum;
					tableRefresh(data.users);
					pageSelectorUpdate(data.pagecount);
				}
				else if(data.value == 'error') {
					adminInterface.errorShow(data.message);
					fatalError();
				}
				else {
					fatalError();
				}
			},

			error: function(error) {
				fatalError();
			}
		});
	};

	var valueSortForGet = function() {

		var toSortForId = $('input[name="columnSort"]:checked').attr('id');
		var toSortFor = '';

		if($.type(toSortForId) == 'string') {
			var toSortFor = toSortForId.replace('sort_', '');
		}

		return toSortFor;
	}

	var valueFilterForGet = function() {

		var filterForId = $('input[name="columnFilter"]:checked').attr('id');
		var filterFor = '';

		if($.type(filterForId) == 'string') {
			var filterFor = filterForId.replace('filter_', '');
		}

		return filterFor;
	}

	var tableRefresh = function(data) {
		tableClear();
		tableFillByUserdata(data);
	};

	/**
	 * Fetches the existing Columns from the Server and rebuilds the Interface
	 *
	 * Calls existingColumnsCreateMenu to rebuild the menu
	 */
	var existingColumnsSet = function() {

		$.ajax({
			type: "POST",
			url: "index.php?module=administrator|System|User|FetchUsercolumns",
			data: {
			},

			success: function(data) {

				try {
					data = JSON.parse(data);
				} catch(e) {
					adminInterface.errorShow(data);
				}
				if(data.value == 'data') {
					existingColumnsCreateMenu(data.message);
					existingColumnsCreateFilter(data.message);
					existingColumnsCreateSortables(data.message);
				}
				else {
					fatalError();
				}
			},

			error: function(error) {
				fatalError();
			}
		});
	}

	/**
	 * Creates the Menu where you can select what columns will be displayed
	 * @param  {Array} data An Array of all Columns that can be displayed
	 */
	var existingColumnsCreateMenu = function(data) {

		var menuButton = String('<label for="{0}">{1}</label>' +
				'<input type="checkbox" class="columnSelect" id="{0}"' +
				'name="columnSelect" />');
		var maxButtonsOnOneLine = 4;
		var rowHelper;
		var counter = 0;
		var colsPreselected = $.cookie('selectedDisplayColumns');
		if(colsPreselected != undefined) {
			var columnsToPreselect = colsPreselected.split(',');
		}
		var buttons = Array();

		$.each(data, function(colId, colName) {
			var button = $(menuButton.format(colId, colName));
			if($.inArray(colId, columnsToPreselect) != -1) {
				button.prop('checked', 'checked');
			}
			buttons.push(button);
		});

		$("div#columnsToShowWrapper").html('');
		$.each(buttons, function(index, button) {
			if(counter == 0) {
				rowHelper = $('<div class="columnsToShow blueButtons"></div>');
			}
			rowHelper.append(button)
			counter += 1;
			if(counter >= 4) {
				$("div#columnsToShowWrapper").append(rowHelper);
				counter = 0;
			}
		});
		$("div.columnsToShow").buttonset();
	}

	var existingColumnsCreateFilter = function(data) {

		var filterButton = String('<label for="filter_{0}">{1}</label>' +
			'<input type="radio" class="columnFilter" id="filter_{0}"' +
				'name="columnFilter" />');
		//the Div every row of filter-Buttons is in
		var rowDiv = '<div class="filterRow blueButtons">';
		var maxButtonsOnOneLine = 4;
		var counter = 0;
		var html = '';

		html += rowDiv;
		$.each(data, function(colId, colName) {
			if(counter >= 4) {
				html += '</div>' + rowDiv;
				counter = 0;
			}
			html += filterButton.format(colId, colName);
			counter += 1;
		});
		html += '</div>';
		html += '<label for="filterInput">...nach filtern:</label>' +
			'<input type="text" id="filterInput" />';
		html += '<input type="button" id="filterSubmit" value="Filtern" />';
		$('div.filter').html(html);
		$('div.filterRow').buttonset();
	}

	var existingColumnsCreateSortables = function(data) {

		var sortButton = String('<label for="sort_{0}">{1}</label>' +
			'<input type="radio" class="columnSort" id="sort_{0}"' +
				'name="columnSort" />');
		//the Div every row of filter-Buttons is in
		var rowDiv = '<div class="sortRow blueButtons">';
		var maxButtonsOnOneLine = 4;
		var counter = 0;
		var html = '';

		html += rowDiv;
		$.each(data, function(colId, colName) {
			if(counter >= 4) {
				html += '</div>' + rowDiv;
				counter = 0;
			}
			html += sortButton.format(colId, colName);
			counter += 1;
		});
		html += '</div>';
		$('div.sort').html(html);
		$('div.sortRow').buttonset();
	}


	/**
	 * Resets the content-table
	 */
	var tableClear = function() {
		$('table.users').html('<thead></thead><tbody></tbody>');
	};

	/**
	 * fills the Content-Table with Userdata fetched from the Server
	 *
	 * It rebuilds the tablehead and the tablebody.
	 *
	 * @param  {Array} userData The Userdata fetched from the server
	 */
	var tableFillByUserdata = function(userData) {

		var columnsToShow = $.map($('input.columnSelect:checked'), function(el) {
			return $(el).attr('id');
		});
		var tablebody = $('table.users tbody');
		var tablehead = $('table.users thead');

		//Sets the TableHead
		var columnHeader = selectedColumnLabelsGet();
		var headRow = '<tr>';
		$.each(columnHeader, function(index, columnName) {
			headRow += '<th>' + columnName + '</th>';
		});
		headRow += '</tr>';
		tablehead.append(headRow);

		//Sets the TableBody
		$.each(userData, function(index, user) {
			row = String('<tr id="user_{0}">').format(user.ID);
			$.each(columnsToShow, function(colIndex, column) {
				row += '<td>' + user[column] + '</td>';
			});
			row += '<td class="userOptions"><a id="actionDetailsForUser#' +
				user.ID + '"><img src="../images/actions/settings_24.png"\
				title="Optionen"/><a/></td>';
			row += '</tr>';
			tablebody .append(row);
		});
	};

	var selectedColumnLabelsGet = function() {
		var columns = $.map($('input.columnSelect:checked'), function(el) {
			return $('label[for="' + $(el).attr('id') + '"]').text();
		});

		return columns;
	}

	var selectedColumnIdsGet = function() {
		var columns = $.map($('input.columnSelect:checked'), function(el) {
			return $(el).attr('id');
		});

		return columns;
	}

	/**
	 * Updates the Page-Selector so that it shows the correct count of pages
	 */
	var pageSelectorUpdate = function(pagecount) {

		amountPages = pagecount;
		var setActivePageSelector = function() {
			$('input#pageSelect' + activePage).attr('checked', 'checked');
		}

		var p = 0; //which pageSelectors to display
		var isActiveString = '';

		if(!(activePage < pageSelectorsDisplayed / 2)) {
			var p = activePage - pageSelectorsDisplayed / 2;
		}
		else {
			//Start with the first pageSelector if the activePage-Number is
			//too small to allow half of the pageSelectorsDisplayed-Count of
			//pageSelectors to be displayed before the activePage
			var p = 1;
		}

		//remove the pageSelectors displayed
		$('div.pageSelect').html('');

		$('div.pageSelect').append(
			'<input type="radio" id="pageSelectFirst" name="pageSelect" />' +
			'<label for="pageSelectFirst">{0}</label>'.format(firstPageImg));
		//and add the correct ones
		while(p <= (Number(activePage) + pageSelectorsDisplayed / 2)) {
			if(p <= amountPages) {
				$('div.pageSelect').append(
					'<input type="radio" id="pageSelect' + p + '" ' +
						'value="' + p + '" name="pageSelect" />' +
					'<label for="pageSelect' + p + '">' + p + '</label>');
			}
			else {
				//only show when pageSelector when page exists
				break;
			}
			p += 1;
		}
		$('div.pageSelect').append(
			'<input type="radio" id="pageSelectLast" name="pageSelect" />' +
			'<label for="pageSelectLast">'+ lastPageImg +
			'</label>');

		setActivePageSelector();
		$("div.pageSelect").buttonset('refresh');
	};

	var userDeletePdf = function(pdfId, forename, name) {

		contentParent = $('.deletedUserPdf');

		//if there is the yet-no-users-deleted Message, remove it
		if($('.deletedUserPdf p.noUsersDeleted').length != 0) {
			$('div.deletedUserPdf').show().animate(500);
			contentParent.html('');
		}

		contentParent.append('<a href="index.php?\
			module=administrator|System|User|DeletedUserShowPdf&pdfId={0}"\
			target="_blank">PDF von "{1} {2}" abrufen</a><br />'
			.format(pdfId, forename, name));
	}

	/**
	 * The number of the active Page (currently displayed)
	 * @type {Number}
	 */
	var activePage = 1;
	/**
	 * The amount of pages that can be displayed
	 * @type {Number}
	 */
	var amountPages = 1;
	/**
	 * How many pages the selection-bar displays for selection
	 * @type {Number}
	 */
	var pageSelectorsDisplayed = 10;

	/**
	 * The path to the Image representing moving to the first page
	 * @type {String}
	 */
	var firstPageImg = '<img src="../images/pointers/arrowDoubleLeft.png" />';
	/**
	 * The path to the Image representing moving to the last page
	 * @type {String}
	 */
	var lastPageImg = '<img src="../images/pointers/arrowDoubleRight.png" />';


	existingColumnsSet();
	newDataFetch();
	pageSelectorUpdate();
});
