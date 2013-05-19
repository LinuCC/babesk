/**
 * General functions for use in the Administrator-Subprogram
 */

var AdminInterface = function() {

	/**
	 * Creates a HTML-list-item
	 * @param  {String} str The String that should be wrapped to a list-item
	 * @return {String} HTML-Code containing the List-Item
	 */
	function listItemHtmlCreate(str) {

		res = '<li><p>' + str + '</p></li>';
		return res;
	}

	this.messageShow = function(str) {

		//does the Message-Container exist?
		if(!$('#messageContainer').length) {
			this.messageContainerAdd();
		}

		htmlStr = listItemHtmlCreate(str);
		$('#messageContainer ul').append(htmlStr);
	}

	this.errorShow = function(str) {

		if(!$('#errorContainer').length) {
			this.errorContainerAdd();
		}

		htmlStr = listItemHtmlCreate(str);
		$('#errorContainer ul').append(htmlStr);
	}

	this.successShow = function(str) {

		if(!$('#successContainer').length) {
			this.successContainerAdd();
		}

		htmlStr = listItemHtmlCreate(str);
		$('#successContainer ul').append(htmlStr);
	}

	/**
	 * Adds a MessageContainer to the Site (void, possibly hidden, too)
	 */
	this.messageContainerAdd = function() {

		html = '<div id="messageContainer"><a class="messageContainerClose" href="#">Schließen</a><ul></ul></div>';

		if($('#errorContainer').length) {
			$('#errorContainer').after(html);
		}
		else {
			$('#header').after(html);
		}
	}

	/**
	 * Adds an ErrorContainer to the Site (void, possibly hidden, too)
	 */
	this.errorContainerAdd = function() {

		html = '<div id="errorContainer"><a class="errorContainerClose" href="#">Schließen</a><ul></ul></div>';
		$('#header').after(html);
	}

	/**
	 * Adds a successContainer to the Site (void, possibly hidden, too)
	 */
	this.successContainerAdd = function() {

		html = '<div id="successContainer"><a class="successContainerClose" href="#">Schließen</a><ul></ul></div>';

		if($('#messageContainer').length) {
			$('#messageContainer').after(html);
		}
		else if($('#errorContainer').length) {
			$('#errorContainer').after(html);
		}
		else {
			$('#header').after(html);
		}
	}
}