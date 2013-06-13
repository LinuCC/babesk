$(document).ready(function() {

	addItemInterface = new AddItemInterface();

	$(document).tooltip();
	/**
	 * fix for absolute positioned Schoolyear-Box not adding to the size of the
	 * parent
	 */
	$('.personalData').height($('.personalData').height() + 50);

	$('input.cardnumberAdd').prop('disabled', true);
	$('.inputItem[name=password]').prop('disabled', true);

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
			$('.inputItem[name=password]').prop('disabled', false);
			$('.inputItem[name=password]').focus();
		}
		else {
			$('.inputItem[name=password]').prop('disabled', true);
			$('.inputItem[name=password]')
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

		$.ajax({
			type: "POST",
			url: "index.php?section=System|User&action=changeUser",
			data: {
				'ID': $('input.inputItem[name=ID]').val(),
				'forename': $('input.inputItem[name=forename]').val(),
				'name': $('input.inputItem[name=name]').val(),
				'username': $('input.inputItem[name=username]').val(),
				'passwordChange': $('input.passwordChange[name=passwordChange]').prop('checked'),
				'password': $('input.inputItem[name=password]').val(),
				'email': $('input.inputItem[name=email]').val(),
				'telephone': $('input.inputItem[name=telephone]').val(),
				'birthday': $('input.inputItem[name=birthday]').val(),
				'schoolyearIds': schoolyears,
				'pricegroupId': $('select.inputItem[name=pricegroupId] option:selected').val(),
				'gradeId': $('select.inputItem[name=gradeId] option:selected').val(),
				'credits': $('input.inputItem[name=credits]').val(),
				'cardnumber': $('input.inputItem[name=cardnumber]').val(),
				'isSoli': $('input.inputItem[name=isSoli]').prop('checked'),
				'accountLocked': $('input.inputItem[name=accountLocked]').prop('checked')
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
