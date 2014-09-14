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
		var parent = button.parent();
		parent.children("button").remove();
		if(button.attr("conflictType") == "GradelevelConflict") {
			newGradeInput(parent, button.attr("conflictId"));
		}
		else {
			parent.append('<div id="lol"></div>');
			//Finding similar usernames is not implemented yet
			var uid = prompt("Dann korrigieren sie den Fehler bitte in der CSV-Datei und laden die CSV-Datei nochmals hoch oder geben sie die Nutzerid hier ein:");
			if(uid) {
				parent.append("<input type='hidden' value='correctedUserId' name='"
				 + "conflict[" + button.attr("conflictId") + "][status]' />");
				parent.append("<input type='hidden' value='" + uid + "' name='"
				 + "conflict[" + button.attr("conflictId") + "][correctedUserId]' />");
			}
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