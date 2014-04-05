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
				'statusId': $('#status').val(),
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

	$('a.unregister-user').on('click', function(ev) {
		ev.preventDefault();
		$.post(
			"index.php?module=administrator|Kuwasys|Classes|DisplayClassDetails&\
				ID={$otherClass.ID}",
			$(this).attr('joinId'),
			function(res) {
				console.log(res);
			},
			'json'
		);
	});

});