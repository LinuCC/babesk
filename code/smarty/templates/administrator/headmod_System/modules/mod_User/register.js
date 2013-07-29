/**
 * Handles the Input of the Schoolyears and the Grades
 */
var schoolyearAndGradeHandler = function() {

	var that = this;

	var updateRemoveClickHandler = function updateRemoveClickHandler() {

		$('input.gradeSchoolyearRemove').on('click', function(event) {

			event.preventDefault();
			var toDel = $(this).parent();
			toDel.remove();
		});
	};

	var inputHtmlSceletonCreate = function() {

		var output = $(
			'<div class="schoolyearGradeRow">\
				Im Schuljahr\
				<select name="schoolyearId">\
				</select>\
				in Klasse\
				<select name="gradeId">\
				</select>\
				<input type="image" src="../images/status/forbidden_32.png"\
					title="Diese Kombination entfernen"\
					class="gradeSchoolyearRemove" />\
			</div>'
		);

		return output;
	};

	var schoolyearContainerAppend = function(parent) {

		var schoolyearContainer = parent.children(
			'select[name="schoolyearId"]');

		for(var syId in schoolyears) {
			schoolyearContainer.append(
				'<option value="' + syId + '">' +
				schoolyears[syId] + '</option>');
		}
	};

	var gradeContainerAppend = function(parent) {

		var gradeContainer = parent.children(
			'select[name="gradeId"]');

		for(var gradeId in grades) {
			gradeContainer.append(
				'<option value="' + gradeId + '">' +
				grades[gradeId] + '</option>');
		}
	};

	var checkForSameSchoolyearId = function(
		schoolyearId,
		selectedGradesAndSchoolyears) {

		var syIsUnique = true;

		$.each(selectedGradesAndSchoolyears, function(index, value) {
			if(value.schoolyearId == schoolyearId) {
				adminInterface.errorShow('Bitte wählen sie nicht zweimal\
					das gleiche Schuljahr aus!');
				syIsUnique = false;
			}
		});

		return syIsUnique;
	}

	that.inputGet = function() {

		var selectedGradesAndSchoolyears = [];
		var isOkay = true;

		$('.schoolyearGradeRow').each(function(index, value) {

			var schoolyearId = $(this).find('select[name="schoolyearId"]')
				.find(':selected').val();
			var gradeId = $(this).find('select[name="gradeId"]')
				.find(':selected').val();

			if(checkForSameSchoolyearId(schoolyearId, selectedGradesAndSchoolyears)) {
				selectedGradesAndSchoolyears.push({
					'schoolyearId': schoolyearId,
					'gradeId': gradeId
				});
			}
			else {
				isOkay = false;
			}
		});

		if(isOkay) {
			return selectedGradesAndSchoolyears;
		}
		else {
			return false;
		}
	};

	$('input.gradeSchoolyearAdd').on('click', function(event) {

		event.preventDefault();

		output = inputHtmlSceletonCreate();
		schoolyearContainerAppend(output);
		gradeContainerAppend(output);

		$('.schoolyearGradeContainer input.gradeSchoolyearAdd').before(output);
		updateRemoveClickHandler();
	});

	updateRemoveClickHandler();
};

$(document).ready(function() {

	addItemInterface = new AddItemInterface();
	sygHandler = new schoolyearAndGradeHandler();

	$('input.cardnumberAdd').hide();

	$('a.cardnumberAdd').on('click', function(event) {

		event.preventDefault();

		$('a.cardnumberAdd').hide(200);
		$('input.cardnumberAdd').css({display: 'inline'}).focus();
		//register Eventhandler
		$('input.cardnumberAdd').on('keyup', function(event) {
			if($(this).val().length == 10) {
				addItemInterface.userInputCheckGump($(this).val(),
					'exact_len,10', $(this));
			}
		});
	});

	$('input.inputItem[name=birthday]').datepicker({
		dateFormat: 'yy-mm-dd',
		changeMonth: true,
		changeYear: true,
		yearRange: "1920:+10"
	});


	/**
	 * When other field selected, check input of the field where input was made
	 */
	$('input.inputItem[name=forename]').on('focusout', function() {
		addItemInterface.userInputCheckGump($(this).val(),
			'required|min_len,2|max_len,64', $(this));
	});
	$('input.inputItem[name=name]').on('focusout', function() {
		addItemInterface.userInputCheckGump($(this).val(),
			'required|min_len,2|max_len,64', $(this));
	});
	$('input.inputItem[name=username]').on('focusout', function() {
		addItemInterface.userInputCheckGump($(this).val(),
			'min_len,2|max_len,64', $(this));
	});
	$('input.inputItem[name=password]').on('focusout', function() {
		addItemInterface.userInputCheckGump($(this).val(),
			'min_len,2|max_len,64', $(this));
	});
	$('input.inputItem[name=passwordRepeat]').on('focusout', function() {
		addItemInterface.userInputCheckGump($(this).val(),
			'min_len,2|max_len,64', $(this));
	});
	$('input.inputItem[name=email]').on('focusout', function() {
		addItemInterface.userInputCheckGump($(this).val(),
			'valid_email|min_len,2|max_len,64', $(this));
	});
	$('input.inputItem[name=telephone]').on('focusout', function() {
		addItemInterface.userInputCheckGump($(this).val(),
			'min_len,2|max_len,64', $(this));
	});
	$('select.inputItem[name=pricegroupId] option:selected').on('focusout',
		function() {
		addItemInterface.userInputCheckGump($(this).val(),
			'numeric|min_len,1|max_len,11', $(this));
	});
	$('input.inputItem[name=credits]').on('focusout', function() {
		value = $(this).val();
		$(this).val(value.replace(',', '.'));
		addItemInterface.userInputCheckGump($(this).val(),
			'numeric|min_len,1|max_len,5', $(this));
	});

	/**
	 * Admin submitted the form, add the User
	 */
	$('form.simpleForm').on('submit', function(event) {

		addFatalError = function() {
			adminInterface.errorShow('Konnte aufgrund eines Fehlers den Benutzer nicht hinzufügen');
		}

		cleanInputFields = function() {
			$('input.inputItem').each(function(index) {
				$(this).val('');
			});
		}

		var schoolyearAndGradeData = sygHandler.inputGet();
		if(!schoolyearAndGradeData) {
			addFatalError();
			return false;
		}

		event.preventDefault();

		$.ajax({
			type: "POST",
			url: "index.php?module=administrator|System|User|Register",
			data: {
				'forename': $('input.inputItem[name=forename]').val(),
				'name': $('input.inputItem[name=name]').val(),
				'username': $('input.inputItem[name=username]').val(),
				'password': $('input.inputItem[name=password]').val(),
				'passwordRepeat':
					$('input.inputItem[name=passwordRepeat]').val(),
				'email': $('input.inputItem[name=email]').val(),
				'telephone': $('input.inputItem[name=telephone]').val(),
				'birthday': $('input.inputItem[name=birthday]').val(),
				'pricegroupId': $('select.inputItem[name=pricegroupId] option:selected').val(),
				'schoolyearAndGradeData': schoolyearAndGradeData,
				'credits': $('input.inputItem[name=credits]').val(),
				'cardnumber': $('input.inputItem[name=cardnumber]').val(),
				'isSoli': $('input.inputItem[name=isSoli]').prop('checked'),
			},

			success: function(data) {

				console.log(data);
				try {
					var res = $.parseJSON(data);
				}
				catch (e) {
					adminInterface.errorShow('Error parsing the server-response');
					addFatalError();
					return;
				}
				if(res.value == 'success') {
					adminInterface.successShow(res.message);
					cleanInputFields();
				}
				else if(res.value == 'inputError') {
					$.each(res.message, function(index, error) {
						console.log(error);
						adminInterface.errorShow(error);
					});
					addFatalError();
				}
				else if(res.value == 'error') {
					addFatalError();
				}
				else if(res.value == 'success') { //success
					adminInterface.successShow(res.message);
				}
				else {
					adminInterface.errorShow('Server returned unknown value');
					addFatalError();
				}
			},

			error: function(error) {
				addFatalError();
			}
		});
	});

});
