/**
 * getElementsByClassName is not supported by older browsers. Define the function so that we can use it anyway
 */
document.getElementsByClassName = function(cl) {
	var retnode = [];
	var myclass = new RegExp('\\b' + cl + '\\b');
	var elem = this.getElementsByTagName('*');
	for ( var i = 0; i < elem.length; i++) {
		var classes = elem[i].className;
		if (myclass.test(classes))
			retnode.push(elem[i]);
	}
	return retnode;
};

function showHelpTextLockedClasses() {
	
	helpelements = document.getElementsByClassName("helpTextLockedClasses");
	for(var i = 0; i < helpelements.length; i++) {
		helpelements [i].hidden = false;
	}
}

function hideHelpTextLockedClasses() {

	helpelements = document.getElementsByClassName("helpTextLockedClasses");
	for(var i = 0; i < helpelements.length; i++) {
		helpelements [i].hidden = true;
	}
}
