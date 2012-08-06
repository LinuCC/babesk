
function displayClassDescription(classDescrId) {
	document.getElementById('classDescription#' + classDescrId).hidden = false;
	document.getElementById('showClassDescriptionOn#' + classDescrId).hidden = true;
	document.getElementById('showClassDescriptionOff#' + classDescrId).hidden = false;
}

function hideClassDescription(classDescrId) {
	document.getElementById('classDescription#' + classDescrId).hidden = true;
	document.getElementById('showClassDescriptionOn#' + classDescrId).hidden = false;
	document.getElementById('showClassDescriptionOff#' + classDescrId).hidden = true;
}

function switchClassDescriptionOfLink(classDescrId) {
	if (document.getElementById('classDescription#' + classDescrId).hidden == true) {
		document.getElementById('classDescription#' + classDescrId).hidden = false;
	}
	else {
		document.getElementById('classDescription#' + classDescrId).hidden = true;
	}
}