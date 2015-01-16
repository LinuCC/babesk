$(document).ready(function() {

	$(document).on('keyup', null, 'ctrl+shift+n', function() {
		$("button#YesToAllConflicts").click();
	});

	$("button#YesToAllConflicts").on('click', function(event) {
		event.preventDefault();
		$.each($('#conflictForm').find('button.conflictAnswerYes'),
			function(index, val) {
				val.click();
		});
	});

	$("button.conflictAnswerYes").on('click', function(event) {

		event.preventDefault();
		var button = $(event.target);
		var parent = button.parent();
		parent.children("button").remove();
		parent.append(translations.answeredWithYes);
		//append hidden input to know what was changed
		parent.append("<input type='hidden' value='confirmed' name='"
			 + "conflict[" + button.attr("conflictId") + "][status]' />");
	});

	$("button.conflictAnswerNo").on('click', function(event) {

		event.preventDefault();
		var button = $(event.target);
		var $listItem = button.closest(".list-group-item");
		$listItem.children("button").remove();
		if(button.attr("conflictType") == "GradelevelConflict") {
			newGradeInput($listItem, button.attr("conflictId"));
		}
		else {
			//$listItem.append('<div><select></div>');
			var $userSelectContainer = $('<div class="">Alternative auswählen:<select name="conflict[' + button.attr("conflictId") + ']' +
				'[correctedUserId]" class="form-control"></select></div>');
			$listItem.append($userSelectContainer);
			var username = $listItem.find('span.forename').text() + '.' + $listItem.find('span.surname').text();
			$.ajax({
				'type': 'GET',
				'url': 'index.php?module=administrator|System|User|SearchForSimilarUsers',
				'data': {
					'username': username,
					'userLimit': 15
				},
				'success': function(users, status, jqXHR) {
					console.log(users);
					var $select = $userSelectContainer.find('select');
					$.each(users, function(userId, user) {
						$select.append(
							'<option name="' + userId + '">' + user + '</option>'
						);
					});
					$listItem.append("<input type='hidden' value='correctedUserId' " +
						"name='conflict[" + button.attr("conflictId") + "][status]' />");
				},
				'error': function(data, status, jqXHR) {
					console.log(data);
					toastr.error('Ein Fehler ist aufgetreten.');
				},
				dataType: 'json'
			});
		}
	});


	function newGradeInput(parent, conflictId) {
		parent.append('<p id="gradeHint">' + translations.newGradeInput + '</p>');
		textfield = $('<input id="gradefield" type="text" name="' + conflictId +
					'" size="4"></input>');
		parent.append(textfield);
		var finButton = $('<button id="finished">' + translations.finished +
			'</button>');
		parent.append(finButton);
		finButton.on('click', function(event) {
			event.preventDefault();
			var newGrade = parent.children('#gradefield').val();
			parent.children('#finished').remove();
			parent.children("#gradefield").remove();
			parent.children("#gradeHint").remove();
			parent.append(translations.newGradeWillBe +
				' <span class="highlighted">"' +  newGrade + '"</span>' +
				'<input type="hidden" name="' + conflictId + '" value="' +
				newGrade + '" />');
		});
	}

});