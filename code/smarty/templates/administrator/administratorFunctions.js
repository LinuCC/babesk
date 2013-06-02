/**
 * General functions for use in the Administrator-Subprogram
 */

/**
 * A function to emulate a sprintf-like functionality
 */
//first, checks if it isn't implemented yet
if (!String.prototype.format) {
	String.prototype.format = function() {
		var args = arguments;
		return this.replace(/{(\d+)}/g, function(match, number) {
			return typeof args[number] != 'undefined'
				? args[number] : match;
		});
	};
}

/**
 * Contains functions allowing to show Messages/Errors/Success-Notices
 */
var AdminInterface = function() {

	/**
	 * Shows a Message to the User
	 * @param  {String} str The Message-String to be shown
	 */
	this.messageShow = function(str) {

		//does the Message-Container exist?
		if(!$('#messageContainer').length) {
			messageContainerAdd();
		}

		htmlStr = listItemHtmlCreate(str);
		$('#messageContainer ul').append(htmlStr);
		$('#messageContainer').show();
	}

	/**
	 * Shows an Error to the User
	 * @param  {String} str The Message-String to be shown
	 */
	this.errorShow = function(str) {

		if(!$('#errorContainer').length) {
			errorContainerAdd();
		}

		var htmlStr = listItemHtmlCreate(str);
		$('#errorContainer ul').append(htmlStr);
		$('#errorContainer').show();
		$('.errorContainerClose').on('click', function(event) {
			event.preventDefault();
			$('#errorContainer').hide('highlight', 500);
			$('#errorContainer ul').html('');
		});
	}

	/**
	 * Shows a Success-Notice to the User
	 * @param  {String} str The Message-String to be shown
	 */
	this.successShow = function(str) {

		if(!$('#successContainer').length) {
			successContainerAdd();
		}

		var htmlStr = listItemHtmlCreate(str);
		$('#successContainer ul').append(htmlStr);
		$('#successContainer').show();
	}

	/**
	 * Creates a HTML-list-item
	 * @param  {String} str The String that should be wrapped to a list-item
	 * @return {String} HTML-Code containing the List-Item
	 */
	var listItemHtmlCreate = function(str) {

		var res = '<li><p>' + str + '</p></li>';
		return res;
	}

	/**
	 * Adds a MessageContainer to the Site (void, possibly hidden, too)
	 */
	messageContainerAdd = function() {

		var html = '<div id="messageContainer"><a class="messageContainerClose" href="#" tabindex="1">Schließen</a><ul></ul></div>';

		if($('#errorContainer').length) {
			$('#errorContainer').after(html);
		}
		else {
			$('#header').after(html);
		}
		$('.messageContainerClose').on('click', function(event) {
		event.preventDefault();
		$('#messageContainer').hide('highlight', 500);
		$('#messageContainer ul').html('');
	});
	}

	/**
	 * Adds an ErrorContainer to the Site (void, possibly hidden, too)
	 */
	errorContainerAdd = function() {

		var html = '<div id="errorContainer"><a class="errorContainerClose" href="#" tabindex="1">Schließen</a><ul></ul></div>';
		$('#header').after(html);
	}

	/**
	 * Adds a successContainer to the Site (void, possibly hidden, too)
	 */
	successContainerAdd = function() {

		var html = '<div id="successContainer"><a class="successContainerClose" href="#" tabindex="1">Schließen</a><ul></ul></div>';

		if($('#messageContainer').length) {
			$('#messageContainer').after(html);
		}
		else if($('#errorContainer').length) {
			$('#errorContainer').after(html);
		}
		else {
			$('#header').after(html);
		}
		$('.successContainerClose').on('click', function(event) {
			event.preventDefault();
			$('#successContainer').hide('highlight', 500);
			$('#successContainer ul').html('');
		});
	}
}

var adminInterface = new AdminInterface();