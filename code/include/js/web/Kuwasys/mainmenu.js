$(document).ready(function() {

	mainmenu();

	function mainmenu() {
		$('button.unregister-category').on('click', function(ev) {
			var categoryId = $(this).attr('catId');
			var $toDel = $(this).closest('.category-panel');
			checkHasClassWithRequestStatus($toDel);
			bootbox.confirm(
				'Willst du dich wirklich von den Kursen abmelden?',
				function(res) {
					if(res) {
						unregisterAll(categoryId, $toDel);
					}
				}
			);
		});

		function checkHasClassWithRequestStatus($categoryPanel) {
			var classes = $categoryPanel.find('.class-container');
			var hasStatus = false;
			$.each(classes, function(ind, el) {
				$el = $(el);
				if($el.attr('statusname') == 'request1' ||
					$el.attr('statusname') == 'request2'
				) {
					hasStatus = true;
				}
			});
			if(!hasStatus) {
				toastr['error'](
					'Du kannst dich nur von Kursen abmelden die du wünscht, ' +
					'nicht bei solchen bei denen du schon eingetragen bist!',
					'Fehler'
				);
			}
		}

		function unregisterAll(categoryId, $containerToVoid) {
			$.ajax({
				'type': 'post',
				'url': 'index.php?module=web|Kuwasys',
				'data': {
					'categoryId': categoryId,
					'unregisterFromAllClassesOfUnit': true
				},
				'success': function(data) {
					console.log(data);
					try {
						data = JSON.parse(data);
					} catch(e) {
						toastr['error'](
							'Konnte die Serverantwort nicht parsen!',
							'Verbindungsfehler'
						);
						return;
					}
					if(data.val == 'success') {
						toastr['success'](data.msg, 'Erfolgreich');
						$containerToVoid.fadeOut({
							'done': function() {
								$containerToVoid.remove();
								if($('#content').find('.category-panel').length == 0) {
									$('#no-selections-info').show();
								}
							}
						});
					}
					else if(data.val == 'message') {
						toastr['info'](data.msg, 'Hinweis');
					}
					else if(data.val == 'error') {
						toastr['error'](data.msg, 'Fehler');
					}
					else {
						toastr['error'](
							'Konnte die Serverantwort nicht lesen!',
							'Verbindungsfehler'
						);
					}
				},
				'error': function(data) {
					toastr['error'](
						'Fehler beim Verbinden zum Server!','Verbindungsfehler'
					);
				}
			});
		}
	}
});