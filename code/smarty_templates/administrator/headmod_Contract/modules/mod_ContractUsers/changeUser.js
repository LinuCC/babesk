function displayChangePassword() {
	
	document.getElementById('pwF').hidden = false;
	document.getElementById('showPw').hidden = true;
	document.getElementById('hidePw').hidden = false;
}

function hideChangePassword() {
	
	document.getElementById('pwF').hidden = true;
	document.getElementById('showPw').hidden = false;
	document.getElementById('hidePw').hidden = true;
	document.getElementById('pw').value = '';
	document.getElementById('pwRep').value = '';
}