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
		// If the user previously just checked if a user with the same name
		// exists but now wants to revert his decision, we need to remove the
		// select-thingie
		$listitem = parent.closest('.list-group-item');
		$listitem.find("div.alternative-selection").remove();
		$listitem.find("input[type='hidden']").remove();
		$listitem.find("div.data-radios").remove();
		parent.append(translations.answeredWithYes + '&nbsp;&nbsp;&nbsp;&nbsp;');
		//append hidden input to know what was changed
		parent.append("<input type='hidden' value='confirmed' name='"
			 + "conflict[" + button.attr("conflictId") + "][status]' />");
	});

	$("button.conflictAnswerNo").on('click', function(event) {

		event.preventDefault();
		var button = $(event.target);
		var $listItem = button.closest(".list-group-item");
		// $listItem.children("button").remove();
		if(button.attr("conflictType") == "GradelevelConflict") {
			newGradeInput($listItem, button.attr("conflictId"));
		}
		else {
			//$listItem.append('<div><select></div>');
			var $userSelectContainer = $(
				'<div class="alternative-selection">Alternative auswählen: '+
				'<select name="conflict[' + button.attr("conflictId") + ']' +
				'[mergeSecondConflictId]" class="form-control"></select></div>'
			);
			$listItem.append($userSelectContainer);
			var username = $listItem.find('span.forename').text() + '.' + $listItem.find('span.surname').text();
			$userSelectContainer.find('select')
				.prop('disabled', true)
				.append(
					'<option class="placeholder" value="" disabled selected>' +
					'Lade...</option>'
				);
			if(button.attr('conflictType') === 'CsvOnlyConflict') {
				mergeConflictType = 'DbOnlyConflict';
			}
			else if(button.attr('conflictType') === 'DbOnlyConflict') {
				mergeConflictType = 'CsvOnlyConflict';
			}
			else {
				toastr.error('Unbekannter Konflikttyp');
				return;
			}
			$.ajax({
				'type': 'GET',
				'url': 'index.php?module=administrator|System|User|UserUpdateWithSchoolyearChange|SessionMenu|ConflictsResolve&search',
				'data': {
					'username': username,
					conflictType: mergeConflictType
				},
				dataType: 'json'
			})
				.done(function(conflicts, status, jqXHR) {
					if(jqXHR.status === 204) {
						toastr.info('Keine ähnlichen Benutzer mit Konflikten gefunden.');
						return;
					}
					var $select = $userSelectContainer.find('select');
					$select.append(conflicts.map(function(conflict) {
						return (
							'<option value="' + conflict.id + '">' + conflict.label +
							'</option>'
						);
					}));
					$select.find('option.placeholder').remove();
					$select.prop('disabled', false);
					origUserIdent = $listItem.data('forename') + ' ' +
						$listItem.data('name') + ' (' + $listItem.data('birthday') + ')';
					// Select which data to use
					$listItem.append(
						'<div class="data-radios checkbox">' +
						'<label><input type="radio" name="conflict['
						+ button.attr('conflictId') +
						'][conflictDataUseSelect]" value="original" checked /> ' +
						'&nbsp;&nbsp;Benutze Daten: "'+  origUserIdent + '"</label><br>' +
						'<label><input type="radio" name="conflict[' +
						button.attr('conflictId') +
						'][conflictDataUseSelect]" value="alternative" /> ' +
						'&nbsp;&nbsp;Benutze Daten der Alternativen-Auswahl</label>' +
						'</div>'
					);
					$listItem.append("<input type='hidden' value='mergeConflicts' " +
						"name='conflict[" + button.attr("conflictId") + "][status]' />");
				})
				.fail(function(data, status, jqXHR) {
					console.log(data);
					toastr.error('Ein Fehler ist aufgetreten.');
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