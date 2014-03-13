
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

$(document).ready(function() {

	$('[id^=classDescription_]').hide();

	$('.classlistingContainer').hover(
		function(event) {
			$(this).children('.classDescription').stop().show(200);
		},
		function(event) {
			$(this).children('.classDescription').stop().hide(200);
		}
	);
});
