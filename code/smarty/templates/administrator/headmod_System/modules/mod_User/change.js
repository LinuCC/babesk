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


	var sygHandler = new schoolyearAndGradeHandler();
	addItemInterface = new AddItemInterface();

	$(document).tooltip();

	$('input.cardnumberAdd').prop('disabled', true);
	$('input[name=password]').prop('disabled', true);

	/**
	 * If link is clicked, a cardnumber shall be added
	 */
	$('a.cardnumberAdd').on('click', function(event) {

		event.preventDefault();

		$('a.cardnumberAdd').hide();
		$('input.cardnumberAdd').prop('disabled', false);
		$('input.cardnumberAdd').focus();
		//register Eventhandler
		$('input.cardnumberAdd').on('keyup', function(event) {
			if($(this).val().length == 10) {
				addItemInterface.userInputCheckGump($(this).val(),
					'exact_len,10', $(this));
			}
		});
	});

	$('.passwordChange').on('change', function(event) {

		if($(this).prop('checked')) {
			$('input[name=password]').prop('disabled', false);
			$('input[name=password]').focus()
				.animate({"background-color": "#FFFFFF"}, 200);
		}
		else {
			$('input[name=password]').prop('disabled', true);
			$('input[name=password]')
				.animate({"background-color": "#DDDDDD"}, 200);
		}
	});

	$('input.inputItem[name=birthday]').datepicker({
		dateFormat: 'yy-mm-dd',
		changeMonth: true,
		changeYear: true,
		yearRange: "1920:+10"
	});

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
	$('input.inputItem[name=email]').on('focusout', function() {
		addItemInterface.userInputCheckGump($(this).val(),
			'valid_email|min_len,2|max_len,64', $(this));
	});
	$('input.inputItem[name=telephone]').on('focusout', function() {
		addItemInterface.userInputCheckGump($(this).val(),
			'min_len,2|max_len,64', $(this));
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

		var fatalError = function() {
			adminInterface.errorShow('Konnte aufgrund eines Fehlers den Benutzer nicht ändern');
		}

		event.preventDefault();

		var schoolyears = JSON.stringify($('select.inputItem[name=schoolyearIds]').val());

		var groups = $("input[name^='groups[']").map(function(){
			if($(this).prop('checked')) {
				var id = $(this).attr('name')
							.replace('groups[', '').replace(']', '');
				return id;
			}
		}).get();

		var schoolyearAndGradeData = sygHandler.inputGet();
		if(!schoolyearAndGradeData) {
			fatalError();
			return false;
		}

		$.ajax({
			type: "POST",
			url: "index.php?module=administrator|System|User|Change",
			data: {
				'ID': $('input.inputItem[name=ID]').val(),
				'forename': $('input.inputItem[name=forename]').val(),
				'name': $('input.inputItem[name=name]').val(),
				'username': $('input.inputItem[name=username]').val(),
				'passwordChange': $('input.passwordChange[name=passwordChange]').prop('checked'),
				'password': $('input[name=password]').val(),
				'email': $('input.inputItem[name=email]').val(),
				'telephone': $('input.inputItem[name=telephone]').val(),
				'birthday': $('input.inputItem[name=birthday]').val(),
				'groups': groups,
				'pricegroupId': $('select.inputItem[name=pricegroupId] option:selected').val(),
				'credits': $('input.inputItem[name=credits]').val(),
				'cardnumber': $('input.inputItem[name=cardnumber]').val(),
				'isSoli': $('input.inputItem[name=isSoli]').prop('checked'),
				'accountLocked': $('input.inputItem[name=accountLocked]').prop('checked'),
				'schoolyearAndGradeData': schoolyearAndGradeData
			},

			success: function(data) {

				console.log(data);

				try {
					var res = $.parseJSON(data);
				}
				catch (e) {
					adminInterface.errorShow('Error parsing the server-response');
					fatalError();
					return;
				}
				if(res.value == 'success') {
					adminInterface.successShow(res.message);
				}
				else if(res.value == 'inputError') {
					$.each(res.message, function(index, error) {
						console.log(error);
						adminInterface.errorShow(error);
					});
					fatalError();
				}
				else if(res.value == 'error') {
					fatalError();
				}
				else {
					adminInterface.errorShow('Server returned unknown value');
					fatalError();
				}
			},

			error: function(error) {
				fatalError();
			}
		});
	});

});
