$(document).ready(function() {

	login();

	function login() {
		$('#login-confirm').on('click', function(ev) {
			run();
		});

		function run() {
			$.ajax({
				'type': 'POST',
				'url': 'index.php?login=ajax',
				'data': {
					'login': $('#username-inp').val(),
					'password': $('#password-inp').val(),
				},
				'success': onSuccess,
				'error': onError
			});
		}

		function onSuccess(data) {
			try {
				data = JSON.parse(data);
			} catch(e) {
				toastr['error'](
					'Konnte Serverantwort nicht lesen',
					'Fehler beim einloggen!'
				);
			}
			if(data.val == 'success') {
				window.location.href = "index.php";
			}
			else if(data.val == 'error') {
				toastr['error'](
					data.msg,
					'Fehler beim einloggen!'
				);
			}
			else {
				toastr['info'](
					data.msg,
					'Hinweis:'
				);
			}
			console.log(data);
			// $('body').innerHTML = data;
		}

		function onError(data) {
			toastr['error']('Schinken', 'Barsch');
		}
	};

});