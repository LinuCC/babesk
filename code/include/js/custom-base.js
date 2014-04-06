/*==============================================================
=            Always included helpful functionality             =
==============================================================*/

/**
 * A function to emulate a sprintf-like functionality
 */
if (!String.prototype.format) {
	String.prototype.format = function() {
		var args = arguments;
		return this.replace(/{(\d+)}/g, function(match, number) {
			return typeof args[number] != 'undefined'
				? args[number] : match;
		});
	};
}

jQuery.fn.outerHtml = function() {
	return jQuery('<div />').append(this.eq(0).clone()).html();
};

jQuery.postJSON = function(url, data, callback) {
	$.ajax({
		'type': 'POST',
		'url': url,
		'data': data,
		'success': callback,
		'error': function(res) {
			console.log(res);
			toastr['error']('Konnte die Serverantwort nicht lesen!', 'Fehler');
		},
		'dataType': 'json'
	});
};

toastr.options = {
	'closeButton': true,
	'debug': false,
	'positionClass': 'toast-top-center',
	'onclick': null,
	'showDuration': '300',
	'hideDuration': '1000',
	'timeOut': '5000',
	'extendedTimeOut': '1000',
	'showEasing': 'swing',
	'hideEasing': 'linear',
	'showMethod': 'fadeIn',
	'hideMethod': 'fadeOut'
}

document.cookie='testcookie';
var cookieEnabled = (
	document.cookie.indexOf('testcookie')!=-1
);
if(!cookieEnabled) {
	toastr['error']('Cookies sind nicht aktiviert! Diese Website benötigt Cookies um zu funktionieren.', 'Cookies');
}

$('body').tooltip({
	selector: 'a[rel="tooltip"], [data-toggle="tooltip"]'
});