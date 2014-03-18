$(document).ready(function() {
	$('#sidebar-module-selection .sidebar-folder').on('click',
		function(ev) {
		$icon = $(this).children('.toggle-icon');
		$icon.toggleClass('icon-plus');
		$icon.toggleClass('icon-minus');
	});
});