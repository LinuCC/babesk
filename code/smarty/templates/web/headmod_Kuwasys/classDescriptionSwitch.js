
function displayClassDescription(classDescrId) {
	document.getElementById('classDescription#' + classDescrId).hidden = false;
}

function hideClassDescription(classDescrId) {
	document.getElementById('classDescription#' + classDescrId).hidden = true;
}

function switchClassDescriptionOfLink(classDescrId) {
	if (document.getElementById('classDescription#' + classDescrId).hidden == true) {
		document.getElementById('classDescription#' + classDescrId).hidden = false;
	}
	else {
		document.getElementById('classDescription#' + classDescrId).hidden = true;
	}
}