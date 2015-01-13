$(document).ready(function() {

	(function displayClassDetails() {
		console.log(statuses);

		// The classes of the same year
		var classesOfSameYear = [];

		$('#change-user-move-class-switch').bootstrapSwitch();

		$('#change-user-move-class-switch').on(
			'switchChange.bootstrapSwitch', function(ev, state) {
				if(state) {
					$('#change-user-move-class-selector-container').fadeIn();
				}
				else {
					$('#change-user-move-class-selector-container').fadeOut();
				}
		});

		$('#username').autocomplete({
			source: "index.php?module=administrator|System|User|JsSearchForUsername",
		});

		$('#add-user-submit').on('click', function(ev) {
			$.ajax({
				type: 'POST',
				url: 'index.php?module=administrator|Kuwasys|Classes&addUserToClass',
				data: {
					'username': $('#username').val(),
					'statusId': $('#add-user-modal select[name="status"]').val(),
					'classId': $('#add-user-modal').attr('classId'),
					'categoryId': $('#add-user-modal select[name="category"]').val()
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
			$modal.attr('joinId', $(this).attr('joinId'));

			if(classesOfSameYear.length > 0) {
				changeUserModalSelectUpdate(classesOfSameYear);
			}
			else {
				$.postJSON(
					'index.php?module=administrator|Kuwasys|Classes|ChangeUserStatus&\
						fetchclasses',
					{
						schoolyearId: $('tr.class-schoolyear').attr('schoolyearid'),
						categoryId: $('tr.class-category').attr('categoryid')
					},
					function(res) {
						console.log(res);
						if(res.state == 'success') {
							classesOfSameYear = res.data;
							changeUserModalSelectUpdate(classesOfSameYear);
						}
						else {
							toastr['error'](res.data);
						}
					}
				);
			}
			$modal.modal('show');
		});

		function changeUserModalSelectUpdate(data) {
			var $select = $('div#change-user-modal select[name="classes"]');
			$select.html();
			$.each(data, function(classId, classLabel) {
				$select.append(
					'<option value="' + classId + '">' + classLabel + '</option>'
				);
			});
		}

		$('button#change-user-submit').on('click', function(ev) {
			var $modal = $('#change-user-modal');
			var switchClass = $modal.find('#change-user-move-class-switch')
				.bootstrapSwitch('state');
			var statusId = $modal.find('select[name="status"] option:selected')
						.val();
			var joinId = $modal.attr('joinId');
			$.postJSON(
				'index.php?module=administrator|Kuwasys|Classes|ChangeUserStatus',
				{
					'joinId': joinId,
					'statusId': statusId,
					'switchClass': switchClass,
					'classId': $modal.find('select[name="classes"] option:selected')
						.val()
				},
				function(res) {
					if(res.state == 'success') {
						$row = $('table tr[joinId="' + joinId + '"]');
						if(switchClass) {
							$row.addClass("text-muted");
						}
						else {
							var $statusField = $row.find('td.user-status');
							$.each(statuses, function(ind, el) {
								if(el.ID == statusId) {
									$statusField.html(el.translatedName);
									return false;
								}
							});
						}
						$modal.modal('hide');
						toastr['success'](res.data);
						toastr['info']("Seite neu laden um aktualisierte Tabelle zu erhalten.");
					}
					else {
						toastr['error'](res.data);
					}
				}
			);
		});
	})();
});