$(document).ready(function() {

	addItemInterface = new AddItemInterface();

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

		event.preventDefault();

		$.ajax({
			type: "POST",
			url: "index.php?section=System|User&action=addUser",
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
				'schoolyearId': $('select.inputItem[name=schoolyearId] option:selected').val(),
				'pricegroupId': $('select.inputItem[name=pricegroupId] option:selected').val(),
				'gradeId': $('select.inputItem[name=gradeId] option:selected').val(),
				'credits': $('input.inputItem[name=credits]').val(),
				'cardnumber': $('input.inputItem[name=cardnumber]').val(),
				'isSoli': $('input.inputItem[name=isSoli]').prop('checked'),
			},

			success: function(data) {

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
