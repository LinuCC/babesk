/**
 * Provides Functions for the various adding-Something-Forms (example Users)
 */
var AddItemInterface = function() {

	that = this;
	/**
	 * Checks the input of a inputField for its correctness
	 * @param  {String} userInput   The Input the User made
	 * @param  {String} regex       The regex to check for (or some predefined
	 *                              constants, see the Server-Side function for
	 *                              checking Input)
	 * @param  {JQueryElement} element The Element to check
	 */
	that.userInputCheck = function(userInput, regex, element) {

		serverPath = location.pathname.replace('administrator/index.php', '');
		var serverRequest = 'http://' + location.host + serverPath + '/publicData/index.php?section=JsDataProcessor|InputdataCheck';

		$.ajax({
			type: 'POST',
			url: serverRequest,
			data: {
				userInput: userInput,
				regex: regex,
				elementName: element.attr('name')
			},
			success: function(data) {
				userInputReturnHandle(data, element);
			},
		});
	};

	that.userInputCheckGump = function(userInput, regex, element) {

		serverPath = location.pathname.replace('/administrator/index.php', '');
		var serverRequest = 'http://' + location.host + serverPath + '/publicData/index.php?module=PublicData|JsDataProcessor|InputdataCheck&gump';

		$.ajax({
			type: 'POST',
			url: serverRequest,
			data: {
				userInput: userInput,
				regex: regex,
				elementName: element.attr('name')
			},
			success: function(data) {
				if(/\s/.test(data)) {
					console.log('"' + data + '"');
				}
				userInputReturnHandle(data, element);
			},
			error: function(data) {
				console.log(data);
				adminInterface.errorShow('Konnte die eingegebenen Daten nicht überprüfen.');
			}
		});
	};

	/**
	 * Handles the data the Server returned for userInputCheck
	 * @param  {String} data Data the server returned
	 */
	userInputReturnHandle = function(data, element) {

		if(data == 'correctInput') {
			that.userInputToCorrect(element);
			return true;
		}
		else if(data == 'wrongInput') {
			that.userInputToWrong(element);
		}
		else if(data == 'parametersMissing') {
			adminInterface.errorShow('Ein Fehler ist beim Überprüfen der Eingabedaten aufgetreten.' + data);
		}
		else {
			adminInterface.errorShow('Ein Fehler ist beim Überprüfen der Eingabedaten aufgetreten.' + data);
		}
		return false;
	};

	that.userInputToWrong = function(element) {

		if(!element.hasClass('inputWrong')) {
			element.addClass('inputWrong');
			element.animate({"background-color": "#FFAAAA"}, 500);
			$('<p class="inputError">Falsche Eingabe!</p>')
				.insertAfter(element)
				.hide().show(500)
				.animate({'color':'#CC4422'});
		}
		else {
			//do nothing, error already showing
		}
	};

	that.userInputToCorrect = function(element) {

		element.animate({"background-color": "#CCFFCC"}, 200);
		if(element.hasClass('inputWrong')) {
			element.removeClass('inputWrong');
			element.next().remove();
		}
		else {
			//no need to show that the input is correct
		}
	}

	/**
	 * Clears all inputfields of their value
	 */
	clearInputdata = function() {

		$('input.inputItem').each(function(index) {
			$(this).attr('value', '');
		});
	};
}
