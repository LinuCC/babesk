$(document).ready(function() {

	$('#username').autocomplete({
		source: "index.php?module=administrator|System|User|JsSearchForUsername",
	});

	$('#add-user-submit').on('click', function(ev) {
		$.ajax({
			type: 'POST',
			url: 'index.php?module=administrator|Kuwasys|KuwasysUsers|AddUserToClass',
			data: {
				'username': $('#username').val(),
				'statusId': $('select[name="status"]').val(),
				'classId': $('#add-user-modal').attr('classId')
			},

			success: function(data) {
				try {
					data = JSON.parse(data);
				} catch(e) {
					toastr['error'](data);
				}

				if(data.value == 'error') {
					toastr['error'](data.message);
				}
				else if(data.value == 'success') {
					window.location.reload();
				}
				else {
					toastr['error']("{t}Could not parse the Serveranswer!{/t}");
				}
			},

			error: function(data) {
				toastr['error']("{t}Could not Assign the User to the Class!{/t}");
			}
		});
	});

	$('#assignUser').on('click', function(event) {
		$('#addUserDialog').dialog('open');
	});

	$('button.unregister-user').on('click', function(ev) {
		ev.preventDefault();
		$clicked = $(this);
		var $row = $clicked.closest('tr');

		var unregisterUser = function() {
			$.postJSON(
				"index.php?module=administrator|Kuwasys|Classes|UnregisterUserFromClass",
				{"joinId": $clicked.attr('joinId')},
				function(res) {
					if(res.state == 'success') {
						toastr['success'](res.data);
					}
					else {
						toastr['error'](res.data, 'Fehler');
					}
				}
			);
			$row.fadeOut();
		}

		var username = $row.children('td.username').text();
		bootbox.confirm(
			'Wollen sie den Benutzer ' + username +
				' wirklich von dem Kurs entfernen?',
			function(res) {
				if(res) {
					unregisterUser();
				}
			}
		);
	});

	$('button.change-user').on('click', function(ev) {
		var $row = $(this).closest('tr');
		var $modal = $('#change-user-modal');
		var statusId = $row.children('td.user-status').attr('statusid');
		var username = $row.children('td.username').text();

		$.each($modal.find('select[name="status"] option'), function(ind, el) {
			if($(el).val() == statusId) {
				$(el).prop('selected', true);
			}
		});
		$modal.find('div.modal-title span.username').html(username);

		$modal.modal('show');
	});
});