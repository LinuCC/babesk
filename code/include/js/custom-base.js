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

if(!navigator.cookieEnabled) {
	toastr['error']('Cookies sind nicht aktiviert! Diese Website benötigt Cookies um zu funktionieren.', 'Cookies');
}

$(document).ready(function() {
	if(typeof bootbox != undefined && typeof bootbox != "undefined") {
		bootbox.setDefaults({locale: 'de'});
	}
});

(function refreshTooltips() {
	$('body').tooltip({
		selector: 'a[rel="tooltip"], [data-toggle="tooltip"]'
	});
})();

$('body').popover({
	selector: 'a[rel="popover"], [data-toggle="popover"]'
});

// Simple JavaScript Templating
// John Resig - http://ejohn.org/ - MIT Licensed
// Client side template parser that uses &lt;?= #&gt; and &lt;? code ?&gt;
// expressions and ? ? code blocks for template expansion.
// NOTE: chokes on single quotes in the document in some situations
//       use &amp;rsquo; for literals in text and avoid any single quote
//       attribute delimiters.
(function() {
		var cache = {};

		this.microTmpl = function microTmpl(str, data) {
				// Figure out if we're getting a template, or if we need to
				// load the template - and be sure to cache the result.
				var fn = !/\W/.test(str) ?
			cache[str] = cache[str] ||
				microTmpl(document.getElementById(str).innerHTML) :

				// Generate a reusable function that will serve as a template
				// generator (and which will be cached).
			new Function("obj",
				"var p=[],print=function(){p.push.apply(p,arguments);};" +

				// Introduce the data as local variables using with(){}
				"with(obj){p.push('" +

				// Convert the template into pure JavaScript
				str.replace(/[\r\t\n]/g, " ")
					 .replace(/'(?=[^%]*%>)/g,"\t")
					 .split("'").join("\\'")
					 .split("\t").join("'")
					 .replace(/<%=(.+?)%>/g, "',$1,'")
					 .split("<%").join("');")
					 .split("%>").join("p.push('")
					 + "');}return p.join('');");
				// Provide some basic currying to the user
				return data ? fn(data) : fn;
		};
})();