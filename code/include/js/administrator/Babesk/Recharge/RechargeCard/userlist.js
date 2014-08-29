$(document).ready(function() {

	var activePage = 1;
	var charCodeToSelector = {
		33: 1, 34: 2, 167: 3, 36: 4, 37: 5, 38: 6, 47: 7, 40: 8, 41: 9, 61: 0
	};

	dataFetch();
	var $pagination = $('#page-select');

	$pagination.bootpag({
		total: 1,
		page: activePage,
		maxVisible: 9
	}).on('page', activePageChange);

	$('#filter').enterKey(dataFetch);
	$('#search-submit').on('click', dataFetch);

	$('table#user-table').on('click', 'tbody tr', userClicked);

	$('#credits-change-form').on('submit', creditsChangeApply);

	$('#credits-change-input').enterKey(function(event) {
		event.preventDefault();
		$('#credits-change-form').submit();
	});

	$('#credits-change-modal').on('shown.bs.modal', function(event) {
		$(this).find('input#credits-change-input').focus().select();
	});

	$('body').on('keypress', executeSelectorKey);

	$('#credits-change-modal').on('hidden.bs.modal', function(event) {
		$('#filter').focus().select();
		$('#credits-add-input').val('');
	});

	$('button.preset-credit-change').on('click', creditChangeByPreset);

	$('input#credits-add-input').enterKey(creditAddToByInput);

	function dataFetch() {

		var filter = $('#filter').val();
		$.postJSON(
			'index.php?module=administrator|Babesk|Recharge|RechargeCard',
			{
				'filter': filter,
				'activePage': activePage
			},
			success
		);

		function success(res, textStatus, jqXHR) {
			if(jqXHR.status == 200) {
				tableFill(res.users);
				pageSelectorUpdate(res.pagecount);
			}
			else if(jqXHR.status == 204) {
				toastr.error('Keinen Eintrag gefunden!');
				$('#filter').focus().select();
			}
			else {
				toastr.error(
					'Fehler! Unbekannter Status ' + toString(jqXHR.status)
				);
			}
		};
	};

	function tableFill(users) {

		var html = microTmpl(
			$('#user-table-template').html(),
			{'users': users}
		);
		$('#user-table').html(html);
	};

	function pageSelectorUpdate(pagecount) {
		$pagination.bootpag({
			total: pagecount,
			page: activePage,
			maxVisible: 9
		});
	};

	function activePageChange(event, pagenum) {
		activePage = pagenum;
		dataFetch();
	};

	function userClicked(event) {

		var $row = $(event.target).closest('tr');
		var $modal = $('#credits-change-modal');
		var userId = $row.data('user-id');
		var username = $row.children('td.username').text();
		var credits = parseFloat($row.children('td.credits').text());
		$modal.find('.username').html(username);
		$modal.find('.credits-before').html(credits.toFixed(2) + '€');
		var $input = $modal.find('input#credits-change-input');
		$input.val(credits.toFixed(2));
		$input.data('user-id', userId);
		$('#credits-change-modal').modal();
	};

	function creditsChangeApply(event) {

		event.preventDefault();
		var $modal = $('#credits-change-modal');
		var $input = $modal.find('input#credits-change-input');
		var credits = $input.val().replace(",", ".");
		var userId = $input.data('user-id');

		$.ajax({
			'type': 'POST',
			'url': 'index.php?module=administrator|Babesk|Recharge|RechargeCard',
			'data': {
				"userId": userId,
				"credits": credits
			},
			'success': success,
			'error': error,
			'dataType': 'json'
		});

		//$.postJSON(
		//	'index.php?module=administrator|Babesk|Recharge|RechargeCard',
		//	{
		//		"userId": userId,
		//		"credits": credits
		//	},
		//	success
		//);

		function success(res) {

			$('table#user-table tbody')
				.find('tr[data-user-id=' + res.userId + ']')
				.find('td.credits')
				.html(parseFloat(res.credits).toFixed(2) + ' €');
			toastr.success('Guthaben erfolgreich verändert.');
			$modal.modal('hide');
		};

		function error(jqXHR) {

			if(jqXHR.status == 500) {
				if(typeof jqXHR.responseJSON !== 'undefined' &&
					typeof jqXHR.responseJSON.message !== 'undefined') {
					toastr.error(jqXHR.responseJSON.message, 'Fehler');
				}
				else {
					toastr.error('Ein Fehler ist beim Ändern aufgetreten');
				}
			}
			else {
				toastr.error('Konnte die Serverantwort nicht lesen!', 'Fehler');
			}
		};
	};

	function executeSelectorKey(event) {

		if(event.shiftKey == true) {
			if(charCodeToSelector[event.charCode] != undefined &&
				charCodeToSelector[event.charCode] != "undefined"
			) {
				event.preventDefault();
				var num = charCodeToSelector[event.charCode];
				$('table#user-table tbody td.selector[data-selector=' + num + ']')
					.closest('tr').click();
			}
		}
	};

	function creditChangeByPreset(event) {

		event.preventDefault();
		var $input = $('input#credits-change-input');
		var amount = parseInt($(this).text());
		var prevAmount = parseFloat($input.val().replace(",", "."));
		$input.val((prevAmount + amount).toFixed(2));
		$input.focus();
	};

	function creditAddToByInput(event) {

		//Dont let the enter-keypress bubble up to the form, so that the modal dont
		//closes
		//event.stopPropagation();
		event.preventDefault();
		var $input = $('input#credits-change-input');
		var addAmount = parseFloat($(this).val().replace(",", "."));
		var changeAmount = parseFloat($input.val().replace(",", "."));
		console.log((changeAmount + addAmount).toFixed(2));
		$input.val((changeAmount + addAmount).toFixed(2));
		$input.focus();
	}
});