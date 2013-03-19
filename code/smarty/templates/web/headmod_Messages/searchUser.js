function searchUser(toSearchForInput, outputContainer) {
	var name = document.getElementById(toSearchForInput).value;
	if (window.XMLHttpRequest) {
		xmlhttp = new XMLHttpRequest();
	}
	else {
		xmlhttp = new ActiveXObject("Microsoft.XMLHTTP");
	}
	xmlhttp.onreadystatechange = function() {
		if (xmlhttp.readyState == 4 && xmlhttp.status == 200) {
			document.getElementById(outputContainer).innerHTML = xmlhttp.responseText;
		}
	}
	xmlhttp.open("POST", "?section=Messages|MessageMainMenu&action=searchUserAjax&username="+name,
		true);
	xmlhttp.send();
}

/** Cleans the search-user dialog
 * @param string outputContainer The Container to clear the elements in
 */
function cleanSearchUser(outputContainer) {
	var selection = document.getElementById(outputContainer)
	selection.innerHTML = "";
}
/**
 * @param string form the id of the formular to save the added users in, as an
 * hidden input-field
 * @param string outputContainer The Container to save the list in, which displays the added users
 */
function addUser(ID, name, form, outputContainer) {
	var hiddenInput = document.createElement("input");
	hiddenInput.setAttribute("type", "hidden");
	hiddenInput.setAttribute("value", ID);
	hiddenInput.setAttribute("name", "msgReceiver[]");
	document.getElementById(form).appendChild(hiddenInput);
	var output = document.createElement("li");
	output.innerHTML = name;
	document.getElementById("userSelected").appendChild(output);
	cleanSearchUser();
}