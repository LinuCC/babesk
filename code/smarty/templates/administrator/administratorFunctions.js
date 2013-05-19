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

		res = '<li>' + str + '</li>';
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
			$(this).errorContainerAdd();
		}

		htmlStr = listItemHtmlCreate(str);
		$('#errorContainer ul').append(str);
	}

	/**
	 * Adds a MessageContainer to the Site (void, possibly hidden, too)
	 */
	this.messageContainerAdd = function() {

		html = '<div id="messageContainer"><ul></ul><a class="messageContainerClose" href="#">Schließen</a></div>';

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

		html = '<div id="errorContainer"><ul></ul><a class="errorContainerClose" href="#">Schließen</a></div>';
		$('#header').after(html);
	}
}